<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $adminCount = Admin::count();
        $tasks = Task::with('user')->oldest()->get();
        $users = User::all();

        return view('admin.dashboard', compact('userCount', 'adminCount', 'tasks', 'users'));
    }

    public function completeTask(Task $task)
    {
        $task->update([
            'completed' => true,
            'completed_at' => now()
        ]);

        // Check if it's an AJAX request
        if (request()->ajax()) {
            // Reload the task with fresh data including the completed_at timestamp
            $task = $task->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Task marked as completed.',
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'completed' => $task->completed,
                    'completed_at' => $task->completed_at,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at
                ]
            ]);
        }

        return back()->with('success', 'Task marked as completed.');
    }

    public function undoTask(Task $task)
    {
        $task->update([
            'completed' => false,
            'completed_at' => null
        ]);

        // Check if it's an AJAX request
        if (request()->ajax()) {
            // Reload the task with fresh data
            $task = $task->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Task marked as incomplete.',
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'completed' => $task->completed,
                    'completed_at' => $task->completed_at,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at
                ]
            ]);
        }

        return back()->with('success', 'Task marked as incomplete.');
    }

    public function editTask(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:16777215',
            'description2' => 'nullable|string|max:16777215'
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'description2' => $request->description2
        ]);

        // Check if it's an AJAX request
        if ($request->ajax()) {
            // Reload the task with fresh data
            $task = $task->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully.',
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'description2' => $task->description2,
                    'completed' => $task->completed,
                    'completed_at' => $task->completed_at,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at
                ]
            ]);
        }

        return back()->with('success', 'Task updated successfully.');
    }

    /**
     * Get task data for API calls (used by admin modals)
     */
    public function getTaskData(Task $task)
    {
        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'description2' => $task->description2,
                'completed' => $task->completed,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
                'completed_at' => $task->completed_at
            ]
        ]);
    }

    public function deleteTask(Task $task)
    {
        $task->delete();

        // Check if it's an AJAX request
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully.'
            ]);
        }

        return back()->with('success', 'Task deleted successfully.');
    }

    public function users()
    {
        $users = User::withCount('tasks')->get(); // adds 'tasks_count' attribute
        return view('admin.users', compact('users'));
    }

    public function assignTaskToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_name' => 'required|string|max:255',
            'task_description' => 'required|string|max:16777215',
            'task_description2' => 'nullable|string|max:16777215',
        ]);

        $task = Task::create([
            'user_id' => $request->user_id,
            'name' => $request->task_name,
            'description' => $request->task_description,
            'description2' => $request->task_description2,
            'completed' => false,
        ]);

        // Check if it's an AJAX request
        if ($request->ajax()) {
            // Load the task with user relationship
            $task = $task->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Task assigned successfully.',
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'description2' => $task->description2,
                    'completed' => $task->completed,
                    'completed_at' => $task->completed_at,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                    'user' => [
                        'id' => $task->user->id,
                        'name' => $task->user->name,
                        'email' => $task->user->email
                    ]
                ]
            ]);
        }

        return back()->with('success', 'Task assigned successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function viewUserTasks(User $user)
    {
        $tasks = $user->tasks()->latest()->get(); // Show newest first
        return view('admin.user-tasks', compact('user', 'tasks'));
    }
}
