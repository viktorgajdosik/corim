<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class ListingApplicationPanel extends Component
{
    public Listing $listing;

    /** Skeleton reveal flag */
    public bool $panelReady = false;

    protected $listeners = [
        'applicationsChanged' => '$refresh',
    ];

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
    }

    /** Triggered by wire:init to show skeleton first */
    public function readyPanel(): void
    {
        $this->panelReady = true;
    }

    public function render()
    {
        $application = $this->listing->applications()
            ->where('user_id', auth()->id())
            ->first();

        $isAccepted = $application && (int)$application->accepted === 1;

        return view('livewire.listing-application-panel', compact('application', 'isAccepted'));
    }
}
