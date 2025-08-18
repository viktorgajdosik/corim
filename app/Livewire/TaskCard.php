<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Listing;
use Livewire\Component;

class TaskCard extends Component
{
    public Task $task;
    public Listing $listing;

    public bool $isReady = false;

    protected $listeners = [
        'taskDeleted' => '$refresh',
    ];

    // Triggered by wire:init on first paint
    public function ready(): void
    {
        // $this->task->loadMissing(['assignedUser']); // (optional) preload relations
        $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.task-card');
    }
}
