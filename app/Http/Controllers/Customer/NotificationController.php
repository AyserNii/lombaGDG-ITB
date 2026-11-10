<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();

        $filter = $request->query('filter', 'all');

        $query = Notification::with(['actor', 'post'])
            ->where('user_id', $authUser->id)
            ->latest();

        if ($filter === 'follows') {
            $query->where('type', 'follow');
        } elseif ($filter === 'comments') {
            $query->where('type', 'comment');
        } elseif ($filter === 'likes') {
            $query->where('type', 'like');
        } elseif ($filter === 'following') {
            // Notifications from users the authUser follows
            $followingIds = $authUser->following()->pluck('following_id');
            $query->whereIn('actor_id', $followingIds);
        }

        $allNotifs = $query->get();

        // Mark unread as read
        Notification::where('user_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Group by time frame: This week, This month, Earlier
        $grouped = [
            'this_week' => $allNotifs->filter(fn ($n) => $n->created_at->diffInDays(now()) <= 7),
            'this_month' => $allNotifs->filter(fn ($n) => $n->created_at->diffInDays(now()) > 7 && $n->created_at->diffInDays(now()) <= 30),
            'earlier' => $allNotifs->filter(fn ($n) => $n->created_at->diffInDays(now()) > 30),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'notifications' => $allNotifs,
                'unread_count' => 0,
            ]);
        }

        return view('customer.notifications.index', compact('grouped', 'filter', 'allNotifs'));
    }

    public function count()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function dropdown()
    {
        $authUser = Auth::user();

        $notifs = Notification::with(['actor', 'post'])
            ->where('user_id', $authUser->id)
            ->latest()
            ->take(15)
            ->get();

        $unreadCount = Notification::where('user_id', $authUser->id)
            ->where('is_read', false)
            ->count();

        // Mark all as read when opening dropdown
        Notification::where('user_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'notifications' => $notifs->map(function ($n) {
                $actor = $n->actor;
                $post  = $n->post;
                return [
                    'id'          => $n->id,
                    'type'        => $n->type,
                    'message'     => $n->message,
                    'is_read'     => $n->is_read,
                    'time_ago'    => $n->created_at->diffForHumans(null, true, true),
                    'actor_id'    => $actor ? $actor->id : null,
                    'actor_name'  => $actor ? $actor->name : null,
                    'actor_username' => $actor ? ($actor->username ?? 'user_' . $actor->id) : null,
                    'actor_avatar' => $actor ? $actor->avatar : null,
                    'actor_initials' => $actor ? strtoupper(substr($actor->name, 0, 2)) : '??',
                    'post_image'  => $post ? $post->image : null,
                    'post_id'     => $post ? $post->id : null,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }
}

