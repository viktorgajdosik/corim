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

    // initial paint / skeleton
    public bool $isReady = false;
    public function ready(): void
    {
        $this->isReady = true;
    }

    // input state
    public string $body = '';
    public bool $sendToAll = true;
    /** @var array<int> */
    public array $recipientIds = [];

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
        $user = auth()->user();

        $acceptedIds = Application::query()
            ->where('listing_id', $listing->id)
            ->where('accepted', true)
            ->pluck('user_id')
            ->all();

        $audienceIds = collect($acceptedIds)
            ->push($listing->user_id)   // author
            ->unique()
            ->values();

        // participant must be accepted OR be the author
        abort_unless($audienceIds->contains($user->id), 403);

        $this->audience = User::query()
            ->whereIn('id', $audienceIds->all())
            ->orderBy('name')
            ->get(['id','name'])
            ->map(fn(User $u) => ['id' => $u->id, 'name' => $u->name])
            ->values()
            ->all();
    }

    public function getMessagesProperty()
    {
        $uid = auth()->id();

        return ChatMessage::query()
            ->with(['sender:id,name'])
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
        // no-op: re-render via Livewire polling
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
    }

    public function send(): void
    {
        $this->validate([
            'body'         => 'required|string|min:1|max:5000',
            'sendToAll'    => 'boolean',
            'recipientIds' => 'array',
            'recipientIds.*' => 'integer',
        ]);

        $senderId = auth()->id();

        // Allowed audience (author + accepted)
        $allowedIds = collect($this->audience)->pluck('id');

        // Compute recipients
        $isBroadcast = $this->sendToAll;
        if ($isBroadcast) {
            $targets = $allowedIds
                ->reject(fn ($id) => $id === $senderId)
                ->values();
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

        // Persist message
        $msg = ChatMessage::create([
            'listing_id'   => $this->listing->id,
            'user_id'      => $senderId,
            'body'         => trim($this->body),
            'is_broadcast' => $isBroadcast,
        ]);

        if (! $isBroadcast) {
            $msg->recipients()->attach($targets->all());
        }

        // Notifications per recipient
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

        // Reset input, ask browser to scroll, and refresh
        $this->reset('body');
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
