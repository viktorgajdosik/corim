<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Notification; // 🔔
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

        // 🔔 notify participant
        Notification::create([
            'user_id' => $this->task->assigned_user_id,
            'type'    => 'task.finished',
            'title'   => 'Task marked as finished',
            'body'    => "“{$this->task->name}” has been marked as finished.",
            'url'     => route('listings.show', $this->task->listing_id),
        ]);
        $this->dispatch('notificationsChanged');

        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Task finished.', 'type' => 'success'];

        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskStatusChanged')->to(ShowManageTasks::class);

        $this->dispatch('taskDomShouldReflect', taskId: $this->task->id, updatedAt: $expectedTs, flash: $flash);
    }

    public function render()
    {
        return view('livewire.mark-task-as-finished');
    }
}
