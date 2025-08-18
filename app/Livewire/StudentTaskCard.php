<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Listing;

class StudentTaskCard extends Component
{
    public Task $task;
    public Listing $listing;
    public bool $isReady = false;

    protected $listeners = [
        'refreshTask' => '$refresh',
        'studentSubmitted' => '$refresh',
    ];

    public function ready(): void
    {
        $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.student-task-card');
    }
}
