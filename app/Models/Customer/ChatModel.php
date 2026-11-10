<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChatModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'messages';

    public static function list_contacts($userId)
    {
        // Kontak yang pernah bertukar pesan atau di-follow
        $chatted = DB::table('messages')
            ->where('sender_id', $userId)
            ->select('receiver_id as user_id')
            ->union(
                DB::table('messages')
                    ->where('receiver_id', $userId)
                    ->select('sender_id as user_id')
            )
            ->union(
                DB::table('follows')
                    ->where('follower_id', $userId)
                    ->select('following_id as user_id')
            )
            ->union(
                DB::table('follows')
                    ->where('following_id', $userId)
                    ->select('follower_id as user_id')
            );

        $subquery = DB::table(DB::raw("({$chatted->toSql()}) as contacts"))
            ->mergeBindings($chatted)
            ->join('users', 'users.id', '=', 'contacts.user_id')
            ->where('users.id', '!=', $userId)
            ->select('users.*');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->distinct()
            ->get();

        return $query;
    }

    public static function data_last_message($user1, $user2)
    {
        $subquery = DB::table('messages')
            ->where(function ($q) use ($user1, $user2) {
                $q->where('sender_id', $user1)->where('receiver_id', $user2);
            })
            ->orWhere(function ($q) use ($user1, $user2) {
                $q->where('sender_id', $user2)->where('receiver_id', $user1);
            })
            ->orderBy('id', 'DESC')
            ->limit(1);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->first();

        return $query;
    }

    public static function total_unread_from_user($fromUserId, $toUserId)
    {
        $query = DB::table('messages')
            ->where('sender_id', $fromUserId)
            ->where('receiver_id', $toUserId)
            ->where('is_read', false)
            ->select(DB::raw('COUNT(id) as total'))
            ->first();

        return $query ? (int) $query->total : 0;
    }

    public static function list_messages($user1, $user2)
    {
        $subquery = DB::table('messages')
            ->where(function ($q) use ($user1, $user2) {
                $q->where('sender_id', $user1)->where('receiver_id', $user2);
            })
            ->orWhere(function ($q) use ($user1, $user2) {
                $q->where('sender_id', $user2)->where('receiver_id', $user1);
            })
            ->orderBy('id', 'ASC');

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }

    public static function tandai_dibaca($receiverId, $senderId)
    {
        return DB::table('messages')
            ->where('receiver_id', $receiverId)
            ->where('sender_id', $senderId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public static function tambah_pesan($senderId, $receiverId, $message, $mediaUrl = null)
    {
        $id = DB::table('messages')->insertGetId([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message ?? '',
            'media_url' => $mediaUrl,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('messages')->where('id', $id)->first();
    }

    public static function list_notes_friends($userId, $limit = 8)
    {
        $subquery = DB::table('users')
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->where('id', '!=', $userId)
            ->limit($limit);

        $query = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
            ->mergeBindings($subquery)
            ->select(DB::raw('*'))
            ->get();

        return $query;
    }
}

