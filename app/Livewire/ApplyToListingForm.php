<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

        $userId     = Auth::id();
        $authorId   = (int) $this->listing->user_id;
        $old        = $this->listing->applications()->where('user_id', $userId)->first();
        $oldMessage = $old?->message;

        // If already accepted, don’t flip it back to pending
        if ($old && (int) $old->accepted === 1) {
            $this->dispatch(
                'applicationDomShouldReflect',
                listingId: $this->listing->id,
                state: 'accepted',
                flash: ['message' => 'You are already accepted for this listing.', 'type' => 'info']
            );
            return;
        }

        // Upsert as PENDING
        $this->listing->applications()->updateOrCreate(
            ['user_id' => $userId],
            ['message' => $this->message, 'accepted' => 0]
        );

        // ===== NOTIFY THE AUTHOR =====
        // Don’t notify yourself (in case author applies to own listing somehow)
        if ($authorId && $authorId !== $userId) {
            $type = $old ? (
                $oldMessage !== $this->message ? 'application.updated' : null
            ) : 'application.new';

            if ($type) {
                Notification::create([
                    'user_id' => $authorId,
                    'type'    => $type,
                    'title'   => 'New application · ' . Str::limit($this->listing->title, 60),
                    'body'    => Str::limit(
                        (Auth::user()?->name ?? 'A user') .
                        ' applied' . ($type === 'application.updated' ? ' (updated message)' : '') .
                        ': ' . $this->message,
                        200
                    ),
                    'url'     => route('listings.show-manage', $this->listing->id) . '#applications-participants',
                ]);

                // If the author is on a page with the bell component, it can refresh
                $this->dispatch('notificationsChanged');
            }
        }

        // Re-render the panel into "awaiting" state
        $this->dispatch('$refresh')->to(ListingApplicationPanel::class);
        $this->dispatch('applicationsChanged')->to(ListingApplicationPanel::class);

        // Tell the browser to stop the spinner once the "awaiting" DOM appears
        $this->dispatch(
            'applicationDomShouldReflect',
            listingId: $this->listing->id,
            state: 'awaiting',
            flash: ['message' => 'Application submitted successfully.', 'type' => 'success']
        );

        // Clear textarea
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.apply-to-listing-form');
    }
}
