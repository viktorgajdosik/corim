<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Listing;
use Livewire\Component;

class TaskCard extends Component
{
    public Task $task;
    public Listing $listing;

    protected $listeners = [
        'taskDeleted' => '$refresh',
    ];

        public function ready(): void
    {
        // $this->task->loadMissing(['assignedUser']);
        $this->ready = true;
    }

    public function render()
    {
        return view('livewire.task-card');
    }
}
