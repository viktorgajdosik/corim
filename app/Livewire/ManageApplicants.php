<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;
use App\Models\Application;

class ManageApplicants extends Component
{
    public Listing $listing;

    public bool $isReady = false;

    protected $listeners = [
        'applicationsChanged' => '$refresh',
    ];

    public function mount(Listing $listing): void
    {
        $this->listing->load(['applications.user']);
    }

    public function ready(): void
    {
        $this->isReady = true;
    }

    public function accept(int $applicationId): void
    {
        $app = Application::with('listing')
            ->where('id', $applicationId)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        if ($app->listing->user_id !== auth()->id()) abort(403);

        $app->accepted = true;
        $app->save();
        $expectedTs = $app->updated_at->getTimestamp();

        // Move it from Applications -> Participants
        $this->listing->load(['applications.user']);

        // Tell browser to wait for the NEW participant card, then stop spinner + remove one ghost via app.js
        $this->dispatch(
            'appDomShouldReflect',
            appId: $app->id,
            action: 'accept',
            updatedAt: $expectedTs,
            listingId: $this->listing->id, // <-- important
            flash: ['message' => 'Application accepted.', 'type' => 'success']
        );

       $this->dispatch('applicationsChanged')->to(\App\Livewire\ShowManageTasks::class);
    }

    public function deny(int $applicationId): void
    {
        $app = Application::with('listing')
            ->where('id', $applicationId)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        if ($app->listing->user_id !== auth()->id()) abort(403);

        $id = $app->id;
        $app->delete();

        $this->listing->load(['applications.user']);

        $this->dispatch(
            'appDomShouldReflect',
            appId: $id,
            action: 'deny',
            updatedAt: null,
            listingId: $this->listing->id,
            flash: ['message' => 'Application denied.', 'type' => 'success']
        );

       $this->dispatch('applicationsChanged')->to(\App\Livewire\ShowManageTasks::class);
    }

    public function remove(int $applicationId): void
    {
        $app = Application::with('listing')
            ->where('id', $applicationId)
            ->where('listing_id', $this->listing->id)
            ->where('accepted', true)
            ->firstOrFail();

        if ($app->listing->user_id !== auth()->id()) abort(403);

        $id = $app->id;
        $app->delete();

        $this->listing->load(['applications.user']);

        $this->dispatch(
            'appDomShouldReflect',
            appId: $id,
            action: 'remove',
            updatedAt: null,
            listingId: $this->listing->id,
            flash: ['message' => 'Participant removed.', 'type' => 'success']
        );

        $this->dispatch('applicationsChanged')->to(\App\Livewire\ShowManageTasks::class);
    }

    public function getPendingProperty()
    {
        return $this->listing->applications->where('accepted', false)->values();
    }

    public function getParticipantsProperty()
    {
        return $this->listing->applications->where('accepted', true)->values();
    }

    public function render()
    {
        return view('livewire.manage-applicants');
    }
}
