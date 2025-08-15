<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{

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
