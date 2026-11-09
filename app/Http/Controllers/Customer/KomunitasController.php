<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KomunitasController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();
        $user = $authUser->toArray();
        $myCommunities = [
            [
                'name' => 'Cluster Makan Beji',
                'category' => 'Kuliner',
                'members' => '142 Warga',
                'icon' => 'food',
                'color' => 'bg-amber-500/10 text-amber-600 border-amber-200/50',
                'iconBg' => 'bg-amber-50 text-amber-600',
            ],
            [
                'name' => 'Belanja Mingguan Margonda',
                'category' => 'Belanja',
                'members' => '89 Warga',
                'icon' => 'cart',
                'color' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200/50',
                'iconBg' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'name' => 'Kirim Bareng Depok',
                'category' => 'Logistik Rute',
                'members' => '64 Warga',
                'icon' => 'truck',
                'color' => 'bg-blue-500/10 text-blue-600 border-blue-200/50',
                'iconBg' => 'bg-blue-50 text-blue-600',
            ],
            [
                'name' => 'Hemat & Hijau ITB',
                'category' => 'Eco-Community',
                'members' => '210 Anggota',
                'icon' => 'leaf',
                'color' => 'bg-teal-500/10 text-teal-600 border-teal-200/50',
                'iconBg' => 'bg-teal-50 text-teal-600',
            ],
        ];

        $storyGroups = Story::with('user')
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->groupBy('user_id');

        $ownStories = $storyGroups->pull($authUser->id);
        if ($ownStories) {
            $storyGroups = (new Collection([$authUser->id => $ownStories]))->union($storyGroups);
        }

        $highlightedStoryIds = Story::whereHas('highlights')->pluck('id')->all();
        $storyViewerData = $storyGroups->flatten()->values()->map(fn (Story $story) => [
            'id' => $story->id,
            'image' => Storage::disk('public')->url($story->image),
            'caption' => $story->caption,
            'name' => $story->user->name,
            'username' => $story->user->username,
            'created_at' => $story->created_at->diffForHumans(),
            'is_own' => $story->user_id === $authUser->id,
            'is_highlighted' => in_array($story->id, $highlightedStoryIds),
        ]);
        $myStories = $storyGroups->get($authUser->id, collect());
        $otherStoryGroups = $storyGroups->except($authUser->id);

        $posts = Post::with('user')->latest()->get();
        $suggestedUsers = User::where('id', '!=', $authUser->id)->take(4)->get();

        return view('community', compact(
            'user',
            'myCommunities',
            'myStories',
            'otherStoryGroups',
            'storyViewerData',
            'posts',
            'suggestedUsers'
        ));
    }
}
