<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\KomunitasModel as MainModel;
use App\Models\Customer\PostModel;
use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

class KomunitasController extends Controller
{
    private $role = 'customer';
    private $module = 'gotongroyong';
    private $page = 'komunitas';
    private $title = 'Komunitas Warga';

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
        $this->routePrefix = $this->role . '.' . $this->page . '.';
        $this->alertKey = 'notifikasi' . $this->role . $this->module;
        $this->lastPageKey = 'url' . $this->role . $this->module . $this->page;
        $this->sessionNamespace = 'sesi' . $this->role . $this->module;
    }

    public function list(Request $request, $halaman = '')
    {
        $authUser = Auth::user();
        $user = $authUser ? $authUser->toArray() : session('user', [
            'name' => 'Resya Anggara',
            'username' => 'aysernii',
            'location' => 'Beji, Depok',
        ]);

        $myCommunities = MainModel::list_komunitas(10, 0);

        $storyGroups = Story::with('user')
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->toBase();

        if ($authUser) {
            $ownStories = $storyGroups->pull($authUser->id);
            if ($ownStories) {
                $storyGroups = (new Collection([$authUser->id => $ownStories]))->union($storyGroups);
            }
        }

        $highlightedStoryIds = Story::whereHas('highlights')->pluck('id')->all();
        $storyViewerData = $storyGroups->flatten()->values()->map(function (Story $story) use ($authUser, $highlightedStoryIds, $storyGroups) {
            $userStories = $storyGroups->get($story->user_id, collect());
            $userStoryIndex = $userStories->search(fn ($s) => $s->id === $story->id);
            return [
                'id'                 => $story->id,
                'user_id'            => $story->user_id,
                'user_avatar'        => $story->user ? $story->user->avatar : null,
                'user_initials'      => strtoupper(substr($story->user ? $story->user->name : 'User', 0, 2)),
                'image'              => Storage::disk('public')->url($story->image),
                'caption'            => $story->caption,
                'name'               => $story->user ? $story->user->name : 'User',
                'username'           => $story->user && $story->user->username ? $story->user->username : ($story->user ? $story->user->name : 'user'),
                'created_at'         => $story->created_at->diffForHumans(null, true, true),
                'is_own'             => $authUser ? ($story->user_id === $authUser->id) : false,
                'is_highlighted'     => in_array($story->id, $highlightedStoryIds),
                'user_story_index'   => $userStoryIndex !== false ? $userStoryIndex : 0,
                'user_stories_total' => $userStories->count(),
                'user_story_ids'     => $userStories->pluck('id')->all(),
            ];
        });
        $myStories = $authUser ? $storyGroups->get($authUser->id, collect()) : collect();
        $otherStoryGroups = $authUser ? $storyGroups->filter(fn ($stories, $userId) => (int)$userId !== (int)$authUser->id) : $storyGroups;

        // Dynamic Post Filtering by Type / Tag
        $activeType = $request->query('type');
        $postQuery = Post::with('user')->latest();
        if (!empty($activeType)) {
            $postQuery->where('type', $activeType);
        }
        $posts = $postQuery->get();

        // Dynamic categories from database
        $topicStats = Post::select('type', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        $communityStats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_stories' => Story::where('expires_at', '>', now())->count(),
        ];

        $suggestedUsers = $authUser ? User::where('id', '!=', $authUser->id)->take(4)->get() : User::take(4)->get();

        $datakonten['judulcore'] = $this->title;
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = "";
        $datakonten['parameterdata'] = "";
        $datakonten['user'] = $user;
        $datakonten['myCommunities'] = $myCommunities;
        $datakonten['topicStats'] = $topicStats;
        $datakonten['activeType'] = $activeType;
        $datakonten['communityStats'] = $communityStats;
        $datakonten['myStories'] = $myStories;
        $datakonten['otherStoryGroups'] = $otherStoryGroups;
        $datakonten['storyViewerData'] = $storyViewerData;
        $datakonten['posts'] = $posts;
        $datakonten['suggestedUsers'] = $suggestedUsers;
        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('community', $datakonten);
    }

    public function index()
    {
        return $this->list(request());
    }
}
