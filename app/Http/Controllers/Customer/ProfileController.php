<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return $this->renderProfile($user, true);
    }

    public function show(string $username)
    {
        $authUser = Auth::user();
        $targetUser = User::where('username', $username)
            ->orWhere('id', is_numeric($username) ? (int) $username : 0)
            ->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $targetUser->id;

        return $this->renderProfile($targetUser, $isOwnProfile);
    }

    private function renderProfile(User $targetUser, bool $isOwnProfile)
    {
        $authUser = Auth::user();

        // Load posts
        $posts = $targetUser->posts()->latest()->get();

        // Saved posts: ONLY for own profile, strictly hidden from other visitors
        $savedPosts = $isOwnProfile ? $targetUser->savedPosts()->latest()->get() : collect();

        // Counts
        $postsCount = $posts->count();
        $followersCount = $targetUser->followers()->count();
        $followingCount = $targetUser->following()->count();

        // Check follow status if viewing someone else
        $isFollowing = false;
        if (! $isOwnProfile && $authUser) {
            $isFollowing = $authUser->isFollowing($targetUser->id);
        }

        $highlights = $targetUser->storyHighlights()->with('stories')->get();
        $archivedStories = $isOwnProfile
            ? $targetUser->stories()->whereDoesntHave('highlights')->latest()->get()
            : collect();

        return view('customer.profile.index', compact(
            'targetUser',
            'isOwnProfile',
            'posts',
            'savedPosts',
            'postsCount',
            'followersCount',
            'followingCount',
            'isFollowing',
            'highlights',
            'archivedStories'
        ));
    }

    public function toggleFollow(Request $request, int $id)
    {
        $authUser = Auth::user();
        if ($authUser->id === $id) {
            return response()->json(['error' => 'Tidak bisa follow akun sendiri.'], 422);
        }

        $targetUser = User::findOrFail($id);
        $isFollowing = $authUser->isFollowing($id);

        if ($isFollowing) {
            $authUser->following()->detach($id);
            $newStatus = false;
        } else {
            $authUser->following()->attach($id);
            $newStatus = true;

            // Create notification for target user
            Notification::create([
                'user_id' => $targetUser->id,
                'actor_id' => $authUser->id,
                'type' => 'follow',
                'message' => ($authUser->username ?? $authUser->name).' started following you.',
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'following' => $newStatus,
                'followers_count' => $targetUser->followers()->count(),
            ]);
        }

        return back()->with('success', $newStatus ? "Kamu sekarang mengikuti @{$targetUser->username}" : "Berhenti mengikuti @{$targetUser->username}");
    }

    public function toggleSave(Request $request, int $postId)
    {
        $authUser = Auth::user();
        $post = Post::findOrFail($postId);

        $hasSaved = $authUser->hasSaved($postId);

        if ($hasSaved) {
            $authUser->savedPosts()->detach($postId);
            $saved = false;
        } else {
            $authUser->savedPosts()->attach($postId);
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
        $user->note = $request->note;
        $user->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'note' => $user->note]);
        }

        return back()->with('success', 'Catatan status profil diperbarui!');
    }

    public function getFollowers(int $id)
    {
        $user = User::findOrFail($id);
        $authUser = Auth::user();

        $followers = $user->followers()->get()->map(function ($u) use ($authUser) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? 'user_'.$u->id,
                'avatar' => $u->avatar,
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'is_following' => $authUser ? $authUser->isFollowing($u->id) : false,
                'is_self' => $authUser ? $authUser->id === $u->id : false,
                'profile_url' => route('profile.show', $u->username ?? $u->id),
            ];
        });

        return response()->json(['users' => $followers, 'title' => 'Followers']);
    }

    public function getFollowing(int $id)
    {
        $user = User::findOrFail($id);
        $authUser = Auth::user();

        $following = $user->following()->get()->map(function ($u) use ($authUser) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? 'user_'.$u->id,
                'avatar' => $u->avatar,
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'is_following' => $authUser ? $authUser->isFollowing($u->id) : false,
                'is_self' => $authUser ? $authUser->id === $u->id : false,
                'profile_url' => route('profile.show', $u->username ?? $u->id),
            ];
        });

        return response()->json(['users' => $following, 'title' => 'Following']);
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'caption' => 'nullable|string|max:1000',
            'type'    => 'nullable|string|max:50',
            'photos'  => 'nullable|array',
            'photos.*' => 'nullable|string', // base64 or file
        ]);

        $authUser = Auth::user();
        $storedImages = [];

        // Check if files were uploaded directly
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $filename = 'post_' . $authUser->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/posts'), $filename);
                $storedImages[] = '/uploads/posts/' . $filename;
            }
        }

        // Check for base64 cropped photos
        if ($request->filled('photos') && is_array($request->photos)) {
            foreach ($request->photos as $b64) {
                if (str_starts_with($b64, 'data:image')) {
                    $parts = explode(',', $b64);
                    if (isset($parts[1])) {
                        $imgData = base64_decode($parts[1]);
                        $filename = 'post_' . $authUser->id . '_' . uniqid() . '.jpg';
                        file_put_contents(public_path('uploads/posts/' . $filename), $imgData);
                        $storedImages[] = '/uploads/posts/' . $filename;
                    }
                } elseif (str_starts_with($b64, '/uploads/')) {
                    $storedImages[] = $b64;
                }
            }
        }

        // Fallback placeholder image if none provided
        $primaryImage = !empty($storedImages) ? $storedImages[0] : '/images/placeholder-post.jpg';

        $caption = trim($request->caption ?? '');
        $type = $request->type ?? 'GOTONG ROYONG';

        // Process custom tags (#)
        $tags = $request->input('tags', []);
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }
        if (is_array($tags) && !empty($tags)) {
            $cleanTags = [];
            foreach ($tags as $t) {
                $t = trim($t);
                if (!empty($t)) {
                    $cleanTags[] = str_starts_with($t, '#') ? $t : '#' . $t;
                }
            }
            if (!empty($cleanTags)) {
                $type = $cleanTags[0]; // Primary tag becomes the type badge
                // Append tags to caption if not already present
                foreach ($cleanTags as $ct) {
                    if (!str_contains($caption, $ct)) {
                        $caption .= ($caption ? ' ' : '') . $ct;
                    }
                }
            }
        }

        $post = Post::create([
            'user_id' => $authUser->id,
            'image'   => $primaryImage,
            'caption' => $caption,
            'type'    => $type,
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Postingan baru berhasil dibagikan!',
                'post'    => [
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
