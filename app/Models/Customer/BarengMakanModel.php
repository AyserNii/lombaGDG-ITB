<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BarengMakanModel extends Model
{
    protected $table = 'food_sessions';

    protected $fillable = [
        'host_id',
        'merchant_name',
        'category',
        'description',
        'pickup_point',
        'location',
        'image',
        'slots_max',
        'normal_ongkir',
        'order_deadline',
        'popular_menus',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'popular_menus' => 'array',
            'order_deadline' => 'datetime',
        ];
    }

    /**
     * @return array{sessions: LengthAwarePaginator, activeSessionCount: int, activeParticipantCount: int}
     */
    public static function browse(string $location, string $searchTerm, int $userId, int $page): array
    {
        $openSessions = static::query()
            ->where('status', 'open')
            ->where(fn (Builder $query) => $query
                ->whereNull('order_deadline')
                ->orWhere('order_deadline', '>', now()));

        if ($location !== '') {
            $openSessions->whereRaw('LOWER(TRIM(location)) = ?', [mb_strtolower($location)]);
        }

        $activeSessionCount = (clone $openSessions)->count();
        $activeParticipantCount = DB::table('food_session_participants as participants')
            ->join('food_sessions as sessions', 'participants.food_session_id', '=', 'sessions.id')
            ->where('participants.status', 'joined')
            ->where('sessions.status', 'open')
            ->where(fn ($query) => $query
                ->whereNull('sessions.order_deadline')
                ->orWhere('sessions.order_deadline', '>', now()))
            ->when($location !== '', fn ($query) => $query->whereRaw(
                'LOWER(TRIM(sessions.location)) = ?',
                [mb_strtolower($location)]
            ))
            ->count();

        $sessionsQuery = (clone $openSessions)
            ->with('host:id,name,username,avatar')
            ->withCount([
                'participants as participants_count' => fn (Builder $query) => $query->where('status', 'joined'),
                'likes',
                'comments',
            ])
            ->withExists([
                'likes as is_liked_by_user' => fn (Builder $query) => $query->where('users.id', $userId),
                'participants as has_joined' => fn (Builder $query) => $query
                    ->where('user_id', $userId)
                    ->where('status', 'joined'),
            ]);

        if ($searchTerm !== '') {
            $sessionsQuery->where(fn (Builder $query) => $query
                ->where('merchant_name', 'like', '%'.$searchTerm.'%')
                ->orWhere('category', 'like', '%'.$searchTerm.'%')
                ->orWhere('description', 'like', '%'.$searchTerm.'%'));
        }

        return [
            'sessions' => $sessionsQuery
                ->latest()
                ->paginate(8, ['*'], 'page', max(1, $page))
                ->withQueryString(),
            'activeSessionCount' => $activeSessionCount,
            'activeParticipantCount' => $activeParticipantCount,
        ];
    }

    /**
     * @return array{percent: int, activeSessionCount: int, participatingSessionCount: int, subtitle: string}
     */
    public static function dashboardParticipation(int $userId, ?string $location): array
    {
        $location = trim((string) $location);
        if ($location === '' || mb_strtolower($location) === 'belum diatur') {
            return [
                'percent' => 0,
                'activeSessionCount' => 0,
                'participatingSessionCount' => 0,
                'subtitle' => 'Atur lokasi profil untuk melihat sesi terdekat',
            ];
        }

        $activeSessions = static::query()
            ->where('status', 'open')
            ->whereRaw('LOWER(TRIM(location)) = ?', [mb_strtolower($location)])
            ->where(fn (Builder $query) => $query
                ->whereNull('order_deadline')
                ->orWhere('order_deadline', '>', now()));

        $activeSessionCount = (clone $activeSessions)->count();
        $participatingSessionCount = (clone $activeSessions)
            ->where(fn (Builder $query) => $query
                ->where('host_id', $userId)
                ->orWhereHas('participants', fn (Builder $participants) => $participants
                    ->where('user_id', $userId)
                    ->where('status', 'joined')))
            ->count();

        $percent = $activeSessionCount > 0
            ? (int) round($participatingSessionCount / $activeSessionCount * 100)
            : 0;
        $subtitle = $activeSessionCount === 0
            ? 'Belum ada sesi aktif di lokasimu'
            : $participatingSessionCount.' dari '.$activeSessionCount.' sesi aktif diikuti';

        return [
            'percent' => $percent,
            'activeSessionCount' => $activeSessionCount,
            'participatingSessionCount' => $participatingSessionCount,
            'subtitle' => $subtitle,
        ];
    }

    public function loadHost(): self
    {
        $this->load('host:id,name,username,avatar');

        return $this;
    }

    public function paginateComments(int $perPage = 20): LengthAwarePaginator
    {
        return $this->comments()
            ->with('user:id,name,username,avatar')
            ->paginate($perPage);
    }

    public static function createForHost(User $host, array $attributes): self
    {
        return DB::transaction(function () use ($host, $attributes): self {
            $foodSession = static::query()->create(array_merge($attributes, ['host_id' => $host->id]));
            $foodSession->participants()->create([
                'user_id' => $host->id,
                'status' => 'joined',
            ]);

            return $foodSession;
        });
    }

    public function lockForUpdate(): self
    {
        return static::query()->lockForUpdate()->findOrFail($this->getKey());
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function hasJoined(int $userId): bool
    {
        return $this->participants()
            ->where('user_id', $userId)
            ->where('status', 'joined')
            ->exists();
    }

    public function joinedParticipantCount(): int
    {
        return $this->participants()
            ->where('status', 'joined')
            ->count();
    }

    public function addParticipant(int $userId, array $attributes): FoodSessionParticipant
    {
        return $this->participants()->create(array_merge($attributes, [
            'user_id' => $userId,
            'status' => 'joined',
        ]));
    }

    public function toggleLikeFor(int $userId): bool
    {
        $alreadyLiked = $this->likes()->where('users.id', $userId)->exists();
        if ($alreadyLiked) {
            $this->likes()->detach($userId);

            return false;
        }

        $this->likes()->attach($userId);

        return true;
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    public function addComment(int $userId, string $comment): FoodSessionComment
    {
        return $this->comments()->create([
            'user_id' => $userId,
            'comment' => $comment,
        ]);
    }

    public function getImageUrlAttribute(): ?string
    {
        $image = $this->attributes['image'] ?? null;
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'food-sessions/')) {
            return route('makan.sessions.images.show', ['fileName' => basename($image)]);
        }

        return $image;
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(FoodSessionParticipant::class, 'food_session_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'food_session_likes', 'food_session_id', 'user_id')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FoodSessionComment::class, 'food_session_id')->latest();
    }
}
