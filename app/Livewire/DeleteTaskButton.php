<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Notification; // ⬅️ add
use App\Livewire\ShowManageTasks; // optional, for clarity
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

        // Preload relations/values we need BEFORE deletion
        $this->task->loadMissing('listing');
        $taskId        = $this->task->id;
        $participantId = $this->task->assigned_user_id;
        $taskName      = $this->task->name;
        $listingId     = $this->task->listing_id;
        $listingTitle  = optional($this->task->listing)->title;

        // Delete stored files (assignment + student result if any)
        foreach (array_filter([$this->task->file, $this->task->result_file]) as $path) {
            try { Storage::disk('public')->delete($path); } catch (\Throwable $e) {}
        }

        // 🔔 Notify participant (if any) that the task was deleted
        if ($participantId) {
            Notification::create([
                'user_id' => $participantId,
                'type'    => 'task.deleted',
                'title'   => 'Task deleted',
                'body'    => $listingTitle
                              ? "“{$taskName}” in “{$listingTitle}” was deleted by the author."
                              : "The task “{$taskName}” was deleted by the author.",
                // Send them back to the listing page
                'url'     => route('listings.show', $listingId),
            ]);

            // Let the bell refresh its unseen count
            $this->dispatch('notificationsChanged');
        }

        // Remove from DB
        $this->task->delete();

        // Ask the parent task list to refresh (so the card disappears)
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskDeleted', taskId: $taskId)->to(ShowManageTasks::class);

        // Tell browser to wait for card disappearance, then stop spinner + toast
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
