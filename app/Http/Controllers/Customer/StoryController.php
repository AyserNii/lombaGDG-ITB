<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:10240',
            'caption' => 'nullable|string|max:2000',
        ]);

        $path = $request->file('image')->store('stories', 'public');

        Auth::user()->stories()->create([
            'image' => $path,
            'caption' => $validated['caption'] ?? null,
            'expires_at' => now()->addDay(),
        ]);

        return redirect()->route('community')->with('success', 'Cerita berhasil dibagikan dan akan tampil selama 24 jam.');
    }

    public function storeHighlight(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:40',
            'story_ids' => 'required|array|min:1',
            'story_ids.*' => 'required|integer|distinct|exists:stories,id',
        ]);

        $stories = Story::where('user_id', Auth::id())
            ->whereIn('id', $validated['story_ids'])
            ->get();

        if ($stories->count() !== count($validated['story_ids'])) {
            return back()->withErrors(['story_ids' => 'Cerita yang dipilih tidak valid.']);
        }

        DB::transaction(function () use ($validated, $stories) {
            $highlight = StoryHighlight::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
            ]);
            $highlight->stories()->sync($stories->modelKeys());
        });

        return back()->with('success', 'Cerita berhasil ditambahkan ke sorotan profil.');
    }
}
