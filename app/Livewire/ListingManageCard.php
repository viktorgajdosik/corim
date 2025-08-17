<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;


class ListingManageCard extends Component
{
    public Listing $listing;
      public bool $isReady = false;

    public function getIsAuthorProperty()
{
    return auth()->check() && $this->listing->user_id === auth()->id();
}

    public function mount(Listing $listing)
    {
        $this->listing = $listing;
    }

    // Used only to trigger wire:loading on first paint
    public function ready(): void
    {
            $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.listing-manage-card');
    }
}
