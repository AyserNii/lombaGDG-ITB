<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'caption',
        'type',
        'likes_count',
        'comments_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_posts', 'post_id', 'user_id')->withTimestamps();
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function reactions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')
            ->withPivot('reaction')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with(['user', 'replies'])->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->reactions()
            ->where('user_id', $user->id)
            ->wherePivot('reaction', 'like')
            ->exists();
    }

    public static function communityFeed(?string $type = null)
    {
        $query = static::with('user')->withCount([
            'reactions as likes_total' => fn ($query) => $query->where('reaction', 'like'),
            'reactions as dislikes_total' => fn ($query) => $query->where('reaction', 'dislike'),
        ])->latest();

        if (! empty($type)) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public static function dashboardHighlights(int $limit = 3)
    {
        return static::with('user')
            ->orderByDesc('likes_count')
            ->latest()
            ->take($limit)
            ->get();
    }

    public static function withAuthorOrFail(int $postId): self
    {
        return static::with('user')->findOrFail($postId);
    }

    public static function findByIdOrFail(int $postId): self
    {
        return static::query()->findOrFail($postId);
    }

    public static function withAuthorAndReactionCountsOrFail(int $postId): self
    {
        return static::with('user')->withCount([
            'reactions as likes_total' => fn ($query) => $query->where('reaction', 'like'),
            'reactions as dislikes_total' => fn ($query) => $query->where('reaction', 'dislike'),
        ])->findOrFail($postId);
    }

    public static function viewerReactionsFor(int $userId, array $postIds)
    {
        if ($postIds === []) {
            return collect();
        }

        return DB::table('post_likes')
            ->where('user_id', $userId)
            ->whereIn('post_id', $postIds)
            ->pluck('reaction', 'post_id');
    }

    public static function viewerSavedPostIdsFor(int $userId, array $postIds): array
    {
        if ($postIds === []) {
            return [];
        }

        return DB::table('saved_posts')
            ->where('user_id', $userId)
            ->whereIn('post_id', $postIds)
            ->pluck('post_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function topicStats()
    {
        return static::query()
            ->selectRaw('type, COUNT(*) as total')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();
    }

    public static function totalCount(): int
    {
        return static::query()->count();
    }
}
