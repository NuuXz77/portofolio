<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'tech_stack',
        'image_path',
        'demo_link',
        'github_link',
        'category',
        'is_featured',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
    ];
}
