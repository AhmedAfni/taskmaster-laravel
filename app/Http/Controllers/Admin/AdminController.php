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
    // Display the admin dashboard
    public function dashboard()
    {
        $userCount = User::count();     // Count total number of users
        $adminCount = Admin::count();   // Count total number of admin users
        $tasks = Task::with('user')->oldest()->get(); // All tasks (oldest first) with user
        $users = User::all(); // Get all users

        // Return the admin dashboard view with user and task data
        return view('admin.dashboard', compact('userCount', 'adminCount', 'tasks', 'users'));
    }

    // Mark a task as completed
    public function completeTask(Task $task)
    {
        $task->update([
            'completed' => true,    // Mark the task as completed
            'completed_at' => now()     // Set the completed_at timestamp to now
        ]);

        // Check if it's an AJAX request
        if (request()->ajax()) {
            $task = $task->fresh();  // Reload the task with fresh data including the completed_at timestamp

            return response()->json([
                'success' => true,  // Indicate success
                'message' => 'Task marked as completed.',   // Success message
                'task' => [
                    'id' => $task->id,  // Task ID
                    'name' => $task->name,  // Task name
                    'completed' => $task->completed,    // Task completion status
                    'completed_at' => $task->completed_at,  // Task completion time
                    'created_at' => $task->created_at,  // Task creation time
                    'updated_at' => $task->updated_at   // Task last updated time
                ]
            ]);
        }

        return back()->with('success', 'Task marked as completed.'); // Redirect back with success message
    }

    // Undo a completed task
    public function undoTask(Task $task)
    {
        $task->update([
            'completed' => false,   // Mark the task as not completed
            'completed_at' => null  // Clear the completed_at timestamp
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

        return back()->with('success', 'Task marked as incomplete.'); // Redirect back with success message
    }

    // Edit a task
    public function editTask(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',    // Task name is required
            'description' => 'required|string|max:16777215', // Task description is required
            'description2' => 'nullable|string|max:16777215'    // Optional second description
        ]);

        $task->update([
            'name' => $request->name, // Update task name
            'description' => $request->description,     // Update task description
            'description2' => $request->description2    // Update optional second description
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

    // Get task data for AJAX requests
    public function getTaskData(Task $task)
    {
        return response()->json([   //Returns the task’s details in JSON format
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

    // Delete a task
    public function deleteTask(Task $task)
    {
        $task->delete(); // Delete the task

        // Check if it's an AJAX request
        if (request()->ajax()) {
            return response()->json([   //Returns a JSON response for AJAX, or redirects back.
                'success' => true,  // Indicate success
                'message' => 'Task deleted successfully.' // Success message
            ]);
        }

        return back()->with('success', 'Task deleted successfully.'); // Redirect back with success message
    }

    // Display aLL users
    public function users()
    {
        $users = User::withCount('tasks')->get(); // Loads a list of users along with their task counts using
        return view('admin.users', compact('users')); // Loads the admin.users Blade view
    }

    // Assign a task to a user
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

        return back()->with('success', 'Task assigned successfully.');  // Redirect back with success message
    }

    // Create a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Create a new user with the provided details
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),   // Hash the password before saving
        ]);

        return redirect()->back()->with('success', 'User created successfully.');   // Redirect back with success message
    }

    // Loads and displays all tasks for a selected user
    public function viewUserTasks(User $user)
    {
        $tasks = $user->tasks()->latest()->get(); // Sorted by latest first
        return view('admin.user-tasks', compact('user', 'tasks'));  // Returns admin.user-tasks view
    }
}