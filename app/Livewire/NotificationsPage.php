<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsPage extends Component
{
    public bool $isReady = false;

    protected $listeners = [
        'notificationsChanged' => '$refresh',
    ];

    public function ready(): void
    {
        $this->isReady = true;
    }

    public function markSeenAndGo(int $id)
    {
        $notif = Notification::where('user_id', Auth::id())->findOrFail($id);

        if (is_null($notif->seen_at)) {
            $notif->seen_at = now();
            $notif->save();
        }

        // Ask the bell to refresh its badge
        $this->dispatch('notificationsChanged')->to(\App\Livewire\nav\NotificationsBell::class);

        // Safe redirect – we expect relative internal paths like /listings/5
        $target = $notif->url ?: route('notifications.index');
        return $this->redirect($target, navigate: true);
    }

    public function render()
    {
        $items = Notification::where('user_id', Auth::id())
            ->latest()
            ->limit(100)
            ->get();

        return view('livewire.notifications-page', [
            'notifications' => $items,
        ]);
    }
}
