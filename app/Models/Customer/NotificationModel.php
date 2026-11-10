<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificationModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'notifications';

    public static function list_notifikasi($userId, $filter = 'all', $limit = 50)
    {
        $builder = DB::table('notifications')
            ->leftJoin('users as actor', 'actor.id', '=', 'notifications.actor_id')
            ->leftJoin('posts', 'posts.id', '=', 'notifications.post_id')
            ->where('notifications.user_id', $userId)
            ->select(
                'notifications.*',
                'actor.name as actor_name',
                'actor.username as actor_username',
                'actor.avatar as actor_avatar',
                'posts.image as post_image'
            )
            ->orderBy('notifications.id', 'DESC')
            ->limit($limit);

        if ($filter === 'follows') {
            $builder->where('notifications.type', 'follow');
        } elseif ($filter === 'comments') {
            $builder->where('notifications.type', 'comment');
        } elseif ($filter === 'likes') {
            $builder->where('notifications.type', 'like');
        } elseif ($filter === 'following') {
            $subFollowing = DB::table('follows')
                ->where('follower_id', $userId)
                ->select('following_id');
            $builder->whereIn('notifications.actor_id', $subFollowing);
        }

        $query = DB::table(DB::raw("({$builder->toSql()}) as subquery"))
            ->mergeBindings($builder)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function total_notifikasi_belum_dibaca($userId)
    {
        $query = DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->select(DB::raw('COUNT(id) as total'))
            ->first();

        return $query ? (int) $query->total : 0;
    }

    public static function tandai_semua_dibaca($userId)
    {
        return DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'updated_at' => now(),
            ]);
    }

    public static function tambah_notifikasi($userId, $actorId, $type, $message, $postId = null)
    {
        $id = DB::table('notifications')->insertGetId([
            'user_id' => $userId,
            'actor_id' => $actorId,
            'type' => $type,
            'message' => $message,
            'post_id' => $postId,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('notifications')->where('id', $id)->first();
    }

    public static function hapus_notifikasi($id, $userId)
    {
        return DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();
    }
}

