<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PostModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function cek_post($idpost)
    {
        $query = DB::table('posts')
            ->where('id', $idpost)
            ->first();

        return $query;
    }

    public static function total_post($limit = 1000)
    {
        $subquery = DB::table('posts')
            ->select(DB::raw('id'))
            ->limit($limit);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('COUNT(subquery.id) AS total'))
            ->first();

        return $query;
    }

    public static function list_post($limit, $offset)
    {
        $subquery = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select(DB::raw('posts.id, posts.user_id, posts.caption, posts.image, posts.type, posts.likes_count, posts.comments_count, posts.created_at, users.name, users.username, users.avatar'))
            ->orderBy('posts.id', 'DESC')
            ->limit($limit)
            ->offset($offset);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function total_postcari($cari, $limit = 1000)
    {
        $subquery = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select(DB::raw('posts.id'))
            ->where(function ($query) use ($cari) {
                $query->where('posts.caption', 'LIKE', '%'.$cari.'%')
                    ->orWhere('users.name', 'LIKE', '%'.$cari.'%')
                    ->orWhere('users.username', 'LIKE', '%'.$cari.'%');
            })
            ->limit($limit);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('COUNT(subquery.id) AS total'))
            ->first();

        return $query;
    }

    public static function list_postcari($cari, $limit, $offset)
    {
        $subquery = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select(DB::raw('posts.id, posts.user_id, posts.caption, posts.image, posts.type, posts.likes_count, posts.comments_count, posts.created_at, users.name, users.username, users.avatar'))
            ->where(function ($query) use ($cari) {
                $query->where('posts.caption', 'LIKE', '%'.$cari.'%')
                    ->orWhere('users.name', 'LIKE', '%'.$cari.'%')
                    ->orWhere('users.username', 'LIKE', '%'.$cari.'%');
            })
            ->orderBy('posts.id', 'DESC')
            ->limit($limit)
            ->offset($offset);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function data_post($idpost)
    {
        $query = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select(DB::raw('posts.id, posts.user_id, posts.caption, posts.image, posts.type, posts.likes_count, posts.comments_count, posts.created_at, users.name, users.username, users.avatar'))
            ->where('posts.id', $idpost)
            ->first();

        return $query;
    }

    public static function tambah_post($userid, $caption, $image, $type = 'post')
    {
        $id = DB::table('posts')->insertGetId([
            'user_id' => $userid,
            'caption' => $caption,
            'image' => $image,
            'type' => $type,
            'likes_count' => 0,
            'comments_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (object) [
            'id_post' => $id,
        ];
    }

    public static function perbarui_post($idpost, $caption)
    {
        DB::table('posts')
            ->where('id', $idpost)
            ->update([
                'caption' => $caption,
                'updated_at' => now(),
            ]);

        return true;
    }

    public static function hapus_post($idpost)
    {
        DB::table('comments')->where('post_id', $idpost)->delete();
        DB::table('post_likes')->where('post_id', $idpost)->delete();
        DB::table('saved_posts')->where('post_id', $idpost)->delete();
        DB::table('posts')->where('id', $idpost)->delete();

        return true;
    }

    public static function cek_like($idpost, $iduser)
    {
        return DB::table('post_likes')
            ->where('post_id', $idpost)
            ->where('user_id', $iduser)
            ->exists();
    }

    public static function tambah_like($idpost, $iduser)
    {
        return self::set_reaction($idpost, $iduser, 'like');
    }

    public static function hapus_like($idpost, $iduser)
    {
        return self::set_reaction($idpost, $iduser, null);
    }

    public static function set_reaction($idpost, $iduser, ?string $reaction): array
    {
        return DB::transaction(function () use ($idpost, $iduser, $reaction) {
            $existing = DB::table('post_likes')
                ->where('post_id', $idpost)
                ->where('user_id', $iduser)
                ->first();

            if ($existing?->reaction === $reaction) {
                $reaction = null;
            }

            if ($reaction === null) {
                DB::table('post_likes')
                    ->where('post_id', $idpost)
                    ->where('user_id', $iduser)
                    ->delete();
            } else {
                DB::table('post_likes')->updateOrInsert(
                    ['post_id' => $idpost, 'user_id' => $iduser],
                    ['reaction' => $reaction, 'created_at' => $existing->created_at ?? now(), 'updated_at' => now()]
                );
            }

            $counts = self::reaction_counts($idpost);
            DB::table('posts')->where('id', $idpost)->update(['likes_count' => $counts['likes_count']]);

            return [
                'reaction' => $reaction,
                ...$counts,
            ];
        });
    }

    public static function reaction_counts($idpost): array
    {
        $counts = DB::table('post_likes')
            ->where('post_id', $idpost)
            ->selectRaw("SUM(CASE WHEN reaction = 'like' THEN 1 ELSE 0 END) AS likes_count")
            ->selectRaw("SUM(CASE WHEN reaction = 'dislike' THEN 1 ELSE 0 END) AS dislikes_count")
            ->first();

        return [
            'likes_count' => (int) ($counts->likes_count ?? 0),
            'dislikes_count' => (int) ($counts->dislikes_count ?? 0),
        ];
    }

    public static function reaction_by_user($idpost, $iduser): ?string
    {
        return DB::table('post_likes')
            ->where('post_id', $idpost)
            ->where('user_id', $iduser)
            ->value('reaction');
    }

    public static function cek_saved($idpost, $iduser)
    {
        return DB::table('saved_posts')
            ->where('post_id', $idpost)
            ->where('user_id', $iduser)
            ->exists();
    }

    public static function list_komentar($idpost)
    {
        return DB::table('comments')
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->select(DB::raw('comments.id, comments.post_id, comments.user_id, comments.parent_id, comments.body, comments.created_at, users.name, users.username, users.avatar'))
            ->where('comments.post_id', $idpost)
            ->whereNull('comments.parent_id')
            ->orderBy('comments.id', 'DESC')
            ->get();
    }

    public static function list_balasankomentar($idparent)
    {
        return DB::table('comments')
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->select(DB::raw('comments.id, comments.post_id, comments.user_id, comments.parent_id, comments.body, comments.created_at, users.name, users.username, users.avatar'))
            ->where('comments.parent_id', $idparent)
            ->orderBy('comments.id', 'ASC')
            ->get();
    }

    public static function total_komentar($idpost)
    {
        return DB::table('comments')
            ->where('post_id', $idpost)
            ->count();
    }

    public static function data_komentar($idcomment)
    {
        return DB::table('comments')
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->select(DB::raw('comments.id, comments.post_id, comments.user_id, comments.parent_id, comments.body, comments.created_at, users.name, users.username, users.avatar'))
            ->where('comments.id', $idcomment)
            ->first();
    }

    public static function tambah_komentar($idpost, $iduser, $body, $parentid = null)
    {
        $id = DB::table('comments')->insertGetId([
            'post_id' => $idpost,
            'user_id' => $iduser,
            'parent_id' => $parentid,
            'body' => $body,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('posts')->where('id', $idpost)->increment('comments_count');

        return (object) [
            'id_comment' => $id,
        ];
    }

    public static function hapus_komentar($idcomment)
    {
        $c = DB::table('comments')->where('id', $idcomment)->first();
        if ($c) {
            DB::table('comments')->where('parent_id', $idcomment)->delete();
            DB::table('comments')->where('id', $idcomment)->delete();

            $newCount = DB::table('comments')->where('post_id', $c->post_id)->count();
            DB::table('posts')->where('id', $c->post_id)->update(['comments_count' => $newCount]);

            return (object) [
                'post_id' => $c->post_id,
                'comments_count' => $newCount,
            ];
        }

        return false;
    }
}
