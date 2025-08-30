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
    public function ready(): void { $this->isReady = true; }

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
        $this->refreshAudience();
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
    }

    /** SEND (or SAVE if editing) */
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

    /** EDIT FLOW */
    public function startEdit(int $id): void
    {
        $m = ChatMessage::query()
            ->whereKey($id)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        abort_unless($m->user_id === auth()->id(), 403);

        $this->editingId = $m->id;
        $this->body = $m->body;

        // focus input on client
        $this->dispatch('chat:focusInput');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->body = '';
    }

    public function saveEdit(): void
    {
        abort_if(!$this->editingId, 400);

        $this->validate([
            'body' => 'required|string|min:1|max:5000',
        ]);

        $m = ChatMessage::query()
            ->whereKey($this->editingId)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        abort_unless($m->user_id === auth()->id(), 403);

        $m->body = trim($this->body);
        $m->save();

        $this->editingId = null;
        $this->body = '';

        $this->dispatch('chat:scrollBottom');
        $this->dispatch('refreshChat')->self();
    }

    /** DELETE */
    public function deleteMessage(int $id): void
    {
        $m = ChatMessage::query()
            ->whereKey($id)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        abort_unless($m->user_id === auth()->id(), 403);

        try { $m->recipients()->detach(); } catch (\Throwable $e) {}
        $m->delete();

        if ($this->editingId === $id) {
            $this->editingId = null;
            $this->body = '';
        }

        $this->dispatch('chat:scrollBottom');
        $this->dispatch('refreshChat')->self();
    }

    public function render()
    {
        return view('livewire.listing-chat', [
            'messages' => $this->messages,
        ]);
    }
}
