<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BerandaModel extends Model
{
    use HasFactory;
    public $timestamps = false;

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
}

