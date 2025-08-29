<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class ListingManageCard extends Component
{
    public Listing $listing;
    public bool $isReady = false;

    // ⇩ NEW: mirror switch state for Alpine entangle
    public bool $isOpen = false;

    public function getIsAuthorProperty(): bool
    {
        return auth()->check() && $this->listing->user_id === auth()->id();
    }

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
        $this->isOpen  = (bool) $listing->is_open; // keep Alpine in sync initially
    }

    // Used only to trigger wire:loading skeleton on first paint
    public function ready(): void
    {
        $this->isReady = true;
    }

    public function toggleOpen(): void
    {
        abort_unless($this->isAuthor, 403);

        // Flip and persist the model
        $this->listing->is_open = ! $this->listing->is_open;
        $this->listing->save();

        // ⇩ IMPORTANT: reflect back to Livewire property (entangled with Alpine)
        $this->isOpen = (bool) $this->listing->is_open;

        // (Optional) let other components know (browser event name is kebab-cased)
        $this->dispatch('listing-open-state-changed', listingId: $this->listing->id, is_open: $this->isOpen);
    }

    public function render()
    {
        return view('livewire.listing-manage-card');
    }
}
