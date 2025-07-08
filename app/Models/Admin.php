<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * Allow these fields to be mass assigned.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hide sensitive attributes when serializing.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}