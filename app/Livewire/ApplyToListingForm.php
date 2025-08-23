<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\Application; // adjust if your namespace differs
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApplyToListingForm extends Component
{
    public Listing $listing;
    public string $message = '';

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
    }

    protected function rules(): array
    {
        return [
            'message' => 'required|string|min:5|max:2000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        // If already accepted, don't overwrite to pending; just flash
        $existing = $this->listing->applications()
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && (int)$existing->accepted === 1) {
            $this->dispatch(
                'applicationDomShouldReflect',
                listingId: $this->listing->id,
                state: 'accepted',
                flash: ['message' => 'You are already accepted for this listing.', 'type' => 'info']
            );
            return;
        }

        // Upsert to PENDING (accepted = 0) to satisfy NOT NULL constraint
        $this->listing->applications()
            ->updateOrCreate(
                ['user_id' => Auth::id()],
                ['message' => $this->message, 'accepted' => 0]
            );

        // Refresh the panel (switch to "awaiting")
        $this->dispatch('$refresh')->to(ListingApplicationPanel::class);
        $this->dispatch('applicationsChanged')->to(ListingApplicationPanel::class);

        // Wait for 'awaiting' DOM, then stop spinner + toast
        $this->dispatch(
            'applicationDomShouldReflect',
            listingId: $this->listing->id,
            state: 'awaiting',
            flash: ['message' => 'Application submitted successfully.', 'type' => 'success']
        );

        // Optional: clear
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.apply-to-listing-form');
    }
}
