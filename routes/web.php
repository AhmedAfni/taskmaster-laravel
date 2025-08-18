<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Http\Request;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\Admin\ProductController;

// Redirect root to register page
Route::get('/', fn () => redirect()->route('register'));

// --------------------
// Auth for Users
// --------------------
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// User logout safety route - handle GET requests gracefully
Route::get('/logout', function() {
    return redirect()->route('login')
                   ->with('status', 'Please use the logout button to sign out.');
})->name('user.logout.get');

// --------------------
// Task Routes (User Side)
// --------------------
Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'update'])->name('tasks.complete');
    Route::post('/tasks/{task}/delete', [TaskController::class, 'destroy'])->name('tasks.delete');
    Route::post('/tasks/{task}/edit', [TaskController::class, 'editName'])->name('tasks.edit');
    Route::get('/tasks/{task}/details', [TaskController::class, 'show'])->name('tasks.details');
    Route::get('/tasks/{task}/full-view', [TaskController::class, 'fullView'])->name('tasks.fullView');

    // Image upload route
    Route::post('/upload-image', [TaskController::class, 'uploadImage'])->name('upload.image');

    // API route for fetching individual task data (for modals)
    Route::get('/api/tasks/{task}', [TaskController::class, 'getTaskData'])->name('api.tasks.show');
});

// Public Image Serving Route (must be outside auth middleware)
Route::get('/storage/task-images/{filename}', [TaskController::class, 'serveImage'])->name('serve.image');

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
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('users/{user}/tasks', [AdminController::class, 'viewUserTasks'])->name('admin.users.tasks');
        Route::post('users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.delete');

        // Admin logout routes - handle both GET and POST
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('logout', function() {
            return redirect()->route('admin.login.form')
                           ->with('info', 'Please use the logout button to sign out.');
        })->name('admin.logout.get');

        // Task management
        Route::post('tasks/assign', [AdminController::class, 'assignTaskToUser'])->name('admin.tasks.assign');
        Route::post('tasks/{task}/complete', [AdminController::class, 'completeTask'])->name('admin.tasks.complete');
        Route::post('tasks/{task}/undo', [AdminController::class, 'undoTask'])->name('admin.tasks.undo');
        Route::post('tasks/{task}/edit', [AdminController::class, 'editTask'])->name('admin.tasks.edit');
        Route::post('tasks/{task}/delete', [AdminController::class, 'deleteTask'])->name('admin.tasks.delete');

        // Admin image upload route (uses same controller as user side)
        Route::post('upload-image', [TaskController::class, 'uploadImage'])->name('admin.upload.image');

        // Admin API route for fetching task data
        Route::get('api/tasks/{task}', [AdminController::class, 'getTaskData'])->name('admin.api.tasks.show');

        // Product management
        Route::post('admin/products/assign', [ProductController::class, 'assignToUser'])->name('admin.products.assign');

        Route::middleware(['auth'])->group(function () {
    Route::get('/my-products', [ProductController::class, 'userProducts'])->name('user.products');
    // For deleting a product
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// For paying for a product (you can adjust the controller/method as needed)
Route::get('/products/{product}/pay', [ProductController::class, 'pay'])->name('products.pay');
});
    });

});

// Google OAuth integration
    Route::get('/google/oauth', [GoogleOAuthController::class, 'redirect'])->name('google.oauth.redirect');
    Route::get('/google/callback', [GoogleOAuthController::class, 'callback'])->name('google.oauth.callback');
