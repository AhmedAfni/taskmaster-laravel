<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // Display a listing of the tasks for the authenticated user.
    public function index()
    {
        $userId = Auth::id();   // Get the authenticated user's ID

        $tasks = Task::where('user_id', $userId)->oldest()->get(); // Retrieve tasks for the user, ordered by creation date

        $totalTasks = $tasks->count(); // Count total tasks for the user
        $completedTasks = $tasks->where('completed', true)->count(); // Count completed tasks for the user
        $pendingTasks = $tasks->where('completed', false)->count(); // Count pending tasks for the user

        return view('tasks', compact('tasks', 'totalTasks', 'completedTasks', 'pendingTasks')); // Return the tasks view with the tasks and counts
    }

    // Store a new task for the authenticated user
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:16777215',
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

    // Update the completion status of a task
    public function update(Task $task, Request $request)
    {
        $this->authorize('update', $task); // Authorize the user to update the task

        $isNowCompleted = !$task->completed; // Toggle the completion status

        $task->update([ // Update the task with the new completion status
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

    // Delete a task and its associated images
    public function destroy(Task $task, Request $request)
    {
        $this->authorize('delete', $task); // Authorize the user to delete the task

        $this->deleteImagesFromDescription($task->description); // Delete images from the task description

        $task->delete(); // Delete the task

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect('/');
    }

    // Edit the name and description of a task
    public function editName(Request $request, Task $task)
    {
        $this->authorize('editName', $task); // Authorize the user to edit the task name

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

    // Show the details of a specific task
    public function show(Task $task)
    {
        $this->authorize('view', $task); // Authorize the user to view the task

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

    // Upload an image associated with a task
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240' // Validate the image file
            ]);

            if ($request->hasFile('image')) { // Check if the image file is present
                $image = $request->file('image'); // Get the uploaded image file

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension(); // Generate a unique filename

                $path = $image->storeAs('task-images', $filename, 'public'); // Store the image in the public disk under 'task-images' directory

                $baseUrl = config('app.url'); // Get the base URL from the application configuration

                $url = $baseUrl . '/storage/' . $path; // Construct the full URL to access the image

                $assetUrl = asset('storage/' . $path); // Get the asset URL for the image

                // Log the image upload details
                \Log::info('Image uploaded', [
                    'filename' => $filename, // Log the filename
                    'path' => $path, // Log the storage path
                    'config_url' => $baseUrl, // Log the base URL
                    'asset_url' => $assetUrl, // Log the asset URL
                    'final_url' => $url, // Log the final URL
                    'user_agent' => $request->header('User-Agent'), // Log the user agent
                    'referer' => $request->header('Referer') // Log the referer header
                ]);

                // Return a JSON response with the success status and image details
                return response()->json([
                    'success' => true,
                    'url' => $url,
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

    // Get the task data for a specific task
    public function getTaskData(Task $task)
    {
        if ($task->user_id !== Auth::id()) { // Check if the authenticated user is the owner of the task
            return response()->json(['error' => 'Unauthorized'], 403); // Return a 403 Unauthorized response if not
        }

        // Return the task data in JSON format
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

    // Serve an image file from the storage
    public function serveImage($filename)
    {
        $path = 'task-images/' . $filename; // Construct the path to the image file

        if (!Storage::disk('public')->exists($path)) { // Check if the image file exists in the public storage
            abort(404); // Return a 404 Not Found response if the file does not exist
        }

        $file = Storage::disk('public')->get($path); // Get the contents of the image file
        $mimeType = Storage::disk('public')->mimeType($path); // Get the MIME type of the image file

        return response($file)->header('Content-Type', $mimeType); // Return the image file with the appropriate content type header
    }

    // Delete images from the task description
    private function deleteImagesFromDescription($description)
    {
        // Use regex to find all image URLs in the description
        preg_match_all('/src="([^"]*storage\/task-images\/[^"]*)"/', $description, $matches);

        if (!empty($matches[1])) { // Check if any image URLs were found
            foreach ($matches[1] as $imageUrl) { // Iterate through each found image URL

                $path = str_replace(asset('storage/'), '', $imageUrl); // Remove the base URL to get the storage path

                if (Storage::disk('public')->exists($path)) { // Check if the image file exists in the public storage
                    Storage::disk('public')->delete($path); // Delete the image file from the public storage
                }
            }
        }
    }

}
