<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
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
        $this->task = $task;
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

        $this->task->name             = $this->task_name;
        $this->task->description      = $this->task_details;
        $this->task->deadline         = $this->deadline;
        $this->task->assigned_user_id = $this->assigned_user_id;

        if ($this->task_file) {
            // Remove previous file (optional)
            if ($this->task->file) {
                Storage::disk('public')->delete($this->task->file);
            }

            // Save new file using original filename under task folder
            $path = $this->storeWithOriginalName(
                $this->task_file,
                "task_assignments/{$this->task->id}"
            );

            $this->task->file = $path;

            // Optional if you add a column:
            // $this->task->original_file_name = $this->task_file->getClientOriginalName();
        }

        $this->task->save();

        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Task updated.', 'type' => 'success'];

        // make parent refresh
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskUpdated')->to(ShowManageTasks::class);

        // tell the browser which DOM we’re waiting for + include flash
        $this->dispatch(
            'taskDomShouldReflect',
            taskId: $this->task->id,
            updatedAt: $expectedTs,
            flash: $flash
        );
    }

    /**
     * Store an uploaded file with its original filename (sanitized) inside $baseDir,
     * avoiding collisions by appending " (2)", " (3)", ...
     */
    protected function storeWithOriginalName(UploadedFile $file, string $baseDir): string
    {
        $origName = $file->getClientOriginalName();
        $name = pathinfo($origName, PATHINFO_FILENAME);
        $ext  = strtolower($file->getClientOriginalExtension());

        // Human-readable but safe
        $safeName = Str::slug($name, '-');
        if ($safeName === '') {
            $safeName = 'file';
        }

        $dir = trim($baseDir, '/');
        $finalName = $safeName . '.' . $ext;
        $path = "$dir/$finalName";

        // Collision avoidance
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
