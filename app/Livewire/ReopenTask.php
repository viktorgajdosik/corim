<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Livewire\ShowManageTasks;

class ReopenTask extends Component
{
    public Task $task;

    public function reopen()
    {
        if ($this->task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Back to 'submitted' if there are results, else 'assigned'
        $this->task->status = ($this->task->result_text || $this->task->result_file) ? 'submitted' : 'assigned';
        $this->task->modification_note = null;
        $this->task->save();

        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Task reopened.', 'type' => 'success'];

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
        return view('livewire.reopen-task');
    }
}
