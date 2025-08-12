<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;

class ShowStudentTasks extends Component
{
    public Listing $listing;

    protected $listeners = [
        '$refresh'         => '$refresh',
        'studentSubmitted' => '$refresh',  // optional convenience
        'refreshTask'      => '$refresh',
    ];

    public function render()
    {
        // Reload listing + fetch ONLY tasks assigned to the current user
        $this->listing->refresh();

        $tasks = $this->listing->tasks()
            ->where('assigned_user_id', auth()->id())
            ->with('assignedUser')
            ->latest()
            ->get();

        return view('livewire.show-student-tasks', [
            'tasks' => $tasks,
        ]);
    }
}
