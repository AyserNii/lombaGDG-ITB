<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'headline',
        'email',
        'google_id',
        'password',
        'role',
        'role_type',
        'location',
        'location_latitude',
        'location_longitude',
        'location_country',
        'bio',
        'website',
        'gender',
        'note',
        'avatar',
        'profile_cover',
        'phone',
        'savings',
        'sessions_count',
        'co2_saved',
        'badge',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function isFollowing(int|string $userId): bool
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->latest();
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function storyHighlights(): HasMany
    {
        return $this->hasMany(StoryHighlight::class)->latest();
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'saved_posts', 'user_id', 'post_id')->withTimestamps()->latest('saved_posts.created_at');
    }

    public function hasSaved(int|string $postId): bool
    {
        return $this->savedPosts()->where('post_id', $postId)->exists();
    }

    public static function firstRecord(): ?self
    {
        return static::query()->first();
    }

    public static function totalCount(): int
    {
        return static::query()->count();
    }

    public static function findById(int $userId): ?self
    {
        return static::query()->find($userId);
    }

    public static function findByIdOrFail(int $userId): self
    {
        return static::query()->findOrFail($userId);
    }

    public static function findByUsernameOrId(string $identifier): ?self
    {
        return static::query()
            ->where('username', $identifier)
            ->orWhere('id', is_numeric($identifier) ? (int) $identifier : 0)
            ->first();
    }

    public static function findByUsernameOrIdOrFail(string $identifier): self
    {
        return static::query()
            ->where('username', $identifier)
            ->orWhere('id', is_numeric($identifier) ? (int) $identifier : 0)
            ->firstOrFail();
    }

    public static function findByGoogleId(string $googleId): ?self
    {
        return static::query()->where('google_id', $googleId)->first();
    }

    public static function findByEmail(string $email): ?self
    {
        return static::query()->where('email', $email)->first();
    }

    public static function usernameExists(string $username): bool
    {
        return static::query()->where('username', $username)->exists();
    }

    public static function firstExcept(?int $userId = null): ?self
    {
        $query = static::query();

        if ($userId !== null) {
            $query->where('id', '!=', $userId);
        }

        return $query->first();
    }

    public static function suggestions(?int $exceptUserId = null, int $limit = 4)
    {
        $query = static::query();

        if ($exceptUserId !== null) {
            $query->where('id', '!=', $exceptUserId);
        }

        return $query->take($limit)->get();
    }

    public function followingUserIds(): array
    {
        return $this->following()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
    }

    public function followerUserIds(): array
    {
        return $this->followers()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
    }

    public function profileHighlights()
    {
        return $this->storyHighlights()->with(['stories', 'thumbnailStory'])->get();
    }

    public function archivedStories()
    {
        return $this->stories()->whereDoesntHave('highlights')->latest()->get();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }
}
