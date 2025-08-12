<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Livewire\ShowManageTasks; // 👈 add this

class RequestModificationForm extends Component
{
    public Task $task;
    public string $modification_message = '';

    public function mount(Task $task)
    {
        if (!in_array($task->status, ['submitted', 'modification_requested'])) {
            abort(403, 'Cannot request modification at this task status.');
        }

        $this->task = $task;
        $this->modification_message = $task->modification_note ?? '';
    }

    protected function rules()
    {
        return [
            'modification_message' => 'required|string|max:2000',
        ];
    }

    public function requestModification()
    {
        $this->validate();

        if ($this->task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($this->task->status, ['submitted', 'modification_requested'])) {
            session()->flash('error', 'You can only request a modification for submitted tasks.');
            return;
        }

        $this->task->modification_note = $this->modification_message;
        $this->task->status = 'modification_requested';
        $this->task->save();

        // ✅ expected DOM version
        $expectedTs = $this->task->updated_at->getTimestamp();
        $flash = ['message' => 'Modification requested.', 'type' => 'success'];

        // ✅ refresh parent + keep your event if you like
        $this->dispatch('$refresh')->to(ShowManageTasks::class);
        $this->dispatch('modificationRequested')->to(ShowManageTasks::class);

        // ✅ tell browser which version to wait for
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
        return view('livewire.request-modification-form');
    }
}
