<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengaturanModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    public static function data_user($userId)
    {
        return DB::table('users')->where('id', $userId)->first();
    }

    public static function cek_username($username, $excludeUserId)
    {
        return DB::table('users')
            ->where('username', $username)
            ->where('id', '!=', $excludeUserId)
            ->exists();
    }

    public static function perbarui_profil($userId, array $data)
    {
        $data['updated_at'] = now();
        return DB::table('users')->where('id', $userId)->update($data);
    }

    public static function perbarui_password($userId, $hashedPassword)
    {
        return DB::table('users')->where('id', $userId)->update([
            'password' => $hashedPassword,
            'updated_at' => now(),
        ]);
    }
}

