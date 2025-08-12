<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Livewire\ShowManageTasks;

class MarkTaskAsFinished extends Component
{
    public Task $task;

    public function markAsFinished()
    {
        if ($this->task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($this->task->status, ['assigned', 'submitted', 'modification_requested'])) {
            session()->flash('error', 'This task cannot be marked as finished.');
            return;
        }

        $this->task->status = 'finished';
        $this->task->modification_note = null;
        $this->task->save();

        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Task finished.', 'type' => 'success'];

        // Re-render parent task list
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskStatusChanged')->to(ShowManageTasks::class);

        // Tell the browser which DOM version to wait for
        $this->dispatch('taskDomShouldReflect', taskId: $this->task->id, updatedAt: $expectedTs);

        // Send flash info along with DOM wait instruction
    $this->dispatch(
        'taskDomShouldReflect',
        taskId: $this->task->id,
        updatedAt: $expectedTs,
        flash: $flash
    );

    }

    public function render()
    {
        return view('livewire.mark-task-as-finished');
    }
}


