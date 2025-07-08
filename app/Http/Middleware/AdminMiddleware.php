<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }

    public function assignTaskToUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id',
    ]);

    Task::create([
        'name' => $request->name,
        'user_id' => $request->user_id
    ]);

    return back()->with('success', 'Task assigned to user.');
}


}