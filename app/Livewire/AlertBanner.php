<?php

namespace App\Livewire;

use Livewire\Component;

class AlertBanner extends Component
{
    public bool $show = false;

    public function mount()
    {
        // Show only if user is logged in and unverified
        $this->show = auth()->check() && !auth()->user()->hasVerifiedEmail();
    }

    public function render()
    {
        return view('livewire.alert-banner');
    }
}

