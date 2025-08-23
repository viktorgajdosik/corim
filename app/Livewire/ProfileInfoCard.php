<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ProfileInfoCard extends Component
{
    public User $user;
    public bool $isReady = false;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    // Triggered by wire:init to show skeleton on first paint
    public function ready(): void
    {
        $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.profile-info-card');
    }
}

