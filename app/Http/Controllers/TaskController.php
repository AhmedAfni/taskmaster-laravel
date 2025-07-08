<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Change is here 👇
        $tasks = Task::where('user_id', $userId)->oldest()->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('completed', true)->count();
        $pendingTasks = $tasks->where('completed', false)->count();

        return view('tasks', compact('tasks', 'totalTasks', 'completedTasks', 'pendingTasks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $task = Task::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'completed' => false,
            'completed_at' => null
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return redirect('/');
    }

    public function update(Task $task, Request $request)
    {
        $this->authorize('update', $task);

        $isNowCompleted = !$task->completed;

        $task->update([
            'completed' => $isNowCompleted,
            'completed_at' => $isNowCompleted ? now() : null
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'completed' => $isNowCompleted]);
        }

        return redirect('/');
    }

    public function destroy(Task $task, Request $request)
    {
        $this->authorize('delete', $task);

        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect('/');
    }

    public function editName(Request $request, Task $task)
    {
        $this->authorize('editName', $task);

        $request->validate(['name' => 'required|string|max:255']);

        $task->update(['name' => $request->name]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'name' => $task->name]);
        }

        return redirect('/');
    }
}