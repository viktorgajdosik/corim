<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;
use App\Models\Application;
use App\Models\Notification;

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
        $app = Application::with(['listing', 'user'])
            ->where('id', $applicationId)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        if ($app->listing->user_id !== auth()->id()) abort(403);

        $app->accepted = true;
        $app->save();
        $expectedTs = $app->updated_at->getTimestamp();

        // 🔔 notify participant
        Notification::create([
            'user_id' => $app->user_id,
            'type'    => 'application.accepted',
            'title'   => 'You’ve been accepted',
            'body'    => "Your application to “{$app->listing->title}” was accepted.",
            'url'     => route('listings.show', $app->listing_id),
        ]);
        $this->dispatch('notificationsChanged');

        $this->listing->load(['applications.user']);

        $this->dispatch(
            'appDomShouldReflect',
            appId: $app->id,
            action: 'accept',
            updatedAt: $expectedTs,
            listingId: $this->listing->id,
            flash: ['message' => 'Application accepted.', 'type' => 'success']
        );

        $this->dispatch('applicationsChanged')->to(\App\Livewire\ShowManageTasks::class);
    }

    public function deny(int $applicationId): void
    {
        $app = Application::with(['listing', 'user'])
            ->where('id', $applicationId)
            ->where('listing_id', $this->listing->id)
            ->firstOrFail();

        if ($app->listing->user_id !== auth()->id()) abort(403);

        // 🔔 notify participant BEFORE delete
        Notification::create([
            'user_id' => $app->user_id,
            'type'    => 'application.denied',
            'title'   => 'Application denied',
            'body'    => "Your application to “{$app->listing->title}” was denied.",
            'url'     => route('listings.show', $app->listing_id),
        ]);
        $this->dispatch('notificationsChanged');

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
        $app = Application::with(['listing', 'user'])
            ->where('id', $applicationId)
            ->where('listing_id', $this->listing->id)
            ->where('accepted', true)
            ->firstOrFail();

        if ($app->listing->user_id !== auth()->id()) abort(403);

        // 🔔 notify participant BEFORE delete
        Notification::create([
            'user_id' => $app->user_id,
            'type'    => 'participant.removed',
            'title'   => 'Removed from project',
            'body'    => "You were removed from “{$app->listing->title}”.",
            'url'     => route('listings.show', $app->listing_id),
        ]);
        $this->dispatch('notificationsChanged');

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
