<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable; //The Notifiable trait enables sending notifications to admins.

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [   // The hidden property specifies which attributes should be hidden for arrays.
        'password', // The password attribute is hidden for security reasons.
        'remember_token', // used to remember logged-in sessions
    ];
}
