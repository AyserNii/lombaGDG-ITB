<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Customer\PostModel as MainModel;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

class PostController extends Controller
{
    private $role = 'customer';
    private $module = 'komunitas';
    private $page = 'posts';
    private $title = 'Postingan Komunitas';

    private $sessionNamespace;
    private $basePath;
    private $routePrefix;
    private $routeName;
    private $lastPageKey;
    private $alertKey;

    public function __construct()
    {
        $this->routeName = '/' . $this->role . '/' . $this->module . '/' . $this->page;
        $this->basePath = $this->role . '.' . $this->module . '.';
        $this->routePrefix = $this->role . '.' . $this->module . '.' . $this->page . '.';
        $this->alertKey = 'notifikasi' . $this->role . $this->module;
        $this->lastPageKey = 'url' . $this->role . $this->module . $this->page;
        $this->sessionNamespace = 'sesi' . $this->role . $this->module;
    }

    public function list(Request $request, $halaman = '')
    {
        if (!empty($halaman) && !filter_var($halaman, FILTER_VALIDATE_INT)) {
            return redirect()->route($this->routePrefix . 'list');
        } else if (!empty($halaman) && ($halaman == 1 || $halaman < 1)) {
            return redirect()->route($this->routePrefix . 'list');
        } else if (empty($halaman)) {
            $halaman = 1;
        }

        $limit = 12;
        $offset = ($halaman - 1) * $limit;
        $makshalaman = 100;
        $maksoffset = $limit * $makshalaman;
        if ($offset > $maksoffset) {
            $offset = $maksoffset;
        }

        $totalpost = MainModel::total_post($maksoffset);
        if ($totalpost && $totalpost->total > 0) {
            $listpost = MainModel::list_post($limit, $offset);
        } else {
            $listpost = collect();
        }

        $hasilpost = new LengthAwarePaginator(
            $listpost,
            $totalpost->total ?? 0,
            $limit,
            $halaman
        );

        $hasilpost->withPath($this->routeName);

        $datakonten['judulcore'] = $this->title;
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = "";
        $datakonten['parameterdata'] = "";
        $datakonten['datapost'] = $hasilpost;

        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('community', $datakonten);
    }

    public function caridata(Request $request): RedirectResponse
    {
        $cari = $request->input('cari', '');
        $carirubah = str_replace('/', '+', $cari);
        $carirubah = preg_split('/\s+/', $carirubah);
        $carirubah = implode('-', $carirubah);

        if (empty($cari)) {
            $pesan = "Kolom cari tidak boleh kosong!";
            return redirect()->back()->with([$this->alertKey => 'error', 'pesan' => $pesan]);
        }

        return redirect()->route($this->routePrefix . 'cari', ['cari' => $carirubah])->withInput();
    }

    public function cari(Request $request, $cari = '', $halaman = '')
    {
        $cari = str_replace('+', '/', $cari);
        $cari = str_replace('-', ' ', $cari);
        $carirubah = str_replace('/', '+', $cari);
        $carirubah = preg_split('/\s+/', $carirubah);
        $carirubah = implode('-', $carirubah);

        if (!empty($halaman) && !filter_var($halaman, FILTER_VALIDATE_INT)) {
            return redirect()->route($this->routePrefix . 'cari', ['cari' => $carirubah]);
        } else if (!empty($halaman) && ($halaman == 1 || $halaman < 1)) {
            return redirect()->route($this->routePrefix . 'cari', ['cari' => $carirubah]);
        } else if (empty($halaman)) {
            $halaman = 1;
        }

        $limit = 12;
        $offset = ($halaman - 1) * $limit;
        $makshalaman = 100;
        $maksoffset = $limit * $makshalaman;
        if ($offset > $maksoffset) {
            $offset = $maksoffset;
        }

        $totalpost = MainModel::total_postcari($cari, $maksoffset);
        if ($totalpost && $totalpost->total > 0) {
            $listpost = MainModel::list_postcari($cari, $limit, $offset);
        } else {
            $listpost = collect();
        }

        $hasilpost = new LengthAwarePaginator(
            $listpost,
            $totalpost->total ?? 0,
            $limit,
            $halaman
        );

        $hasilpost->withPath($this->routeName . '/cari/' . $carirubah);

        $datakonten['judulcore'] = $this->title;
        $datakonten['menucore'] = $this->routePrefix . "list";
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = $cari;
        $datakonten['parameterdata'] = "";
        $datakonten['datapost'] = $hasilpost;

        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('community', $datakonten);
    }

