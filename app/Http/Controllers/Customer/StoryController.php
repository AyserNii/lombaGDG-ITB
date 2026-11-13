<?php

namespace App\Http\Controllers\Customer;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

use App\Http\Controllers\Controller;
use App\Models\Customer\StoryModel as MainModel;
use App\Models\Story;
use App\Models\StoryHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
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
        $this->module = 'cerita';
        $this->page = 'Cerita Gotong Royong';
        $this->title = 'Cerita & Sorotan Profil';
        $this->basePath = 'customer.stories';
        $this->routePrefix = 'customer.cerita';
        $this->routeName = 'customer.cerita.index';
        $this->lastPageKey = 'customer_story_last_page';
        $this->alertKey = 'customer_story_alert';
        $this->sessionNamespace = 'customer_cerita';
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
            'caption' => 'nullable|string|max:2000',
        ]);

        $path = $request->file('image')->store('stories', 'public');
        $userId = Auth::id();

        MainModel::tambah_story($userId, $path, $validated['caption'] ?? null);

        return redirect()->route('community')->with('success', 'Cerita berhasil dibagikan dan akan tampil selama 24 jam.');
    }

    public function image(Story $story)
    {
        abort_unless(Storage::disk('public')->exists($story->image), 404);

        return Storage::disk('public')->response($story->image, null, [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function highlightImage(StoryHighlight $highlight)
    {
        abort_unless($highlight->thumbnail_path && Storage::disk('public')->exists($highlight->thumbnail_path), 404);

        return Storage::disk('public')->response($highlight->thumbnail_path, null, [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function storeHighlight(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:40',
            'story_ids' => 'required|array|min:1',
            'story_ids.*' => 'required|integer|distinct|exists:stories,id',
            'thumbnail_story_id' => 'nullable|integer|exists:stories,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $userId = Auth::id();
        $stories = Story::ownedByWithIds($userId, $validated['story_ids']);

        if ($stories->count() !== count($validated['story_ids'])) {
            return back()->withErrors(['story_ids' => 'Cerita yang dipilih tidak valid.']);
        }

        $thumbnailStoryId = $validated['thumbnail_story_id'] ?? null;
        if ($thumbnailStoryId && ! $stories->contains('id', $thumbnailStoryId)) {
            return back()->withErrors(['thumbnail_story_id' => 'Pilih thumbnail dari cerita yang disertakan dalam sorotan.']);
        }

        $thumbnailPath = $request->file('thumbnail')?->store('story-highlights', 'public');
        MainModel::tambah_highlight(
            $userId,
            $validated['title'],
            $validated['story_ids'],
            $thumbnailPath,
            $request->hasFile('thumbnail') ? null : $thumbnailStoryId
        );

        return back()->with('success', 'Cerita berhasil ditambahkan ke sorotan profil.');
    }
}
