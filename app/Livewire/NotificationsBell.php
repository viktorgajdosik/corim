<?php

namespace App\Livewire\Nav;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsBell extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('notificationsChanged')]
    public function refreshCount(): void
    {
        $uid = Auth::id();
        $this->count = $uid
            ? Notification::where('user_id', $uid)->unseen()->count()
            : 0;
    }

    public function render()
    {
        return view('livewire.notifications-bell');
    }
}

