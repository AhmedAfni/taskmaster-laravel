<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    // Display the admin login form
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // validate email and password
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the user is an admin and admin user can log in
        if (Auth::attempt(array_merge($credentials, ['is_admin' => true]), $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard'); // Redirect to the admin dashboard after successful login
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not authorized as admin.', // Error message for invalid login
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();     // Log out the admin user
        $request->session()->invalidate();      // Invalidate the current session to prevent reuse
        $request->session()->regenerateToken();     // Regenerate the CSRF token to avoid session hijacking

        return redirect('/admin/login');    // Redirect to the admin login page after logout
    }
}