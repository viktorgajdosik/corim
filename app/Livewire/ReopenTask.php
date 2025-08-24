<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Notification; // 🔔
use App\Livewire\ShowManageTasks;

class ReopenTask extends Component
{
    public Task $task;

    public function reopen()
    {
        if ($this->task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $this->task->status = ($this->task->result_text || $this->task->result_file) ? 'submitted' : 'assigned';
        $this->task->modification_note = null;
        $this->task->save();

        // 🔔 notify participant
        Notification::create([
            'user_id' => $this->task->assigned_user_id,
            'type'    => 'task.reopened',
            'title'   => 'Task reopened',
            'body'    => "“{$this->task->name}” has been reopened.",
            'url'     => route('listings.show', $this->task->listing_id),
        ]);
        $this->dispatch('notificationsChanged');

        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Task reopened.', 'type' => 'success'];

        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskStatusChanged')->to(ShowManageTasks::class);

        $this->dispatch('taskDomShouldReflect', taskId: $this->task->id, updatedAt: $expectedTs, flash: $flash);
    }

    public function render()
    {
        return view('livewire.reopen-task');
    }
}
