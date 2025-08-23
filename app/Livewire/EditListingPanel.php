<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class EditListingPanel extends Component
{
    public Listing $listing;
    public bool $panelReady = false;

    protected $listeners = [
        // child form notifies to re-render after save
        'listingUpdated' => '$refresh',
    ];

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
    }

    // Initial placeholder only
    public function readyPanel(): void
    {
        $this->panelReady = true;
    }

    public function render()
    {
        // keep fresh for updated_at (DOM anchor)
        $this->listing->refresh();

        return view('livewire.edit-listing-panel');
    }
}
