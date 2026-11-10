<?php

namespace App\Http\Controllers\Customer;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

use App\Http\Controllers\Controller;
use App\Models\Customer\NotificationModel as MainModel;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private $role;
    private $module;
    private $page;
    private $title;
    private $basePath;
    private $routePrefix;
    private $routeName;
    private $lastPageKey;
    private $alertKey;
    private $sessionNamespace;

    public function __construct()
    {
        $this->role             = 'customer';
        $this->module           = 'notifikasi';
        $this->page             = 'Pusat Notifikasi';
        $this->title            = 'Notifikasi Gotong Royong';
        $this->basePath         = 'customer.notifications';
        $this->routePrefix      = 'customer.notifikasi';
        $this->routeName        = 'customer.notifikasi.index';
        $this->lastPageKey      = 'customer_notif_last_page';
        $this->alertKey         = 'customer_notif_alert';
        $this->sessionNamespace = 'customer_notifikasi';
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            $authUser = User::first();
        }

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
            $followingIds = $authUser->following()->pluck('following_id');
            $query->whereIn('actor_id', $followingIds);
        }

        $allNotifs = $query->get();

        // Tandai notifikasi dibaca via MainModel
        MainModel::tandai_semua_dibaca($authUser->id);

        // Kelompokkan berdasarkan jangka waktu: Minggu Ini, Bulan Ini, Sebelumnya
        $grouped = [
            'this_week'  => $allNotifs->filter(fn ($n) => $n->created_at->diffInDays(now()) <= 7),
            'this_month' => $allNotifs->filter(fn ($n) => $n->created_at->diffInDays(now()) > 7 && $n->created_at->diffInDays(now()) <= 30),
            'earlier'    => $allNotifs->filter(fn ($n) => $n->created_at->diffInDays(now()) > 30),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'notifications' => $allNotifs,
                'unread_count'  => 0,
            ]);
        }

        $datakonten = [
            'judulcore'         => $this->title,
            'role'              => $this->role,
            'module'            => $this->module,
            'page'              => $this->page,
            'basePath'          => $this->basePath,
            'routePrefix'       => $this->routePrefix,
            'sessionNamespace'  => $this->sessionNamespace,
            'grouped'           => $grouped,
            'filter'            => $filter,
            'allNotifs'         => $allNotifs,
        ];

        return view('customer.notifications.index', $datakonten);
    }

    public function count()
    {
        $userId = Auth::id() ?: 1;
        $count = MainModel::total_notifikasi_belum_dibaca($userId);

        return response()->json(['unread_count' => $count]);
    }

    public function dropdown()
    {
        $authUser = Auth::user();
        if (!$authUser) {
            $authUser = User::first();
        }

        $notifs = Notification::with(['actor', 'post'])
            ->where('user_id', $authUser->id)
            ->latest()
            ->take(15)
            ->get();

        $unreadCount = MainModel::total_notifikasi_belum_dibaca($authUser->id);

        // Mark all as read when opening dropdown
        MainModel::tandai_semua_dibaca($authUser->id);

        return response()->json([
            'notifications' => $notifs->map(function ($n) {
                $actor = $n->actor;
                $post  = $n->post;
                return [
                    'id'             => $n->id,
                    'type'           => $n->type,
                    'message'        => $n->message,
                    'is_read'        => $n->is_read,
                    'time_ago'       => $n->created_at->diffForHumans(null, true, true),
                    'actor_id'       => $actor ? $actor->id : null,
                    'actor_name'     => $actor ? $actor->name : null,
                    'actor_username' => $actor ? ($actor->username ?? 'user_' . $actor->id) : null,
                    'actor_avatar'   => $actor ? $actor->avatar : null,
                    'actor_initials' => $actor ? strtoupper(substr($actor->name, 0, 2)) : '??',
                    'post_id'        => $post ? $post->id : null,
                    'post_image'     => $post ? $post->image : null,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }
}
