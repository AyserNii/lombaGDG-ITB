<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function show(int $id)
    {
        $authUser = Auth::user();
        $post = Post::with(['user'])->findOrFail($id);

        $isLiked = $authUser ? $post->likes()->where('user_id', $authUser->id)->exists() : false;
        $isSaved = $authUser ? $authUser->savedPosts()->where('post_id', $post->id)->exists() : false;
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
                'comments_count' => $post->allComments()->count(),
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
        $post = Post::findOrFail($id);

        if ($post->user_id !== $authUser->id) {
            return response()->json(['success' => false, 'error' => 'Tidak memiliki izin.'], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Postingan berhasil dihapus.'
        ]);
    }

    public function toggleLike(Request $request, int $id)
    {
        $authUser = Auth::user();
        $post = Post::findOrFail($id);

        $existingLike = $post->likes()->where('user_id', $authUser->id)->first();

        if ($existingLike) {
            $post->likes()->detach($authUser->id);
            $liked = false;
        } else {
            $post->likes()->attach($authUser->id);
            $liked = true;

            // Create notification if liking someone else's post
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

        $likesCount = $post->likes()->count();
        $post->update(['likes_count' => $likesCount]);

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
            'comments_count' => $post->allComments()->count(),
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
        $post = Post::findOrFail($id);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $authUser->id,
            'parent_id' => $request->parent_id,
            'body' => trim($request->body),
        ]);

        // Increment count
        $commentsCount = $post->allComments()->count();
        $post->update(['comments_count' => $commentsCount]);

        // Notifications
        if ($request->parent_id) {
            $parent = Comment::find($request->parent_id);
            if ($parent && $parent->user_id !== $authUser->id) {
                Notification::create([
                    'user_id' => $parent->user_id,
                    'actor_id' => $authUser->id,
                    'type' => 'comment',
                    'message' => '@' . ($authUser->username ?? $authUser->name) . ' membalas komentar Anda: "' . Str::limit($comment->body, 30) . '"',
                    'post_id' => $post->id,
                    'is_read' => false,
                ]);
            }
        } elseif ($post->user_id !== $authUser->id) {
            Notification::create([
                'user_id' => $post->user_id,
                'actor_id' => $authUser->id,
                'type' => 'comment',
                'message' => '@' . ($authUser->username ?? $authUser->name) . ' mengomentari postingan Anda: "' . Str::limit($comment->body, 30) . '"',
                'post_id' => $post->id,
                'is_read' => false,
            ]);
        }

        $comment->load('user', 'replies.user');

        return response()->json([
            'success' => true,
            'comment' => $this->formatComment($comment, $authUser, $post),
            'comments_count' => $commentsCount,
        ]);
    }

    public function destroyComment(int $commentId)
    {
        $authUser = Auth::user();
        $comment = Comment::with('post')->findOrFail($commentId);

        // Allow deletion if commenter is auth user OR post owner is auth user
        if ($comment->user_id !== $authUser->id && $comment->post->user_id !== $authUser->id) {
            return response()->json(['success' => false, 'error' => 'Tidak memiliki izin menghapus komentar ini.'], 403);
        }

        $postId = $comment->post_id;
        $comment->delete();

        $post = Post::find($postId);
        $count = $post ? $post->allComments()->count() : 0;
        if ($post) {
            $post->update(['comments_count' => $count]);
        }

        return response()->json([
            'success' => true,
            'comments_count' => $count,
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

