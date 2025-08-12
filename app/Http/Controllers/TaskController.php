<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'task_name' => 'required|string|max:255',
            'task_details' => 'nullable|string',
            'task-file' => 'nullable|file|max:10240',
            'assigned_user_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $task = new Task();
        $task->author_id = auth()->id();
        $task->listing_id = $formFields['listing_id'];
        $task->assigned_user_id = $formFields['assigned_user_id'];
        $task->name = $formFields['task_name'];
        $task->description = $formFields['task_details'];
        $task->deadline = $formFields['deadline'] ?? null;
        $task->status = 'assigned';

        if ($request->hasFile('task-file')) {
            $task->file = $request->file('task-file')->store('task_assignments', 'public');
        }

        $task->save();

        return back()->with('message', 'Task created and assigned successfully.');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'task_name' => 'required|string|max:255',
            'task_details' => 'nullable|string',
            'task-file' => 'nullable|file|max:10240',
            'assigned_user_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $task->name = $data['task_name'];
        $task->description = $data['task_details'];
        $task->assigned_user_id = $data['assigned_user_id'];
        $task->deadline = $data['deadline'] ?? null;

        if ($request->hasFile('task-file')) {
            if ($task->file) {
                Storage::disk('public')->delete($task->file);
            }
            $task->file = $request->file('task-file')->store('task_assignments', 'public');
        }

        $task->save();

        return back()->with('message', 'Task updated successfully.');
    }

    public function submit(Request $request, Task $task)
    {
        if ($task->assigned_user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'result_text' => 'nullable|string|max:2000',
            'result_file' => 'nullable|file|max:10240',
        ]);

        $task->result_text = $validated['result_text'] ?? null;

        if ($request->hasFile('result_file')) {
            $task->result_file = $request->file('result_file')->store('task_results', 'public');
        }

        $task->status = 'submitted';
        $task->modification_note = null;

        $task->save();

        return back()->with('message', 'Task submitted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        if ($task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $action = $request->input('action');

        if ($action === 'request_modification') {
            $request->validate([
                'modification_message' => 'required|string|max:2000',
            ]);

            $task->modification_note = $request->input('modification_message');
            $task->status = 'modification_requested';
            $task->save();

            return back()->with('message', 'Modification requested.');
        }

        if ($action === 'confirm_task') {
            $task->status = 'finished';
            $task->modification_note = null;
            $task->save();

            return back()->with('message', 'Task marked as finished.');
        }

        if ($action === 'reopen_task') {
            $task->status = ($task->result_text || $task->result_file) ? 'submitted' : 'assigned';
            $task->modification_note = null;
            $task->save();

            return back()->with('message', 'Task reopened.');
        }

        return back()->with('error', 'Invalid action.');
    }

    public function destroy(Task $task)
    {
        if ($task->author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($task->file) {
            Storage::disk('public')->delete($task->file);
        }

        $task->delete();

        return back()->with('message', 'Task deleted successfully.');
    }
}
