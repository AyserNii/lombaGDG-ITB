<?php

namespace App\Http\Controllers\Customer;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

use App\Http\Controllers\Controller;
use App\Models\Customer\ChatModel as MainModel;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
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
        $this->module           = 'chat';
        $this->page             = 'Pesan Masuk';
        $this->title            = 'Pesan & Obrolan Gotong Royong';
        $this->basePath         = 'customer.chat';
        $this->routePrefix      = 'customer.chat';
        $this->routeName        = 'customer.chat.index';
        $this->lastPageKey      = 'customer_chat_last_page';
        $this->alertKey         = 'customer_chat_alert';
        $this->sessionNamespace = 'customer_chat';
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            $authUser = User::first();
        }

        $activeUsername = $request->query('user');
        $activeUser = null;

        if ($activeUsername) {
            $activeUser = User::where('username', $activeUsername)
                ->orWhere('id', is_numeric($activeUsername) ? (int) $activeUsername : 0)
                ->first();
        }

        // Ambil kontak dari MainModel
        $rawContacts = MainModel::list_contacts($authUser->id);

        $contacts = collect($rawContacts)->map(function ($u) use ($authUser) {
            $lastMsg = MainModel::data_last_message($authUser->id, $u->id);
            $unreadCount = MainModel::total_unread_from_user($u->id, $authUser->id);

            $lastTime = '';
            if ($lastMsg && !empty($lastMsg->created_at)) {
                $lastTime = Carbon::parse($lastMsg->created_at)->diffForHumans(null, true, true);
            }

            return [
                'id'            => $u->id,
                'name'          => $u->name,
                'username'      => $u->username ?? 'user_' . $u->id,
                'avatar'        => $u->avatar,
                'initials'      => strtoupper(substr($u->name, 0, 2)),
                'last_message'  => $lastMsg ? $lastMsg->message : 'Mulai percakapan baru...',
                'is_outgoing'   => $lastMsg ? $lastMsg->sender_id === $authUser->id : false,
                'last_time'     => $lastTime,
                'unread'        => $unreadCount > 0,
                'note'          => $u->note,
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

            MainModel::tandai_dibaca($authUser->id, $activeUser->id);
        }

        // Status notes tray (friends notes like Foto 3)
        $notes = MainModel::list_notes_friends($authUser->id, 8);

        $datakonten = [
            'judulcore'         => $this->title,
            'role'              => $this->role,
            'module'            => $this->module,
            'page'              => $this->page,
            'basePath'          => $this->basePath,
            'routePrefix'       => $this->routePrefix,
            'sessionNamespace'  => $this->sessionNamespace,
            'authUser'          => $authUser,
            'activeUser'        => $activeUser,
            'contacts'          => $contacts,
            'messages'          => $messages,
            'notes'             => $notes,
        ];

        return view('customer.chat.index', $datakonten);
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'nullable|string',
            'media_url'   => 'nullable|string',
        ]);

        $authUser = Auth::user();

        if (empty(trim($request->message)) && empty($request->media_url)) {
            return back();
        }

        $message = MainModel::tambah_pesan(
            $authUser->id,
            $request->receiver_id,
            $request->message,
            $request->media_url
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'          => $message->id,
                    'text'        => $message->message,
                    'media_url'   => $message->media_url,
                    'created_at'  => Carbon::parse($message->created_at)->format('H:i'),
                    'is_outgoing' => true,
                ],
            ]);
        }

        $receiver = User::find($request->receiver_id);
        return redirect()->route('chat', ['user' => $receiver->username ?? $receiver->id]);
    }
}
