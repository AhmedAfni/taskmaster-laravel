<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Show the admin registration form.
     */
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    /**
     * Handle admin registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|confirmed|min:6',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login.form')
                         ->with('success', 'Registration successful. Please log in.');
    }

    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle admin login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard')
                             ->with('success', 'Login successful');
        }

        // Use both SweetAlert-compatible error and validation bag
        return back()
            ->withErrors(['email' => 'Invalid email or password'])
            ->with('error', 'Invalid email or password')
            ->withInput();
    }

    /**
     * Logout the admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form')->with('success', 'Logged out successfully.');
    }
}