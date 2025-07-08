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
        $task->update(['completed' => true]);
        return back()->with('success', 'Task marked as completed.');
    }

    public function undoTask(Task $task)
    {
        $task->update(['completed' => false]);
        return back()->with('success', 'Task marked as incomplete.');
    }

    public function editTask(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $task->update(['name' => $request->name]);
        return back()->with('success', 'Task updated successfully.');
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
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
        ]);

        Task::create([
            'user_id' => $request->user_id,
            'name' => $request->task_name,
            'completed' => false,
        ]);

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
