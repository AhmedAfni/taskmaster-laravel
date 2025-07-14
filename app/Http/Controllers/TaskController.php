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
                'description' => 'required|string|max:16777215' // Changed to handle larger content
            ]);

            $task = Task::create([
                'name' => $request->name,
                'description' => $request->description,
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
            'description' => 'required|string|max:16777215'
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'name' => $task->name,
                'description' => $task->description
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

                // Return the URL that can be used in TinyMCE
                $url = asset('storage/' . $path);

                return response()->json([
                    'success' => true,
                    'url' => $url,
                    'path' => $path
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file found'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
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
