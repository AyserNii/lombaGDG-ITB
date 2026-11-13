<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoryModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'stories';

    public static function tambah_story($userId, $image, $caption = null)
    {
        $id = DB::table('stories')->insertGetId([
            'user_id' => $userId,
            'image' => $image,
            'caption' => $caption,
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('stories')->where('id', $id)->first();
    }

    public static function list_stories_aktif($userId = null)
    {
        $builder = DB::table('stories')
            ->join('users', 'users.id', '=', 'stories.user_id')
            ->where('stories.expires_at', '>', now())
            ->select('stories.*', 'users.name as user_name', 'users.username as user_username', 'users.avatar as user_avatar')
            ->orderBy('stories.id', 'DESC');

        if ($userId) {
            $builder->where('stories.user_id', $userId);
        }

        $query = DB::table(DB::raw("({$builder->toSql()}) as subquery"))
            ->mergeBindings($builder)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function tambah_highlight($userId, $title, array $storyIds, ?string $thumbnailPath = null, ?int $thumbnailStoryId = null)
    {
        return DB::transaction(function () use ($userId, $title, $storyIds, $thumbnailPath, $thumbnailStoryId) {
            $highlightId = DB::table('story_highlights')->insertGetId([
                'user_id' => $userId,
                'title' => $title,
                'thumbnail_path' => $thumbnailPath,
                'thumbnail_story_id' => $thumbnailStoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($storyIds as $sId) {
                DB::table('story_highlight_stories')->insert([
                    'story_highlight_id' => $highlightId,
                    'story_id' => $sId,
                ]);
            }

            return $highlightId;
        });
    }

    public static function list_highlights($userId)
    {
        $subquery = DB::table('story_highlights')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }
}
