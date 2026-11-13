<?php

namespace App\Http\Controllers\Customer;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

use App\Http\Controllers\Controller;
use App\Models\Customer\ProfileModel as MainModel;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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
        $this->role = 'customer';
        $this->module = 'profil';
        $this->page = 'Profil Warga';
        $this->title = 'Profil Pengguna Gotong Royong';
        $this->basePath = 'customer.profile';
        $this->routePrefix = 'customer.profil';
        $this->routeName = 'customer.profil.index';
        $this->lastPageKey = 'customer_profile_last_page';
        $this->alertKey = 'customer_profile_alert';
        $this->sessionNamespace = 'customer_profil';
    }

    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        return $this->renderProfile($user, true);
    }

    public function show(string $username)
    {
        $authUser = Auth::user();
        $targetUser = User::findByUsernameOrIdOrFail($username);

        $isOwnProfile = $authUser && $authUser->id === $targetUser->id;

        return $this->renderProfile($targetUser, $isOwnProfile);
    }

    private function renderProfile(User $targetUser, bool $isOwnProfile)
    {
        $authUser = Auth::user();

        // Ambil data melalui MainModel
        $posts = MainModel::list_posts_by_user($targetUser->id);
        $savedPosts = $isOwnProfile ? MainModel::list_saved_posts_by_user($targetUser->id) : collect();
        $savedPostIds = $savedPosts->pluck('id')->all();

        $postsCount = MainModel::total_posts_by_user($targetUser->id);
        $followersCount = MainModel::total_followers_by_user($targetUser->id);
        $followingCount = MainModel::total_following_by_user($targetUser->id);

        $isFollowing = false;
        $isFollowBack = false;
        if (! $isOwnProfile && $authUser) {
            $isFollowing = MainModel::cek_following($authUser->id, $targetUser->id);
            $isFollowBack = $targetUser->isFollowing($authUser->id);
        }

        $highlights = $targetUser->profileHighlights();
        $archivedStories = $isOwnProfile
            ? $targetUser->archivedStories()
            : collect();

        $datakonten = [
            'judulcore' => $this->title,
            'role' => $this->role,
            'module' => $this->module,
            'page' => $this->page,
            'basePath' => $this->basePath,
            'routePrefix' => $this->routePrefix,
            'sessionNamespace' => $this->sessionNamespace,
            'targetUser' => $targetUser,
            'isOwnProfile' => $isOwnProfile,
            'posts' => $posts,
            'savedPosts' => $savedPosts,
            'savedPostIds' => $savedPostIds,
            'postsCount' => $postsCount,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'isFollowing' => $isFollowing,
            'isFollowBack' => $isFollowBack,
            'highlights' => $highlights,
            'archivedStories' => $archivedStories,
        ];

        return view('customer.profile.index', $datakonten);
    }

    public function toggleFollow(Request $request, int $id)
    {
        $authUser = Auth::user();
        if ($authUser->id === $id) {
            return response()->json(['error' => 'Tidak bisa follow akun sendiri.'], 422);
        }

        $targetUser = User::findByIdOrFail($id);
        $isFollowing = MainModel::cek_following($authUser->id, $id);

        if ($isFollowing) {
            MainModel::hapus_following($authUser->id, $id);
            $newStatus = false;
        } else {
            MainModel::tambah_following($authUser->id, $id);
            $newStatus = true;

            Notification::create([
                'user_id' => $targetUser->id,
                'actor_id' => $authUser->id,
                'type' => 'follow',
                'message' => ($authUser->username ?? $authUser->name).' started following you.',
                'is_read' => false,
            ]);
        }

        $followersCount = MainModel::total_followers_by_user($targetUser->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'following' => $newStatus,
                'followers_count' => $followersCount,
                'follows_you' => $targetUser->isFollowing($authUser->id),
            ]);
        }

        return back()->with('success', $newStatus ? "Kamu sekarang mengikuti @{$targetUser->username}" : "Berhenti mengikuti @{$targetUser->username}");
    }

    public function toggleSave(Request $request, int $postId)
    {
        $authUser = Auth::user();
        $post = Post::findByIdOrFail($postId);

        $hasSaved = MainModel::cek_saved($authUser->id, $postId);

        if ($hasSaved) {
            MainModel::hapus_saved($authUser->id, $postId);
            $saved = false;
        } else {
            MainModel::tambah_saved($authUser->id, $postId);
            $saved = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'saved' => $saved,
                'message' => $saved ? 'Postingan berhasil disimpan ke koleksi privat.' : 'Postingan dihapus dari simpanan.',
            ]);
        }

        return back()->with('info', $saved ? 'Postingan tersimpan secara privat.' : 'Batal menyimpan postingan.');
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'note' => 'nullable|string|max:60',
        ]);

        $user = Auth::user();
        MainModel::perbarui_note($user->id, $request->note);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'note' => $request->note]);
        }

        return back()->with('success', 'Catatan status profil diperbarui!');
    }

    public function getFollowers(int $id)
    {
        $authUser = Auth::user();
        $rawFollowers = MainModel::list_followers($id);
        [$followingIds, $followerIds] = $this->authFollowLookup($authUser);

        $followers = collect($rawFollowers)->map(function ($u) use ($authUser, $followingIds, $followerIds) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? 'user_'.$u->id,
                'avatar' => $u->avatar,
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'is_following' => in_array((int) $u->id, $followingIds, true),
                'follows_you' => in_array((int) $u->id, $followerIds, true),
                'is_self' => $authUser ? $authUser->id === $u->id : false,
                'profile_url' => route('profile.show', $u->username ?? $u->id),
            ];
        });

        return response()->json(['users' => $followers, 'title' => 'Followers']);
    }

    public function getFollowing(int $id)
    {
        $authUser = Auth::user();
        $rawFollowing = MainModel::list_following($id);
        [$followingIds, $followerIds] = $this->authFollowLookup($authUser);

        $following = collect($rawFollowing)->map(function ($u) use ($authUser, $followingIds, $followerIds) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? 'user_'.$u->id,
                'avatar' => $u->avatar,
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'is_following' => in_array((int) $u->id, $followingIds, true),
                'follows_you' => in_array((int) $u->id, $followerIds, true),
                'is_self' => $authUser ? $authUser->id === $u->id : false,
                'profile_url' => route('profile.show', $u->username ?? $u->id),
            ];
        });

        return response()->json(['users' => $following, 'title' => 'Following']);
    }

    private function authFollowLookup(?User $authUser): array
    {
        if (! $authUser) {
            return [[], []];
        }

        return [
            $authUser->followingUserIds(),
            $authUser->followerUserIds(),
        ];
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'caption' => 'nullable|string|max:1000',
            'type' => 'nullable|string|max:50',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|string',
        ]);

        $authUser = Auth::user();
        $storedImages = [];

        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $filename = 'post_'.$authUser->id.'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/posts'), $filename);
                $storedImages[] = '/uploads/posts/'.$filename;
            }
        }

        if ($request->filled('photos') && is_array($request->photos)) {
            foreach ($request->photos as $b64) {
                if (str_starts_with($b64, 'data:image')) {
                    $parts = explode(',', $b64);
                    if (isset($parts[1])) {
                        $imgData = base64_decode($parts[1]);
                        $filename = 'post_'.$authUser->id.'_'.uniqid().'.jpg';
                        file_put_contents(public_path('uploads/posts/'.$filename), $imgData);
                        $storedImages[] = '/uploads/posts/'.$filename;
                    }
                } elseif (str_starts_with($b64, '/uploads/')) {
                    $storedImages[] = $b64;
                }
            }
        }

        $primaryImage = ! empty($storedImages) ? $storedImages[0] : '/images/placeholder-post.jpg';
        $caption = trim($request->caption ?? '');
        $type = $request->type ?? 'GOTONG ROYONG';

        $tags = $request->input('tags', []);
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }
        if (is_array($tags) && ! empty($tags)) {
            $cleanTags = [];
            foreach ($tags as $t) {
                $t = trim($t);
                if (! empty($t)) {
                    $cleanTags[] = str_starts_with($t, '#') ? $t : '#'.$t;
                }
            }
            if (! empty($cleanTags)) {
                $type = $cleanTags[0];
                foreach ($cleanTags as $ct) {
                    if (! str_contains($caption, $ct)) {
                        $caption .= ($caption ? ' ' : '').$ct;
                    }
                }
            }
        }

        $post = Post::create([
            'user_id' => $authUser->id,
            'image' => $primaryImage,
            'caption' => $caption,
            'type' => $type,
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Postingan baru berhasil dibagikan!',
                'post' => [
                    'id' => $post->id,
                    'image' => $post->image,
                    'caption' => $post->caption,
                    'type' => $post->type,
                    'likes_count' => 0,
                    'comments_count' => 0,
                ],
            ]);
        }

        return redirect()->route('profile')->with('success', 'Postingan baru berhasil dibagikan!');
    }
}
