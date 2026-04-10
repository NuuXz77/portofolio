<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeSkill(Builder $query): Builder
    {
        return $query->where('type', 'skill');
    }

    public function scopeProject(Builder $query): Builder
    {
        return $query->where('type', 'project');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'category_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'category_id');
    }
}
