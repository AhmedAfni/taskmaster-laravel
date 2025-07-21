<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // This method allows an admin to add a new user to the system.
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',    // Name is required
            'email'    => 'required|email|unique:users,email', // Email must be unique in the users table
            'password' => 'required|min:6', // Password must be at least 6 characters long
        ]);

        // The user is created with a hashed password for security
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'User created successfully.'); // Redirects back with a flash success message.
    }

    // This method allows the admin to delete a user from the system.
    public function destroy($id)
    {
        $user = User::findOrFail($id); // Attempts to find the user by ID.

        // Optional: prevent deleting self or another admin
        if ($user->id == auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        // Deletes the user from the database.
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.'); // Redirects back with a flash success message.
    }
}
