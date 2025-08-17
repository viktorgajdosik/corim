<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class ListingCard extends Component
{
    public Listing $listing;
      public bool $isReady = false;

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
    }

    // just to trigger wire:loading once
    public function ready(): void
    {
      $this->isReady = true;
    }



    public function getIsAuthorProperty(): bool
    {
        return auth()->check() && $this->listing->user_id === auth()->id();
    }

    public function render()
    {
        return view('livewire.listing-card');
    }
}
