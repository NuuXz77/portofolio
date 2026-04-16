<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleCommentLike extends Model
{
    protected $fillable = [
        'article_comment_id',
        'device_token_hash',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(ArticleComment::class, 'article_comment_id');
    }
}
