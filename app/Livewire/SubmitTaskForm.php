<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class SubmitTaskForm extends Component
{
    use WithFileUploads;

    public Task $task;
    public $result_text;
    public $result_file;

    public function mount(Task $task)
    {
        $this->task         = $task;
        $this->result_text  = $task->result_text;
        $this->result_file  = null; // fresh upload each time
    }

    protected function rules()
    {
        return [
            'result_text' => 'required|string|max:5000',
            'result_file' => 'nullable|file|max:10240',
        ];
    }

    public function submit()
    {
        // Only the assigned student may submit
        if ($this->task->assigned_user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $this->validate();

        // Persist text
        $this->task->result_text = $this->result_text;

        // Persist file (replace existing)
        if ($this->result_file) {
            if ($this->task->result_file) {
                Storage::disk('public')->delete($this->task->result_file);
            }

            // save using original filename under the task-specific folder
            $path = $this->storeWithOriginalName(
                $this->result_file,
                "task_submissions/{$this->task->id}"
            );

            $this->task->result_file = $path;

            // Optional if you add a column:
            // $this->task->original_result_file_name = $this->result_file->getClientOriginalName();
        }

        // Status transitions to submitted on student submit
        $this->task->status = 'submitted';
        $this->task->save();

        $expectedTs = $this->task->updated_at->getTimestamp();

        // Ask the student task list to re-render
        $this->dispatch('$refresh')->to(ShowStudentTasks::class);
        $this->dispatch('studentSubmitted')->to(ShowStudentTasks::class);

        // Let the browser wait for DOM, then stop spinner + toast
        $this->dispatch(
            'taskDomShouldReflect',
            taskId: $this->task->id,
            updatedAt: $expectedTs,
            flash: ['message' => 'Submission sent.', 'type' => 'success']
        );
    }

    public function clearForm()
    {
        // Restore form to current DB values and drop temp upload
        $this->result_text = $this->task->result_text;
        $this->result_file = null;
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

    public function render()
    {
        return view('livewire.submit-task-form');
    }
}
