<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Show the admin registration form
    public function showRegisterForm()
    {
        return view('admin.register'); // Loads the admin registration page from the blade view
    }

    // Handle the admin registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|confirmed|min:6', // Password must be confirmed and at least 6 characters long
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login.form')
                         ->with('success', 'Registration successful. Please log in.');
    }

    // Show the admin login form
    public function showLoginForm()
    {
        return view('admin.login'); // Loads the admin login page from the blade view
    }

    // Handle the admin login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) { // Attempt to log in the admin user & configured a custom admin guard
            return redirect()->route('admin.dashboard') // Redirect to the admin dashboard after successful login
                             ->with('success', 'Login successful'); // Adds a success message
        }

        // If login fails and redirects back
        return back()
            ->withErrors(['email' => 'Invalid email or password'])  // Adds an error message for invalid credentials
            ->with('error', 'Invalid email or password') // error flash message (for SweetAlert or similar)
            ->withInput();  // withInput() keeps the email field filled
    }

    // Logs out the current admin user
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate(); // Invalidates and regenerates the session
        $request->session()->regenerateToken();

        // Redirects to login page with a success message
        return redirect()->route('admin.login.form')->with('success', 'Logged out successfully.');
    }
}
