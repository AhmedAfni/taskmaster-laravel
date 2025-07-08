<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        Auth::login($admin);

        return redirect('/admin/dashboard');
    }

    public function assignTaskToUser(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'name' => 'required|string|max:255',
    ]);

    Task::create([
        'user_id' => $request->user_id,
        'name' => $request->name,
        'assigned_by_admin_id' => auth('admin')->id(),
        'completed' => false,
    ]);

    return back()->with('success', 'Task assigned to user.');
}

}
