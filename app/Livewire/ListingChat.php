<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\ChatMessage;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class ListingChat extends Component
{
    public Listing $listing;

    // initial paint / gating (optional)
    public bool $isReady = false;
    public function ready(): void
    {
        $this->isReady = true;
        $this->dispatch('chat:scrollBottom');
    }

    // NEW: pause polling while any dropdown menu is open
    public bool $pollPaused = false;

    // input state
    public string $body = '';
    public bool $sendToAll = true;
    /** @var array<int> */
    public array $recipientIds = [];

    // edit state
    public ?int $editingId = null;

    // pagination-ish
    public int $perPage = 50;

    // derived UI state
    /** @var array<int, array{id:int,name:string}> */
    public array $audience = []; // [{id,name}, ...]

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;

        // Gate: author or accepted participant only
        abort_unless(auth()->check(), 403);
        $userId = auth()->id();

        $acceptedIds = Application::query()
            ->where('listing_id', $listing->id)
            ->where('accepted', true)
            ->pluck('user_id');

        $audienceIds = $acceptedIds
            ->push($listing->user_id)   // author
            ->unique()
            ->values();

        abort_unless($audienceIds->contains($userId), 403);

        $this->refreshAudience();
    }

    protected function refreshAudience(): void
    {
        $acceptedIds = Application::query()
            ->where('listing_id', $this->listing->id)
            ->where('accepted', true)
            ->pluck('user_id');

        $audienceIds = $acceptedIds
            ->push($this->listing->user_id)
            ->unique()
            ->values();

        $this->audience = User::query()
            ->whereIn('id', $audienceIds->all())
            ->orderBy('name')
            ->get(['id','name'])
            ->map(fn(User $u) => ['id' => $u->id, 'name' => $u->name])
            ->values()
            ->all();

        // prune selected recipients if audience changed
        $this->recipientIds = collect($this->recipientIds)
            ->map(fn ($v) => (int) $v)
            ->intersect($audienceIds)
            ->values()
            ->all();
    }

    public function getMessagesProperty()
    {
        $uid = auth()->id();

        return ChatMessage::query()
            ->with(['sender:id,name','recipients:id,name'])
            ->where('listing_id', $this->listing->id)
            ->where(function (Builder $q) use ($uid) {
                $q->where('is_broadcast', true)
                  ->orWhere('user_id', $uid)
                  ->orWhereHas('recipients', fn(Builder $r) => $r->where('users.id', $uid));
            })
            ->orderBy('created_at', 'asc')
            ->limit($this->perPage)
            ->get();
    }

    #[On('refreshChat')]
    public function refreshChat(): void
    {
        // 🔒 Do nothing while dropdowns are open (or during edit)
        if ($this->pollPaused || $this->editingId) return;

        $this->refreshAudience();
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
    }

    protected function latestMessageId(): ?int
    {
        return ChatMessage::query()
            ->where('listing_id', $this->listing->id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->value('id');
    }

    // ---- SEND (or SAVE if in edit) ----
    public function send(): void
    {
        if ($this->editingId) { $this->saveEdit(); return; }

        $this->validate([
            'body'           => 'required|string|min:1|max:5000',
            'sendToAll'      => 'boolean',
            'recipientIds'   => 'array',
            'recipientIds.*' => 'integer',
        ]);

        $senderId = auth()->id();

        $this->refreshAudience();
        $allowedIds = collect($this->audience)->pluck('id');

        $isBroadcast = $this->sendToAll;
        if ($isBroadcast) {
            $targets = $allowedIds->reject(fn ($id) => $id === $senderId)->values();
        } else {
            $targets = collect($this->recipientIds)
                ->map(fn ($v) => (int) $v)
                ->intersect($allowedIds)
                ->reject(fn ($id) => $id === $senderId)
                ->values();

            if ($targets->isEmpty()) {
                $this->addError('recipientIds', 'Choose at least one recipient or send to all.');
                return;
            }
        }

        $msg = ChatMessage::create([
            'listing_id'   => $this->listing->id,
            'user_id'      => $senderId,
            'body'         => trim($this->body),
            'is_broadcast' => $isBroadcast,
        ]);

        if (!$isBroadcast) {
            $msg->recipients()->attach($targets->all());
        }

        foreach ($targets as $rid) {
            Notification::create([
                'user_id' => $rid,
                'type'    => 'chat.message',
                'title'   => 'New message',
                'body'    => Str::limit(strip_tags($this->body), 140),
                'url'     => route('listings.show', $this->listing->id),
            ]);
        }
        $this->dispatch('notificationsChanged');

        $this->reset('body');
        $this->dispatch('chat:scrollBottom');
        $this->dispatch('refreshChat')->self();
    }

    // ---- EDIT FLOW ----
    public function startEdit(int $id): void
    {
        $m = ChatMessage::query()
            ->with('recipients:id')
            ->whereKey($id)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        abort_unless($m->user_id === auth()->id(), 403);

        $latestId = $this->latestMessageId();
        abort_unless($latestId && $m->id === $latestId, 403);

        $this->editingId = $m->id;
        $this->body = $m->body;

        // Prime recipient controls based on the existing message
        $this->sendToAll = (bool) $m->is_broadcast;
        $this->recipientIds = $m->is_broadcast
            ? []
            : $m->recipients->pluck('id')->map(fn($v)=>(int)$v)->values()->all();

        $this->dispatch('chat:focusInput');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->body = '';
        // optional reset to defaults
        $this->sendToAll = true;
        $this->recipientIds = [];
    }

    public function saveEdit(): void
    {
        abort_if(!$this->editingId, 400);

        $this->validate([
            'body'           => 'required|string|min:1|max:5000',
            'sendToAll'      => 'boolean',
            'recipientIds'   => 'array',
            'recipientIds.*' => 'integer',
        ]);

        $m = ChatMessage::query()
            ->whereKey($this->editingId)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        abort_unless($m->user_id === auth()->id(), 403);

        $latestId = $this->latestMessageId();
        abort_unless($latestId && $m->id === $latestId, 403);

        // Re-validate audience
        $this->refreshAudience();
        $allowedIds = collect($this->audience)->pluck('id');
        $senderId = auth()->id();

        $isBroadcast = (bool) $this->sendToAll;
        if ($isBroadcast) {
            $targets = $allowedIds->reject(fn ($id) => $id === $senderId)->values();
        } else {
            $targets = collect($this->recipientIds)
                ->map(fn ($v) => (int) $v)
                ->intersect($allowedIds)
                ->reject(fn ($id) => $id === $senderId)
                ->values();

            if ($targets->isEmpty()) {
                $this->addError('recipientIds', 'Choose at least one recipient or send to all.');
                return;
            }
        }

        // Apply updates
        $m->body = trim($this->body);
        $m->is_broadcast = $isBroadcast;
        $m->save();

        // Sync recipients
        if ($isBroadcast) {
            $m->recipients()->sync([]); // none for broadcast
        } else {
            $m->recipients()->sync($targets->all());
        }

        // exit edit mode
        $this->editingId = null;
        $this->body = '';
        $this->sendToAll = true;
        $this->recipientIds = [];

        $this->dispatch('chat:scrollBottom');
        $this->dispatch('refreshChat')->self();
    }

    // ---- DELETE ----
    public function deleteMessage(int $id): void
    {
        $m = ChatMessage::query()
            ->whereKey($id)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        abort_unless($m->user_id === auth()->id(), 403);

        $latestId = $this->latestMessageId();
        abort_unless($latestId && $m->id === $latestId, 403);

        try { $m->recipients()->detach(); } catch (\Throwable $e) {}
        $m->delete();

        if ($this->editingId === $id) {
            $this->editingId = null;
            $this->body = '';
            $this->sendToAll = true;
            $this->recipientIds = [];
        }

        $this->dispatch('chat:scrollBottom');
        $this->dispatch('refreshChat')->self();
    }

    public function render()
    {
        $messages = $this->messages;
        $lastVisibleId = $messages->isNotEmpty() ? $messages->last()->id : null;

        return view('livewire.listing-chat', [
            'messages'      => $messages,
            'lastVisibleId' => $lastVisibleId,
        ]);
    }
}
