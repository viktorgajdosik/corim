<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
use App\Models\Notification; // 🔔
use App\Livewire\ShowManageTasks;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UpdateTaskForm extends Component
{
    use WithFileUploads;

    public $task;
    public $task_name;
    public $task_details;
    public $deadline;
    public $assigned_user_id;
    public $task_file;

    public function mount(Task $task)
    {
        $this->task = $task->loadMissing('listing'); // ensure listing for notification text
        $this->resetForm();
    }

    protected function rules()
    {
      return [
        'task_name'        => 'required|string|max:255',
        'task_details'     => 'required|string',
        'deadline'         => 'nullable|date',
        'assigned_user_id' => 'required|exists:users,id',
        'task_file'        => 'nullable|file|max:10240',
      ];
    }

    public function update()
    {
        $this->validate();

        $previousAssignee = $this->task->assigned_user_id;

        // apply updates
        $this->task->name             = $this->task_name;
        $this->task->description      = $this->task_details;
        $this->task->deadline         = $this->deadline;
        $this->task->assigned_user_id = $this->assigned_user_id;

        if ($this->task_file) {
            if ($this->task->file) {
                Storage::disk('public')->delete($this->task->file);
            }
            $this->task->file = $this->storeWithOriginalName($this->task_file, "task_assignments/{$this->task->id}");
        }

        $this->task->save();
        $this->task->refresh()->loadMissing('listing');

        $assigneeChanged = ((int) $previousAssignee) !== ((int) $this->task->assigned_user_id);

        // 🔔 If reassigned: notify BOTH sides
        if ($assigneeChanged) {
            // notify previous assignee (if existed)
            if ($previousAssignee) {
                Notification::create([
                    'user_id' => $previousAssignee,
                    'type'    => 'task.reassigned.from',
                    'title'   => 'You were unassigned from a task',
                    'body'    => "“{$this->task->name}” in “{$this->task->listing->title}”.",
                    'url'     => route('listings.show', $this->task->listing_id),
                ]);
            }

            // notify new assignee
            Notification::create([
                'user_id' => $this->task->assigned_user_id,
                'type'    => 'task.assigned',
                'title'   => 'New task assigned',
                'body'    => "“{$this->task->name}” in “{$this->task->listing->title}”.",
                'url'     => route('listings.show', $this->task->listing_id),
            ]);
        }

        // 🔔 Always notify the current assignee that the task was updated
        Notification::create([
            'user_id' => $this->task->assigned_user_id,
            'type'    => 'task.updated',
            'title'   => 'Task updated',
            'body'    => "“{$this->task->name}” was updated.",
            'url'     => route('listings.show', $this->task->listing_id),
        ]);

        // refresh bell count
        $this->dispatch('notificationsChanged');

        // UI refresh + toast
        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Task updated.', 'type' => 'success'];

        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskUpdated')->to(ShowManageTasks::class);

        $this->dispatch('taskDomShouldReflect', taskId: $this->task->id, updatedAt: $expectedTs, flash: $flash);
    }

    protected function storeWithOriginalName(UploadedFile $file, string $baseDir): string
    {
        $origName = $file->getClientOriginalName();
        $name = pathinfo($origName, PATHINFO_FILENAME);
        $ext  = strtolower($file->getClientOriginalExtension());

        $safeName = Str::slug($name, '-') ?: 'file';

        $dir = trim($baseDir, '/');
        $finalName = $safeName . '.' . $ext;
        $path = "$dir/$finalName";

        $i = 1;
        while (Storage::disk('public')->exists($path)) {
            $finalName = $safeName . " ($i)." . $ext;
            $path = "$dir/$finalName";
            $i++;
        }

        $file->storeAs($dir, $finalName, 'public');
        return $path;
    }

    public function resetForm()
    {
        $this->task_name        = $this->task->name;
        $this->task_details     = $this->task->description;
        $this->deadline         = $this->task->deadline?->format('Y-m-d');
        $this->assigned_user_id = $this->task->assigned_user_id;
        $this->task_file        = null;
    }

    public function render()
    {
        $participants = $this->task->listing
            ->applications()
            ->where('accepted', true)
            ->with('user')
            ->get();

        return view('livewire.update-task-form', compact('participants'));
    }
}
