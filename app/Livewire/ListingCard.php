<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class ListingCard extends Component
{
    public Listing $listing;
    public bool $isReady = false;

    /**
     * Optional status label for this card.
     * Example: "applied" (used on the profile page to mark pending apps)
     */
    public ?string $status = null;

    public function mount(Listing $listing, ?string $status = null): void
    {
        $this->listing = $listing;
        $this->status  = $status;
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
