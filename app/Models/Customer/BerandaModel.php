<?php

namespace App\Models\Customer;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BerandaModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    public static function cek_login($id)
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

    public static function data_ringkasan($userId)
    {
        $totalPost = DB::table('posts')->where('user_id', $userId)->count();
        $totalSesi = 3;
        $totalNotif = DB::table('notifications')->where('user_id', $userId)->where('is_read', false)->count();

        return (object)[
            'total_post' => $totalPost,
            'total_sesi' => $totalSesi,
            'total_notif' => $totalNotif,
        ];
    }

    public static function community_posts($limit = 3)
    {
        return Post::with('user')
            ->orderByDesc('likes_count')
            ->latest()
            ->take($limit)
            ->get();
    }
}