    public function show(int $id)
    {
        $authUser = Auth::user();
        $post = Post::with(['user'])->findOrFail($id);

        $isLiked = $authUser ? MainModel::cek_like($post->id, $authUser->id) : false;
        $isSaved = $authUser ? MainModel::cek_saved($post->id, $authUser->id) : false;
        $isFollowingAuthor = $authUser && $post->user_id !== $authUser->id ? $authUser->isFollowing($post->user_id) : false;

        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get()
            ->map(function ($c) use ($authUser, $post) {
                return $this->formatComment($c, $authUser, $post);
            });

        return response()->json([
            'success' => true,
            'post' => [
                'id' => $post->id,
                'caption' => $post->caption,
                'image' => $post->image,
                'type' => $post->type,
                'likes_count' => $post->likes_count,
                'comments_count' => MainModel::total_komentar($post->id),
                'is_liked' => $isLiked,
                'is_saved' => $isSaved,
                'created_at' => $post->created_at->diffForHumans(),
                'created_date' => $post->created_at->translatedFormat('d F Y · H:i'),
                'author_id' => $post->user_id,
                'author_name' => $post->user->name ?? 'User',
                'author_username' => $post->user->username ?? 'user_' . $post->user_id,
                'author_avatar' => $post->user->avatar ?? null,
                'author_initials' => strtoupper(substr($post->user->name ?? 'U', 0, 2)),
                'is_following_author' => $isFollowingAuthor,
                'is_own_post' => $authUser ? ($post->user_id === $authUser->id) : false,
            ],
            'comments' => $comments,
        ]);
    }

    public function destroy(int $id)
    {
        $authUser = Auth::user();
        $post = MainModel::cek_post($id);

        if (!$post) {
            return response()->json(['success' => false, 'error' => 'Postingan tidak ditemukan.'], 404);
        }

        if ($post->user_id !== $authUser->id) {
            return response()->json(['success' => false, 'error' => 'Tidak memiliki izin.'], 403);
        }

        MainModel::hapus_post($id);

        return response()->json([
            'success' => true,
            'message' => 'Postingan berhasil dihapus.'
        ]);
    }

    public function toggleLike(Request $request, int $id)
    {
        $authUser = Auth::user();
        $post = MainModel::cek_post($id);
        if (!$post) {
            return response()->json(['success' => false, 'error' => 'Postingan tidak ditemukan.'], 404);
        }

        $isLiked = MainModel::cek_like($id, $authUser->id);

        if ($isLiked) {
            MainModel::hapus_like($id, $authUser->id);
            $liked = false;
        } else {
            MainModel::tambah_like($id, $authUser->id);
            $liked = true;

            // Notifikasi ke author jika bukan postingan sendiri
            if ($post->user_id !== $authUser->id) {
                Notification::create([
                    'user_id' => $post->user_id,
                    'actor_id' => $authUser->id,
                    'type' => 'like',
                    'message' => '@' . ($authUser->username ?? $authUser->name) . ' menyukai postingan Anda.',
                    'post_id' => $post->id,
                    'is_read' => false,
                ]);
            }
        }

        $freshPost = MainModel::cek_post($id);
        $likesCount = $freshPost->likes_count ?? 0;

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }

    public function getComments(int $id)
    {
        $authUser = Auth::user();
        $post = Post::with('user')->findOrFail($id);

        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get()
            ->map(function ($c) use ($authUser, $post) {
                return $this->formatComment($c, $authUser, $post);
            });

        return response()->json([
            'success' => true,
            'comments' => $comments,
            'comments_count' => MainModel::total_komentar($post->id),
            'post' => [
                'id' => $post->id,
                'caption' => $post->caption,
                'image' => $post->image,
                'type' => $post->type,
                'created_at' => $post->created_at->diffForHumans(),
                'author_name' => $post->user->name ?? 'User',
                'author_username' => $post->user->username ?? 'user_' . $post->user_id,
                'author_avatar' => $post->user->avatar ?? null,
                'author_initials' => strtoupper(substr($post->user->name ?? 'U', 0, 2)),
            ]
        ]);
    }

