<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task) // Checks if the current user owns the task.
    {
        return $user->id === $task->user_id; // Allows the user to view only their own task.
    }

    public function update(User $user, Task $task) // User can update the task only if they created it.
    {
        return $user->id === $task->user_id; // Prevents users from editing someone else’s task.
    }

    public function delete(User $user, Task $task) // Only the task owner is allowed to delete the task.
    {
        return $user->id === $task->user_id;
    }

    public function editName(User $user, Task $task) // Custom method to allow editing only the name of the task.
    {
        return $user->id === $task->user_id;
    }
}
