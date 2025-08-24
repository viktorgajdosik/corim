<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
use App\Models\Listing;
use App\Models\Notification; // 🔔
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
        $this->listing = Listing::with('applications.user')->findOrFail($listing->id);

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

        // 1) create task
        $task = new Task();
        $task->author_id        = auth()->id();
        $task->listing_id       = $this->listing->id;
        $task->name             = $this->task_name;
        $task->description      = $this->task_details;
        $task->deadline         = $this->deadline;
        $task->assigned_user_id = $this->assigned_user_id;
        $task->status           = 'assigned';
        $task->save();

        // 2) store file (optional)
        if ($this->task_file) {
            $task->file = $this->storeWithOriginalName($this->task_file, "task_assignments/{$task->id}");
            $task->save();
        }

        // 🔔 notify participant (new task)
        Notification::create([
            'user_id' => $task->assigned_user_id,
            'type'    => 'task.assigned',
            'title'   => 'New task assigned',
            'body'    => "“{$task->name}” in “{$this->listing->title}”.",
            'url'     => route('listings.show', $this->listing->id),
        ]);
        $this->dispatch('notificationsChanged');

        // expected DOM
        $expectedTs = $task->updated_at->getTimestamp();

        // refresh list
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('taskCreated')->to(ShowManageTasks::class);

        // wait for DOM + toast
        $this->dispatch('taskDomShouldReflect', taskId: $task->id, updatedAt: $expectedTs, flash: [
            'message' => 'Task created.',
            'type'    => 'success'
        ]);

        // reset
        $this->reset(['task_name', 'task_details', 'deadline', 'task_file']);
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
