<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    // The Task model represents a task in the application.
    protected $fillable = [
        'name', 'description', 'description2', 'completed', 'user_id', 'completed_at', 'assigned_by_admin_id', 'image', 'scheduled_at', 'google_event_link', 'google_meet_link'
    ];
    // The fillable property specifies which attributes can be assigned.

    protected $casts = [ // The casts property specifies how attributes should be cast when retrieved from the database.
        'created_at' => 'datetime', // The created_at attribute is cast to a datetime instance.
        'updated_at' => 'datetime', // The updated_at attribute is cast to a datetime instance.
        'completed_at' => 'datetime', // The completed_at attribute is cast to a datetime instance.
        'scheduled_at' => 'datetime', // The scheduled_at attribute is cast to a datetime instance.
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // The user method defines a relationship where a task belongs to a user.
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'assigned_by_admin_id');
        // The admin method defines a relationship where a task is assigned by an admin.
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class);
    }
}