<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Listing;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class TaskCard extends Component
{
    public Task $task;
    public Listing $listing;

    public bool $isReady = false;

    protected $listeners = [
        'taskDeleted' => '$refresh',
    ];

    /** Triggered by wire:init on first paint */
    public function ready(): void
    {
        $this->isReady = true;
    }

    /** Livewire delete action (replaces the old controller form) */
    public function deleteTask(): void
    {
        // Authorization: only author can delete
        if ($this->task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Remove file if present
        if ($this->task->file) {
            Storage::disk('public')->delete($this->task->file);
        }

        $taskId = $this->task->id;

        // Delete task
        $this->task->delete();

        // Ask parent list to refresh (so the card is removed)
        // If your list component is ShowManageTasks, keep this:
        $this->dispatch('$refresh')->to(ShowManageTasks::class);

        // Tell browser to wait until the TASK CARD disappears, then stop spinner + toast
        $this->dispatch(
            'taskDomShouldReflect',
            taskId: $taskId,
            action: 'delete',
            updatedAt: null,
            flash: ['message' => 'Task deleted.', 'type' => 'success']
        );
    }

    public function render()
    {
        return view('livewire.task-card');
    }
}
