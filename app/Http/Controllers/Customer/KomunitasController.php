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
            ->groupBy('user_id');

        if ($authUser) {
            $ownStories = $storyGroups->pull($authUser->id);
            if ($ownStories) {
                $storyGroups = (new Collection([$authUser->id => $ownStories]))->union($storyGroups);
            }
        }

        $highlightedStoryIds = Story::whereHas('highlights')->pluck('id')->all();
        $storyViewerData = $storyGroups->flatten()->values()->map(fn (Story $story) => [
            'id' => $story->id,
            'image' => Storage::disk('public')->url($story->image),
            'caption' => $story->caption,
            'name' => $story->user->name,
            'username' => $story->user->username,
            'created_at' => $story->created_at->diffForHumans(),
            'is_own' => $authUser ? ($story->user_id === $authUser->id) : false,
            'is_highlighted' => in_array($story->id, $highlightedStoryIds),
        ]);
        $myStories = $authUser ? $storyGroups->get($authUser->id, collect()) : collect();
        $otherStoryGroups = $authUser ? $storyGroups->except($authUser->id) : $storyGroups;

        $posts = Post::with('user')->latest()->get();
        $suggestedUsers = $authUser ? User::where('id', '!=', $authUser->id)->take(4)->get() : User::take(4)->get();

        $datakonten['judulcore'] = $this->title;
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = "";
        $datakonten['parameterdata'] = "";
        $datakonten['user'] = $user;
        $datakonten['myCommunities'] = $myCommunities;
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
