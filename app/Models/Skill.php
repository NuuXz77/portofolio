<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'category',
        'level',
        'icon',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_visible' => 'boolean',
    ];
}
