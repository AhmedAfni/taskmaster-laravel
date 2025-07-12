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

        $tasks = Task::where('user_id', $userId)->oldest()->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('completed', true)->count();
        $pendingTasks = $tasks->where('completed', false)->count();

        return view('tasks', compact('tasks', 'totalTasks', 'completedTasks', 'pendingTasks'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:16777215'
            ]);

            $task = Task::create([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => Auth::id(),
                'completed' => false,
                'completed_at' => null
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'task' => [
                        'id' => $task->id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                        'completed' => $task->completed,
                        'completed_at' => $task->completed_at
                    ]
                ]);
            }

            return redirect('/');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Task creation failed: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create task: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
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
            return response()->json([
                'success' => true,
                'completed' => $isNowCompleted,
                'name' => $task->name,
                'description' => $task->description,
                'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null
            ]);
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

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:16777215'
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'name' => $task->name,
                'description' => $task->description
            ]);
        }

        return redirect('/');
    }
}
