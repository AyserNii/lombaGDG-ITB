<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();

        // Target user from query or default to last interacted contact (or Jall / fat1gued_)
        $activeUsername = $request->query('user');
        $activeUser = null;

        if ($activeUsername) {
            $activeUser = User::where('username', $activeUsername)
                ->orWhere('id', is_numeric($activeUsername) ? (int)$activeUsername : 0)
                ->first();
        }

        // Get all contacts authUser has exchanged messages with OR followers/following
        $chattedUserIds = Message::where('sender_id', $authUser->id)
            ->pluck('receiver_id')
            ->merge(
                Message::where('receiver_id', $authUser->id)->pluck('sender_id')
            )
            ->unique();

        // Also merge followers/following so user can start chatting with anyone they follow
        $socialUserIds = $authUser->following()->pluck('following_id')
            ->merge($authUser->followers()->pluck('follower_id'));

        $contactIds = $chattedUserIds->merge($socialUserIds)->unique()->filter(fn ($id) => $id !== $authUser->id);

        $contacts = User::whereIn('id', $contactIds)->get()->map(function ($u) use ($authUser) {
            // Find latest message between authUser and $u
            $lastMsg = Message::where(function ($q) use ($authUser, $u) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $u->id);
            })->orWhere(function ($q) use ($authUser, $u) {
                $q->where('sender_id', $u->id)->where('receiver_id', $authUser->id);
            })->latest()->first();

            $unreadCount = Message::where('sender_id', $u->id)
                ->where('receiver_id', $authUser->id)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? 'user_' . $u->id,
                'avatar' => $u->avatar,
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'last_message' => $lastMsg ? $lastMsg->message : 'Mulai percakapan baru...',
                'is_outgoing' => $lastMsg ? $lastMsg->sender_id === $authUser->id : false,
                'last_time' => $lastMsg ? $lastMsg->created_at->diffForHumans(null, true, true) : '',
                'unread' => $unreadCount > 0,
                'note' => $u->note,
            ];
        });

        // Set active user if not yet chosen
        if (!$activeUser) {
            if ($contacts->isNotEmpty()) {
                $firstContactId = $contacts->first()['id'];
                $activeUser = User::find($firstContactId);
            } else {
                $activeUser = User::where('id', '!=', $authUser->id)->first();
            }
        }

        // Messages with activeUser
        $messages = collect();
        if ($activeUser) {
            $messages = Message::where(function ($q) use ($authUser, $activeUser) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $activeUser->id);
            })->orWhere(function ($q) use ($authUser, $activeUser) {
                $q->where('sender_id', $activeUser->id)->where('receiver_id', $authUser->id);
            })->orderBy('created_at', 'asc')->get();

            // Mark received messages as read
            Message::where('receiver_id', $authUser->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        // Status notes tray (friends notes like Foto 3)
        $notes = User::whereNotNull('note')
            ->where('id', '!=', $authUser->id)
            ->take(8)
            ->get();

        return view('customer.chat.index', compact('authUser', 'activeUser', 'contacts', 'messages', 'notes'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'media_url' => 'nullable|string',
        ]);

        $authUser = Auth::user();

        if (empty(trim($request->message)) && empty($request->media_url)) {
            return back();
        }

        $message = Message::create([
            'sender_id' => $authUser->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message ?? '',
            'media_url' => $request->media_url,
            'is_read' => false,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'text' => $message->message,
                    'media_url' => $message->media_url,
                    'created_at' => $message->created_at->format('H:i'),
                    'is_outgoing' => true,
                ],
            ]);
        }

        $receiver = User::find($request->receiver_id);
        return redirect()->route('chat', ['user' => $receiver->username ?? $receiver->id]);
    }
}

