<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    protected $fillable = [
        'task_id', 'image_path'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
