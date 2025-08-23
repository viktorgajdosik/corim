<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;

class EditProfilePanel extends Component
{
    public User $user;
    public bool $panelReady = false;

    protected $listeners = [
        'profileUpdated' => '$refresh',
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
        abort_if(!$this->user, 403);
    }

    // Initial placeholder only
    public function readyPanel(): void
    {
        $this->panelReady = true;
    }

    public function render()
    {
        // keep fresh for updated_at (DOM anchor)
        $this->user->refresh();

        return view('livewire.edit-profile-panel');
    }
}
