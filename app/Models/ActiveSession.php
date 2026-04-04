<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveSession extends Model
{
    protected $fillable = [
        'session_id',
        'last_active',
    ];

    protected $casts = [
        'last_active' => 'datetime',
    ];
}
