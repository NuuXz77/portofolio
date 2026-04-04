<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'created_by',
        'title',
        'slug',
        'thumbnail_path',
        'excerpt',
        'content',
        'tags',
        'status',
        'visibility',
        'published_at',
        'author_name',
        'read_time',
        'view_count',
        'access_token',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->published()->where('visibility', 'public');
    }
}
