<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AdminUserController;

// Redirect root to register page
Route::get('/', function () {
    return redirect()->route('register');
});

// --------------------
// Auth for Users
// --------------------
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// --------------------
// Task Routes (User Side)
// --------------------
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/tasks/{task}/complete', [TaskController::class, 'update']);
Route::post('/tasks/{task}/delete', [TaskController::class, 'destroy']);
Route::post('/tasks/{task}/edit', [TaskController::class, 'editName']);

// --------------------
// Admin Routes
// --------------------
Route::prefix('admin')->group(function () {

    // Admin Auth
    Route::get('register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register.form');
    Route::post('register', [AdminAuthController::class, 'register'])->name('admin.register');
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login');

    Route::middleware('auth:admin')->group(function () {
        // Dashboard
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User Management
        Route::get('users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('users/{user}/tasks', [AdminController::class, 'viewUserTasks'])->name('admin.users.tasks');
        Route::post('users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.delete');

        // Admin Logout
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // Task Management
        Route::post('tasks/assign', [AdminController::class, 'assignTaskToUser'])->name('admin.tasks.assign');
        Route::post('tasks/{task}/complete', [AdminController::class, 'completeTask'])->name('admin.tasks.complete');
        Route::post('tasks/{task}/undo', [AdminController::class, 'undoTask'])->name('admin.tasks.undo');
        Route::post('tasks/{task}/edit', [AdminController::class, 'editTask'])->name('admin.tasks.edit');
        Route::post('tasks/{task}/delete', [AdminController::class, 'deleteTask'])->name('admin.tasks.delete');
    });
});