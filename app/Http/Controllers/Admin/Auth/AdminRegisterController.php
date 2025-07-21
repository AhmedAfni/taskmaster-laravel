<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminRegisterController extends Controller
{
    // Display the admin registration form
    public function showRegistrationForm()
    {
        return view('admin.auth.register');
    }

    // Handle the admin registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],// required name field must be a string and max 255 characters
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],// required email field must be a valid email, max 255 characters, and unique in users table
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // required password field must be a string, min 8 characters, and confirmed (password confirmation field)
        ]);

        // Create a new admin user
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before saving
            'is_admin' => true, // Set is_admin to true to indicate this user is an admin
        ]);

        Auth::login($admin);
        return redirect('/admin/dashboard');    // Redirect to the admin dashboard after successful registration
    }

    // Assign a task to a user
    public function assignTaskToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Validate that user_id exists in users table
            'name' => 'required|string|max:255', // task name is required and must be a valid string
        ]);

        // Create a new task assigned to the specified user by the admin
        Task::create([
            'user_id' => $request->user_id, // the ID of the user to whom the task is assigned
            'name' => $request->name,   // name of the task
            'assigned_by_admin_id' => auth('admin')->id(),  // ID of the admin assigning the task
            'completed' => false, // task is initially not completed
        ]);

        return back()->with('success', 'Task assigned to user.'); // Redirect back with success message
    }

}
