<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'created_by',
        'title',
        'title_translations',
        'slug',
        'thumbnail_path',
        'excerpt',
        'excerpt_translations',
        'content',
        'content_translations',
        'tags',
        'status',
        'visibility',
        'published_at',
        'author_name',
        'read_time',
        'view_count',
        'access_token',
        'seo_title',
        'seo_title_translations',
        'seo_description',
        'seo_description_translations',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'title_translations' => 'array',
        'excerpt_translations' => 'array',
        'content_translations' => 'array',
        'seo_title_translations' => 'array',
        'seo_description_translations' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'article_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class, 'article_id');
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
