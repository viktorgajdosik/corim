<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DeleteTaskButton extends Component
{
    public Task $task;

    public function mount(Task $task): void
    {
        $this->task = $task;
    }

    public function delete(): void
    {
        // AuthZ
        if ($this->task->author_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Delete any stored file
        if ($this->task->file) {
            try {
                Storage::disk('public')->delete($this->task->file);
            } catch (\Throwable $e) {
                // Non-fatal; proceed with task deletion anyway
            }
        }

        $taskId = $this->task->id;

        // Remove from DB
        $this->task->delete();

        // Ask the parent task list to refresh (so the card disappears)
        // You already use ShowManageTasks as the wrapper that owns the list
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskDeleted', taskId: $taskId)->to(ShowManageTasks::class);

        // Tell the browser to keep the spinner until the DOM no longer contains this task card,
        // then stop spinner + show toast
        $this->dispatch(
            'taskRemovedDomShouldReflect',
            taskId: $taskId,
            flash: ['message' => 'Task deleted successfully.', 'type' => 'success']
        );
    }

    public function render()
    {
        return view('livewire.delete-task-button');
    }
}
