<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
use App\Models\Listing;
use App\Livewire\ShowManageTasks;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateTaskForm extends Component
{
    use WithFileUploads;

    public $listing;
    public $task_name;
    public $task_details;
    public $deadline;
    public $assigned_user_id;
    public $task_file;

    public function mount($listing)
    {
        // Ensure listing has participants loaded
        $this->listing = Listing::with('applications.user')->findOrFail($listing->id);

        // Auto-select if there is exactly one accepted participant
        $accepted = $this->listing->applications->where('accepted', true)->pluck('user_id');
        if ($accepted->count() === 1) {
            $this->assigned_user_id = $accepted->first();
        }
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

    public function create()
    {
        $this->validate();

        // 1) Create the task first (so we have an ID for folder)
        $task = new Task();
        $task->author_id        = auth()->id();
        $task->listing_id       = $this->listing->id;
        $task->name             = $this->task_name;
        $task->description      = $this->task_details;
        $task->deadline         = $this->deadline;
        $task->assigned_user_id = $this->assigned_user_id;
        $task->status           = 'assigned';
        $task->save();

        // 2) If a file was sent, store it with the original filename under task folder
        if ($this->task_file) {
            $path = $this->storeWithOriginalName(
                $this->task_file,
                "task_assignments/{$task->id}"
            );
            $task->file = $path;

            // Optional: keep a separate original-name column if you created it
            // $task->original_file_name = $this->task_file->getClientOriginalName();

            $task->save();
        }

        // Timestamp we expect to see on the new DOM node
        $expectedTs = $task->updated_at->getTimestamp();

        // Ask parent to re-render (so the new card appears)
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskCreated')->to(ShowManageTasks::class);

        // Tell the browser to wait for the new DOM version, then show toast
        $this->dispatch(
            'taskDomShouldReflect',
            taskId: $task->id,
            updatedAt: $expectedTs,
            flash: ['message' => 'Task created.', 'type' => 'success']
        );

        // Reset inputs (keep assigned_user_id unless you want to clear it)
        $this->reset(['task_name', 'task_details', 'deadline', 'task_file']);
        // If you prefer to clear assigned user too, add 'assigned_user_id' above.
    }

    /**
     * Store an uploaded file using its original filename (sanitized),
     * avoiding collisions inside $baseDir by appending " (2)", " (3)", ...
     * Returns the relative path saved on the 'public' disk.
     */
    protected function storeWithOriginalName(UploadedFile $file, string $baseDir): string
    {
        $origName = $file->getClientOriginalName();
        $name = pathinfo($origName, PATHINFO_FILENAME);
        $ext  = strtolower($file->getClientOriginalExtension());

        // Sanitize the base name but keep it human-readable
        $safeName = Str::slug($name, '-');
        if ($safeName === '') {
            $safeName = 'file';
        }

        $dir = trim($baseDir, '/'); // e.g. "task_assignments/123"
        $finalName = $safeName . '.' . $ext;
        $path = "$dir/$finalName";

        // Avoid collisions (keep visible base, add " (2)" style)
        $i = 1;
        while (Storage::disk('public')->exists($path)) {
            $finalName = $safeName . " ($i)." . $ext;
            $path = "$dir/$finalName";
            $i++;
        }

        // Store on public disk so Storage::url($path) works
        $file->storeAs($dir, $finalName, 'public');

        return $path;
    }

    public function render()
    {
        return view('livewire.create-task-form', [
            'participants' => $this->listing
                ->applications()
                ->where('accepted', true)
                ->with('user')
                ->get(),
        ]);
    }
}
