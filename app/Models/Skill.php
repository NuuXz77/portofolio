<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'category',
        'category_id',
        'level',
        'icon',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'level' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function portfolioCategory(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }
}
