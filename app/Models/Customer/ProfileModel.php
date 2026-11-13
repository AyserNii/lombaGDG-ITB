<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProfileModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'users';

    public static function data_user_by_username($username)
    {
        $subquery = DB::table('users')
            ->where('username', $username)
            ->orWhere('id', is_numeric($username) ? (int) $username : 0)
            ->limit(1);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->first();

        return $query;
    }

    public static function cek_login($username, $password)
    {
        $subquery = DB::table('users')
            ->where(function ($query) use ($username) {
                $query->where('username', $username)
                    ->orWhere('email', $username);
            })
            ->where('password', md5($password))
            ->limit(1);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->first();

        return $query;
    }

    public static function data_user_by_id($id)
    {
        $subquery = DB::table('users')
            ->where('id', $id)
            ->limit(1);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->first();

        return $query;
    }

    public static function list_posts_by_user($userId)
    {
        $subquery = DB::table('posts')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function list_saved_posts_by_user($userId)
    {
        $subquery = DB::table('saved_posts')
            ->join('posts', 'posts.id', '=', 'saved_posts.post_id')
            ->where('saved_posts.user_id', $userId)
            ->orderBy('saved_posts.id', 'DESC')
            ->select('posts.*');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function total_posts_by_user($userId)
    {
        $query = DB::table('posts')
            ->where('user_id', $userId)
            ->select(DB::raw('COUNT(id) as total'))
            ->first();

        return $query ? (int) $query->total : 0;
    }

    public static function total_followers_by_user($userId)
    {
        $query = DB::table('follows')
            ->where('following_id', $userId)
            ->select(DB::raw('COUNT(id) as total'))
            ->first();

        return $query ? (int) $query->total : 0;
    }

    public static function total_following_by_user($userId)
    {
        $query = DB::table('follows')
            ->where('follower_id', $userId)
            ->select(DB::raw('COUNT(id) as total'))
            ->first();

        return $query ? (int) $query->total : 0;
    }

    public static function cek_following($followerId, $followingId)
    {
        $query = DB::table('follows')
            ->where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        return $query !== null;
    }

    public static function tambah_following($followerId, $followingId)
    {
        return DB::table('follows')->insert([
            'follower_id' => $followerId,
            'following_id' => $followingId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function hapus_following($followerId, $followingId)
    {
        return DB::table('follows')
            ->where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->delete();
    }

    public static function cek_saved($userId, $postId)
    {
        $query = DB::table('saved_posts')
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        return $query !== null;
    }

    public static function tambah_saved($userId, $postId)
    {
        return DB::table('saved_posts')->insert([
            'user_id' => $userId,
            'post_id' => $postId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function hapus_saved($userId, $postId)
    {
        return DB::table('saved_posts')
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->delete();
    }

    public static function perbarui_note($userId, $note)
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update([
                'note' => $note,
                'updated_at' => now(),
            ]);
    }

    public static function list_followers($userId)
    {
        $subquery = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.follower_id')
            ->where('follows.following_id', $userId)
            ->select('users.*')
            ->orderBy('follows.id', 'DESC');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function list_following($userId)
    {
        $subquery = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.following_id')
            ->where('follows.follower_id', $userId)
            ->select('users.*')
            ->orderBy('follows.id', 'DESC');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function list_highlights($userId)
    {
        return DB::table('story_highlights')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get();
    }

    public static function list_stories_archived($userId)
    {
        return DB::table('stories')
            ->leftJoin('story_highlight_stories', 'story_highlight_stories.story_id', '=', 'stories.id')
            ->where('stories.user_id', $userId)
            ->whereNull('story_highlight_stories.id')
            ->orderBy('stories.id', 'DESC')
            ->select('stories.*')
            ->get();
    }
}

