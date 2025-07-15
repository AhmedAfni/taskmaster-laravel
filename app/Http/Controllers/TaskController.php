<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tasks = Task::where('user_id', $userId)->oldest()->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('completed', true)->count();
        $pendingTasks = $tasks->where('completed', false)->count();

        return view('tasks', compact('tasks', 'totalTasks', 'completedTasks', 'pendingTasks'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:16777215', // Changed to handle larger content
                'description2' => 'nullable|string|max:16777215'
            ]);

            $task = Task::create([
                'name' => $request->name,
                'description' => $request->description,
                'description2' => $request->description2,
                'user_id' => Auth::id(),
                'completed' => false,
                'completed_at' => null
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'task' => [
                        'id' => $task->id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'description2' => $task->description2,
                        'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                        'completed' => $task->completed,
                        'completed_at' => $task->completed_at
                    ]
                ]);
            }

            return redirect('/');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create task: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function update(Task $task, Request $request)
    {
        $this->authorize('update', $task);

        $isNowCompleted = !$task->completed;

        $task->update([
            'completed' => $isNowCompleted,
            'completed_at' => $isNowCompleted ? now() : null
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'completed' => $isNowCompleted,
                'name' => $task->name,
                'description' => $task->description,
                'description2' => $task->description2,
                'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null
            ]);
        }

        return redirect('/');
    }

    public function destroy(Task $task, Request $request)
    {
        $this->authorize('delete', $task);

        // Extract and delete images from description before deleting task
        $this->deleteImagesFromDescription($task->description);

        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect('/');
    }

    public function editName(Request $request, Task $task)
    {
        $this->authorize('editName', $task);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:16777215',
            'description2' => 'nullable|string|max:16777215'
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'description2' => $request->description2
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'name' => $task->name,
                'description' => $task->description,
                'description2' => $task->description2
            ]);
        }

        return redirect('/');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json([
            'id' => $task->id,
            'name' => $task->name,
            'description' => $task->description,
            'description2' => $task->description2,
            'completed' => $task->completed,
            'created_at' => $task->created_at->format('Y-m-d H:i:s'),
            'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null
        ]);
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240' // 10MB max
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store in storage/app/public/task-images
                $path = $image->storeAs('task-images', $filename, 'public');

                // Generate absolute URL with proper base URL
                $baseUrl = config('app.url');
                $url = $baseUrl . '/storage/' . $path;

                // Alternative: Use asset() but ensure it generates absolute URL
                $assetUrl = asset('storage/' . $path);

                // Log for debugging
                \Log::info('Image uploaded', [
                    'filename' => $filename,
                    'path' => $path,
                    'config_url' => $baseUrl,
                    'asset_url' => $assetUrl,
                    'final_url' => $url,
                    'user_agent' => $request->header('User-Agent'),
                    'referer' => $request->header('Referer')
                ]);

                return response()->json([
                    'success' => true,
                    'url' => $url, // Use the absolute URL with config base
                    'path' => $path,
                    'debug' => [
                        'config_url' => $baseUrl,
                        'asset_url' => $assetUrl,
                        'final_url' => $url
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file found'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get task data for API calls (used by modals)
     */
    public function getTaskData(Task $task)
    {
        // Ensure the task belongs to the authenticated user
        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'description2' => $task->description2,
                'completed' => $task->completed,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
                'completed_at' => $task->completed_at
            ]
        ]);
    }

    /**
     * Serve images directly (fallback for symbolic link issues)
     */
    public function serveImage($filename)
    {
        $path = 'task-images/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        return response($file)->header('Content-Type', $mimeType);
    }

    /**
     * Delete images from description content
     */
    private function deleteImagesFromDescription($description)
    {
        // Extract image URLs from the description HTML
        preg_match_all('/src="([^"]*storage\/task-images\/[^"]*)"/', $description, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $imageUrl) {
                // Extract the path from the URL
                $path = str_replace(asset('storage/'), '', $imageUrl);

                // Delete the file if it exists
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }

}
