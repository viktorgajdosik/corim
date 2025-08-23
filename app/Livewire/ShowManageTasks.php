<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;

class ShowManageTasks extends Component
{
    public Listing $listing;

    // local flag for the Create Task card (mirrors task-card pattern)
    public bool $createReady = false;

    protected $listeners = [
        'taskCreated' => '$refresh',
        'taskDeleted' => '$refresh',
        'taskUpdated' => '$refresh',
        'modificationRequested' => '$refresh',
        'taskStatusChanged' => '$refresh',
        'refreshTask' => '$refresh',

        // Ensure this component refreshes when participants change
        'applicationsChanged' => '$refresh',
    ];

    // Triggered by wire:init on the Create Task card wrapper
    public function readyCreate(): void
    {
        $this->createReady = true;
    }

    public function render()
    {
        $this->listing->refresh();

        $tasks = $this->listing->tasks()
            ->with('assignedUser')
            ->latest()
            ->get();

        return view('livewire.show-manage-tasks', ['tasks' => $tasks]);
    }

    // 🔔 Runs after Livewire morphs the DOM for THIS component
    public function rendered()
    {
        // Browser event (Livewire v3): forms/widgets can listen for this
        $this->dispatch('tasksPatchComplete');
    }
}
