<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'category_id',
        'is_featured',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'tech_stack' => 'array',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function portfolioCategory(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }
}