    public function storeComment(Request $request, int $id)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $authUser = Auth::user();
        $post = MainModel::cek_post($id);
        if (!$post) {
            return response()->json(['success' => false, 'error' => 'Postingan tidak ditemukan.'], 404);
        }

        $res = MainModel::tambah_komentar($id, $authUser->id, trim($request->body), $request->parent_id);
        $comment = Comment::with(['user', 'replies.user'])->find($res->id_comment);
        $postModel = Post::find($id);

        // Notifikasi komentar
        if ($request->parent_id) {
            $parent = MainModel::data_komentar($request->parent_id);
            if ($parent && $parent->user_id !== $authUser->id) {
                Notification::create([
                    'user_id' => $parent->user_id,
                    'actor_id' => $authUser->id,
                    'type' => 'comment',
                    'message' => '@' . ($authUser->username ?? $authUser->name) . ' membalas komentar Anda: "' . Str::limit($comment->body, 30) . '"',
                    'post_id' => $id,
                    'is_read' => false,
                ]);
            }
        } elseif ($post->user_id !== $authUser->id) {
            Notification::create([
                'user_id' => $post->user_id,
                'actor_id' => $authUser->id,
                'type' => 'comment',
                'message' => '@' . ($authUser->username ?? $authUser->name) . ' mengomentari postingan Anda: "' . Str::limit($comment->body, 30) . '"',
                'post_id' => $id,
                'is_read' => false,
            ]);
        }

        $commentsCount = MainModel::total_komentar($id);

        return response()->json([
            'success' => true,
            'comment' => $this->formatComment($comment, $authUser, $postModel),
            'comments_count' => $commentsCount,
        ]);
    }

    public function destroyComment(int $commentId)
    {
        $authUser = Auth::user();
        $comment = MainModel::data_komentar($commentId);

        if (!$comment) {
            return response()->json(['success' => false, 'error' => 'Komentar tidak ditemukan.'], 404);
        }

        $post = MainModel::cek_post($comment->post_id);

        // Hanya pemilik komentar atau pemilik postingan yang boleh menghapus
        if ($comment->user_id !== $authUser->id && (!$post || $post->user_id !== $authUser->id)) {
            return response()->json(['success' => false, 'error' => 'Tidak memiliki izin menghapus komentar ini.'], 403);
        }

        $result = MainModel::hapus_komentar($commentId);

        return response()->json([
            'success' => true,
            'comments_count' => $result ? $result->comments_count : 0,
        ]);
    }

    private function formatComment(Comment $c, $authUser, Post $post): array
    {
        $user = $c->user;
        return [
            'id' => $c->id,
            'body' => $c->body,
            'created_at' => $c->created_at->diffForHumans(null, true, true),
            'user_id' => $c->user_id,
            'user_name' => $user->name ?? 'User',
            'user_username' => $user->username ?? 'user_' . $c->user_id,
            'user_avatar' => $user->avatar ?? null,
            'user_initials' => strtoupper(substr($user->name ?? 'U', 0, 2)),
            'can_delete' => ($authUser && ($authUser->id === $c->user_id || $authUser->id === $post->user_id)),
            'replies' => $c->replies->map(function ($r) use ($authUser, $post) {
                $rUser = $r->user;
                return [
                    'id' => $r->id,
                    'body' => $r->body,
                    'created_at' => $r->created_at->diffForHumans(null, true, true),
                    'user_id' => $r->user_id,
                    'user_name' => $rUser->name ?? 'User',
                    'user_username' => $rUser->username ?? 'user_' . $r->user_id,
                    'user_avatar' => $rUser->avatar ?? null,
                    'user_initials' => strtoupper(substr($rUser->name ?? 'U', 0, 2)),
                    'can_delete' => ($authUser && ($authUser->id === $r->user_id || $authUser->id === $post->user_id)),
                ];
            }),
        ];
    }
}
