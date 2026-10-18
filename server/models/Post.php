<?php
// /server/models/Post.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Live Feed post — the `posts` table (scripts/migrations/2026_10_16_000005_*).
 * media_url is a bare file name in public/images/uploads/posts/.
 */
class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'orig_user_id',
        'content',
        'media_url',
        'media_type', // 'image', 'video', or 'none'
    ];

    protected $casts = [
        'orig_user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'orig_user_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id')->orderBy('created_at', 'asc');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    /**
     * Posts written by any of the given user IDs.
     */
    public function scopeFromAuthors($query, array $userIds)
    {
        return $query->whereIn('orig_user_id', $userIds);
    }
}
