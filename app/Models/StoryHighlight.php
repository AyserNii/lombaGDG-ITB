<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StoryHighlight extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'thumbnail_path',
        'thumbnail_story_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'story_highlight_stories')->withTimestamps();
    }

    public function thumbnailStory(): BelongsTo
    {
        return $this->belongsTo(Story::class, 'thumbnail_story_id');
    }
}
