<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'body',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->oldest();
    }

    public static function topLevelForPost(int $postId, bool $withReplyCounts = false, string $sort = 'newest')
    {
        $query = static::where('post_id', $postId)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user']);

        if ($withReplyCounts) {
            $query->withCount('replies');
        }

        match ($sort) {
            'oldest' => $query->oldest(),
            'top' => $query->orderByDesc('replies_count')->latest(),
            default => $query->latest(),
        };

        return $query->get();
    }

    public static function withUserAndRepliesOrFail(int $commentId): self
    {
        return static::with(['user', 'replies.user'])->findOrFail($commentId);
    }
}

