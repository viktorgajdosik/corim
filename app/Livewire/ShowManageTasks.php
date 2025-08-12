<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;

class ShowManageTasks extends Component
{
    public Listing $listing;

    protected $listeners = [
        'taskCreated' => '$refresh',
        'taskDeleted' => '$refresh',
        'taskUpdated' => '$refresh',
        'modificationRequested' => '$refresh',
        'taskStatusChanged' => '$refresh',
        'refreshTask' => '$refresh',
    ];

    public function render()
    {
        $this->listing->refresh();

        $tasks = $this->listing->tasks()
            ->with('assignedUser')
            ->latest()
            ->get();

        return view('livewire.show-manage-tasks', ['tasks' => $tasks]);
    }

    // 🔔 This runs after Livewire finishes morphing the DOM for THIS component
    public function rendered()
    {
        // Browser event (Livewire v3): listen to this in your Alpine forms
     $this->dispatch('tasksPatchComplete');
    }
}
