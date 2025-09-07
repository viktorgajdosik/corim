<?php

namespace App\Livewire\Admin;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Broadcast extends Component
{
    public string $search = '';
    public bool $sendToAll = true;

    /** @var array<int> */
    public array $selectedIds = [];

    // in-app notification fields
    public string $note_title = '';
    public string $note_body  = '';
    public ?string $note_url  = null; // optional deep-link

    // email fields
    public string $email_subject = '';
    public string $email_body    = '';
    public bool $email_verified_only = true;

    protected $queryString = [
        'search'    => ['except' => ''],
        'sendToAll' => ['except' => true],
    ];

    public function updatedSearch() { /* re-render only */ }

    public function getResultsProperty()
    {
        $s = trim($this->search);
        return User::query()
            ->when($s !== '', function ($q) use ($s) {
                $q->where(function ($w) use ($s) {
                    $w->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->orderBy('name')
            ->limit(25)
            ->get(['id','name','email','email_verified_at']);
    }

    public function getSelectedUsersProperty()
    {
        if (empty($this->selectedIds)) return collect();
        return User::query()
            ->whereIn('id', $this->selectedIds)
            ->get(['id','name','email']);
    }

    private function intendedRecipientIds(): array
    {
        if ($this->sendToAll) {
            return User::query()->pluck('id')->all();
        }
        $ids = array_values(array_unique(array_map('intval', $this->selectedIds)));
        return User::query()->whereIn('id', $ids)->pluck('id')->all();
    }

    public function toggleSelectShown(bool $check): void
    {
        $shown = $this->results->pluck('id')->all();
        if ($check) {
            $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $shown)));
        } else {
            $this->selectedIds = array_values(array_diff($this->selectedIds, $shown));
        }
    }

    public function sendNotification(): void
    {
        $this->validate([
            'note_title' => 'required|string|max:120',
            'note_body'  => 'required|string|max:5000',
            'note_url'   => 'nullable|string|max:2000',
            'sendToAll'  => 'boolean',
            'selectedIds'=> 'array'
        ]);

        $ids = $this->intendedRecipientIds();
        if (!$ids) {
            $this->addError('selectedIds', 'Choose at least one recipient or select “Send to all”.');
            return;
        }

        foreach ($ids as $uid) {
            Notification::create([
                'user_id' => $uid,
                'type'    => 'admin.broadcast',
                'title'   => $this->note_title,
                'body'    => $this->note_body,
                'url'     => $this->note_url ?: url('/'),
            ]);
        }

        // Optional: nudge clients that observe notifications
        $this->dispatch('notificationsChanged');

        session()->flash('message', 'Notification sent to '.count($ids).' user(s).');

        // Keep the text; clear selection
        if (!$this->sendToAll) $this->selectedIds = [];
    }

    public function sendEmail(): void
    {
        $this->validate([
            'email_subject' => 'required|string|max:200',
            'email_body'    => 'required|string|max:10000',
            'email_verified_only' => 'boolean',
        ]);

        $ids = $this->intendedRecipientIds();
        if (!$ids) {
            $this->addError('selectedIds', 'Choose at least one recipient or select “Send to all”.');
            return;
        }

        $recips = User::query()
            ->whereIn('id', $ids)
            ->when($this->email_verified_only, fn($q) => $q->whereNotNull('email_verified_at'))
            ->get(['id','name','email']);

        foreach ($recips as $u) {
            // Simple inline email (no custom Mailable needed)
            Mail::raw($this->email_body, function ($m) use ($u) {
                $m->to($u->email, $u->name)
                  ->subject($this->email_subject);
            });
        }

        session()->flash('message', 'Email sent to '.$recips->count().' user(s).');

        if (!$this->sendToAll) $this->selectedIds = [];
    }

    public function render()
    {
        return view('livewire.admin.broadcast', [
            'results' => $this->results,
            'selectedUsers' => $this->selectedUsers,
        ])->layout('layouts.admin')->title('Admin · Broadcast');
    }
}
