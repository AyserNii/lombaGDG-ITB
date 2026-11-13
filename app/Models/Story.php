<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Story extends Model
{
    protected $fillable = [
        'user_id',
        'image',
        'caption',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(StoryHighlight::class, 'story_highlight_stories')->withTimestamps();
    }

    public static function activeWithUsers()
    {
        return static::with('user')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();
    }

    public static function highlightedIds(): array
    {
        return static::whereHas('highlights')->pluck('id')->all();
    }

    public static function countActive(): int
    {
        return static::where('expires_at', '>', now())->count();
    }

    public static function ownedByWithIds(int $userId, array $storyIds)
    {
        return static::where('user_id', $userId)
            ->whereIn('id', $storyIds)
            ->get();
    }
}
