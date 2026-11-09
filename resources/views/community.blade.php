@extends('layouts.app')

@section('title', 'Komunitas Gotong Royong')

@section('content')
@php
    $authUser = Auth::user();
    $rawName = trim($authUser ? $authUser->name : ($user['name'] ?? 'User'));
    $nameParts = preg_split('/\s+/', $rawName);
    $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
    $followersCount = $authUser->followers()->count();
    $followingCount = $authUser->following()->count();
    $postsCount = $authUser->posts()->count();
@endphp

<div class="grid gap-5 xl:grid-cols-[240px_minmax(0,1fr)_320px]">
    <!-- LEFT ASIDE: Mini Instagram Profile & Communities -->
    <aside class="hidden space-y-4 xl:block">
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <a href="{{ route('profile') }}" class="group block">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        @if ($authUser && $authUser->avatar)
                            <img src="{{ $authUser->avatar }}" class="h-14 w-14 shrink-0 rounded-full object-cover ring-2 ring-[#4154e8]/20 group-hover:ring-[#4154e8] transition">
                        @else
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-lg font-black shadow-sm ring-2 ring-[#4154e8]/20 select-none group-hover:ring-[#4154e8] transition">
                                {{ $userInitials }}
                            </div>
                        @endif
                        @if ($authUser && $authUser->note)
                            <span class="absolute -top-2 left-1/2 -translate-x-1/2 rounded-lg bg-white px-1.5 py-0.5 text-[8px] font-bold text-slate-700 shadow border border-slate-200 truncate max-w-[80px]">
                                {{ $authUser->note }}
                            </span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-extrabold leading-tight text-slate-900 group-hover:text-[#4154e8] transition truncate">
                            {{ $authUser->name ?? 'User' }}
                        </p>
                        <p class="text-xs text-slate-400 truncate">{{ $authUser->username ? '@' . $authUser->username : 'Customer' }}</p>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-3 gap-1 text-center border-t border-slate-100 pt-3">
                    <div>
                        <p class="text-base font-extrabold text-slate-900">{{ $postsCount }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold">Post</p>
                    </div>
                    <div>
                        <p class="text-base font-extrabold text-slate-900">{{ $followersCount }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold">Followers</p>
                    </div>
                    <div>
                        <p class="text-base font-extrabold text-slate-900">{{ $followingCount }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold">Following</p>
                    </div>
                </div>
            </a>

            <div class="mt-4 pt-3 border-t border-slate-100 flex gap-2">
                <a href="{{ route('profile') }}" class="flex-1 rounded-xl bg-slate-100 hover:bg-slate-200 py-2 text-center text-xs font-bold text-slate-700 transition">
                    Lihat Profil
                </a>
                <a href="{{ route('chat') }}" class="rounded-xl bg-[#4154e8] hover:bg-[#3243d4] p-2 text-white transition" title="Buka Chat DM">
                    @include('partials.icons', ['name' => 'mail', 'class' => 'h-4 w-4'])
                </a>
            </div>
        </section>

        <!-- Direct Chat Quick Access Banner -->
        <a href="{{ route('chat') }}" class="flex items-center justify-between rounded-[28px] border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 text-slate-800 shadow-[0_12px_30px_rgba(76,84,210,0.08)] transition hover:scale-[1.02] active:scale-[0.98]">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#4154e8] text-white">
                    @include('partials.icons', ['name' => 'mail', 'class' => 'h-5 w-5'])
                </span>
                <div>
                    <h4 class="text-xs font-extrabold">Chat Komunitas</h4>
                    <p class="text-[10px] text-slate-500">Direct Messages tetangga</p>
                </div>
            </div>
            <span class="text-[#4154e8] text-sm font-bold">&rarr;</span>
        </a>

        <section class="rounded-[28px] bg-white p-5 text-sm shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <p class="mb-3 text-xs font-extrabold tracking-wide text-slate-400 uppercase">Komunitasmu</p>
            <div class="space-y-2">
                @foreach ($myCommunities as $comm)
                    <div class="flex items-center gap-3 rounded-2xl border {{ $comm['color'] }} px-3 py-2.5 font-semibold">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl {{ $comm['iconBg'] }}">
                            @include('partials.icons', ['name' => $comm['icon'], 'class' => 'h-4 w-4'])
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-bold">{{ $comm['name'] }}</p>
                            <p class="text-[10px] opacity-70">{{ $comm['members'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </aside>

    <!-- CENTER FEED -->
    <div class="space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->has('story_ids'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first('story_ids') }}</div>
        @endif

        <!-- Stories (Instagram Style) -->
        <section class="rounded-[28px] bg-white p-4 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <div class="flex gap-4 overflow-x-auto pb-1 no-scrollbar">
                @php $myActiveStory = $myStories->first(); @endphp
                <div class="relative flex w-16 shrink-0 flex-col items-center gap-2 group">
                    <button type="button" @if ($myActiveStory) onclick="openStory({{ $storyViewerData->search(fn ($item) => $item['id'] === $myActiveStory->id) }})" @else onclick="openStoryComposer()" @endif
                            class="rounded-full p-[2.5px] ring-2 {{ $myActiveStory ? 'ring-rose-500 ring-offset-2' : 'ring-slate-200' }} transition group-hover:scale-105">
                        @if ($myActiveStory)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($myActiveStory->image) }}" alt="" class="h-14 w-14 rounded-full border-2 border-white object-cover">
                        @elseif ($authUser->avatar)
                            <img src="{{ $authUser->avatar }}" alt="" class="h-14 w-14 rounded-full border-2 border-white object-cover">
                        @else
                            <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-sm font-extrabold text-white">
                                {{ $userInitials }}
                            </span>
                        @endif
                    </button>
                    <button type="button" onclick="openStoryComposer()" class="absolute bottom-5 right-0 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-[#4154e8] text-white" aria-label="Tambah cerita">
                        @include('partials.icons', ['name' => 'plus', 'class' => 'h-3 w-3'])
                    </button>
                    <span class="w-16 truncate text-[11px] text-center font-semibold text-slate-700">Cerita Anda</span>
                </div>

                @foreach ($otherStoryGroups as $userStories)
                    @php
                        $firstStory = $userStories->first();
                        $storyIndex = $storyViewerData->search(fn ($item) => $item['id'] === $firstStory->id);
                    @endphp
                    <button type="button" onclick="openStory({{ $storyIndex }})" class="flex w-16 shrink-0 flex-col items-center gap-2 group">
                        <span class="rounded-full p-[2.5px] ring-2 ring-rose-500 ring-offset-2 transition group-hover:scale-105">
                            @if ($firstStory->user->avatar)
                                <img src="{{ $firstStory->user->avatar }}" alt="" class="h-14 w-14 rounded-full border-2 border-white object-cover">
                            @else
                                <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-gradient-to-tr from-violet-500 to-[#4154e8] text-sm font-extrabold text-white">
                                    {{ strtoupper(mb_substr($firstStory->user->name, 0, 2)) }}
                                </span>
                            @endif
                        </span>
                        <span class="w-16 truncate text-center text-[11px] font-semibold text-slate-700">{{ $firstStory->user->username ? '@' . $firstStory->user->username : $firstStory->user->name }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        <!-- Share Input Box -->
        <section class="rounded-[28px] bg-white p-4 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-xs font-black shadow-sm select-none">
                    {{ $userInitials }}
                </div>
                <input onclick="openCreatePostModal()" class="w-full rounded-full bg-[#f4f6ff] px-4 py-3 text-sm text-slate-500 cursor-pointer" value="Bagikan sesi, hemat, atau momen bareng..." readonly>
            </div>
            <div class="mt-3 flex gap-2 text-xs font-bold text-[#4c54d2]">
                <button type="button" onclick="openCreatePostModal()" class="flex items-center gap-1.5 rounded-full bg-[#eef2fb] px-3 py-1.5 hover:bg-[#e0e7ff] transition">
                    @include('partials.icons', ['name' => 'camera', 'class' => 'h-3.5 w-3.5'])
                    Foto
                </button>
                <a href="{{ route('sesi') }}" class="flex items-center gap-1.5 rounded-full bg-[#eef2fb] px-3 py-1.5 hover:bg-[#e0e7ff] transition">
                    @include('partials.icons', ['name' => 'schedule', 'class' => 'h-3.5 w-3.5'])
                    Sesi
                </a>
                <a href="{{ route('chat') }}" class="flex items-center gap-1.5 rounded-full bg-[#eef2fb] px-3 py-1.5 hover:bg-[#e0e7ff] transition">
                    @include('partials.icons', ['name' => 'mail', 'class' => 'h-3.5 w-3.5'])
                    Chat Warga
                </a>
            </div>
        </section>

        <!-- Posts Feed -->
        @forelse ($posts as $post)
            <article class="feed-card bg-white rounded-[28px] overflow-hidden shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if ($post->user->avatar)
                            <img src="{{ $post->user->avatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
                        @else
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-xs font-black text-white shadow-sm select-none">
                                {{ strtoupper(mb_substr($post->user->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-extrabold text-sm text-slate-900 leading-tight">{{ $post->user->username ? '@' . $post->user->username : $post->user->name }}</p>
                            <p class="text-[11px] text-slate-400">{{ $post->user->role }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-extrabold text-blue-600">
                        {{ $post->type }}
                    </span>
                </div>

                @if ($post->image)
                    <div class="aspect-video w-full overflow-hidden bg-slate-100">
                        <img src="{{ $post->image }}" alt="Post image" class="h-full w-full object-cover">
                    </div>
                @endif

                <div class="p-4 text-xs sm:text-sm text-slate-700 leading-relaxed">
                    <p>{{ $post->caption }}</p>
                </div>

                <div class="flex items-center justify-between px-4 py-3 text-sm font-semibold border-t border-slate-100">
                    <div class="flex gap-4">
                        <button data-like class="flex items-center gap-1.5 text-[#4c54d2] hover:opacity-80 transition">
                            @include('partials.icons', ['name' => 'heart'])
                            <span>{{ $post->likes_count }}</span>
                        </button>
                        <span class="flex items-center gap-1.5 text-slate-500">
                            @include('partials.icons', ['name' => 'comment'])
                            {{ $post->comments_count }}
                        </span>
                        <a href="{{ route('chat') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-[#4154e8] transition">
                            @include('partials.icons', ['name' => 'send'])
                            Kirim DM
                        </a>
                    </div>

                    <!-- Bookmark / Save button (Private Collection) -->
                    <button type="button" onclick="alert('Postingan disimpan ke koleksi privat akunmu!')" class="text-slate-400 hover:text-[#4154e8] transition" title="Simpan ke koleksi privat">
                        @include('partials.icons', ['name' => 'bookmark'])
                    </button>
                </div>
            </article>
        @empty
            <section class="rounded-[28px] border border-slate-100 bg-white px-6 py-12 text-center shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-[#4154e8]">
                    @include('partials.icons', ['name' => 'forum', 'class' => 'h-7 w-7'])
                </div>
                <h2 class="mt-4 text-sm font-extrabold text-slate-900">Belum ada postingan</h2>
                <p class="mt-1 text-xs text-slate-500">Postingan warga akan tampil di sini setelah dibagikan.</p>
            </section>
        @endforelse
    </div>

    <!-- RIGHT ASIDE: Recommendations & Hot Sessions -->
    <aside class="space-y-4">
        <!-- Connect with Neighbors / Follow Suggestions -->
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <h2 class="text-sm font-extrabold text-slate-900">Tetangga yang mungkin kamu kenal</h2>
            <div class="mt-4 space-y-4">
                @foreach ($suggestedUsers as $person)
                    @php
                        $isFollowed = Auth::user()->isFollowing($person->id);
                    @endphp
                    <div class="flex items-start gap-3">
                        <a href="{{ route('profile.show', $person->username ?? $person->id) }}" class="shrink-0">
                            @if ($person->avatar)
                                <img src="{{ $person->avatar }}" class="h-11 w-11 rounded-full object-cover">
                            @else
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-xs font-black shadow-sm select-none">
                                    {{ strtoupper(substr($person->name, 0, 2)) }}
                                </div>
                            @endif
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('profile.show', $person->username ?? $person->id) }}" class="truncate text-xs font-bold text-slate-900 hover:underline block leading-tight">
                                {{ $person->username ? '@' . $person->username : $person->name }}
                            </a>
                            <p class="text-[10px] leading-4 text-slate-400 truncate">{{ $person->name }} · {{ $person->location }}</p>
                            <button type="button"
                                    onclick="toggleFollowSide({{ $person->id }}, this)"
                                    class="mt-1.5 flex items-center gap-1 rounded-xl px-3 py-1 text-xs font-bold transition shadow-sm {{ $isFollowed ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#4154e8] text-white hover:bg-[#3243d4]' }}">
                                {{ $isFollowed ? 'Following' : 'Follow' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Active Hot Sessions -->
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <h2 class="text-sm font-extrabold text-slate-900">Sesi yang lagi ramai</h2>
            <div class="mt-4 space-y-3">
                @foreach ([
                    ['title' => 'Warung Geprek 88', 'sub' => '4/5 orang · tutup 20 menit lagi', 'icon' => 'food', 'iconBg' => 'bg-orange-50 text-orange-500'],
                    ['title' => 'Alfamart Cluster 2', 'sub' => 'Kurang Rp 35rb ke minimum', 'icon' => 'cart', 'iconBg' => 'bg-emerald-50 text-emerald-500'],
                    ['title' => 'Kirim arah UI Depok', 'sub' => '2 paket, muat 1 lagi', 'icon' => 'truck', 'iconBg' => 'bg-blue-50 text-blue-500'],
                ] as $sess)
                    <div class="flex items-center gap-3 rounded-2xl bg-[#f6f8ff] p-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $sess['iconBg'] }}">
                            @include('partials.icons', ['name' => $sess['icon'], 'class' => 'h-4 w-4'])
                        </span>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $sess['title'] }}</p>
                            <p class="text-[10px] text-slate-400">{{ $sess['sub'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </aside>
</div>

<div id="story-composer" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="story-composer-title">
    <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <h2 id="story-composer-title" class="text-lg font-extrabold text-slate-900">Bagikan cerita</h2>
            <button type="button" onclick="closeStoryComposer()" class="rounded-full p-2 text-slate-500 hover:bg-slate-100" aria-label="Tutup">@include('partials.icons', ['name' => 'close', 'class' => 'h-5 w-5'])</button>
        </div>
        <p class="mt-1 text-sm text-slate-500">Cerita akan tampil selama 24 jam. Kamu bisa menyimpannya ke sorotan profil.</p>
        <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            <label class="block text-sm font-bold text-slate-700">
                Foto cerita
                <input type="file" name="image" accept="image/*" required class="mt-2 block w-full rounded-xl border border-slate-200 p-3 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-bold file:text-[#4154e8]">
                @error('image') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-bold text-slate-700">
                Keterangan <span class="font-normal text-slate-400">(opsional)</span>
                <textarea name="caption" rows="3" maxlength="2000" class="mt-2 block w-full rounded-xl border border-slate-200 p-3 text-sm font-normal text-slate-700 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20" placeholder="Ceritakan momenmu..."></textarea>
                @error('caption') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>
            <button type="submit" class="w-full rounded-xl bg-[#4154e8] px-4 py-3 text-sm font-extrabold text-white transition hover:bg-[#3243d4]">Bagikan cerita</button>
        </form>
    </div>
</div>

<div id="story-viewer" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-3 sm:p-8" role="dialog" aria-modal="true" aria-label="Lihat cerita">
    <button type="button" onclick="closeStoryViewer()" class="absolute right-4 top-4 rounded-full bg-white/15 p-3 text-white hover:bg-white/25" aria-label="Tutup">@include('partials.icons', ['name' => 'close', 'class' => 'h-5 w-5'])</button>
    <button type="button" onclick="changeStory(-1)" class="absolute left-3 top-1/2 rounded-full bg-white/15 px-3 py-2 text-white hover:bg-white/25 sm:left-8" aria-label="Cerita sebelumnya">‹</button>
    <article class="relative flex h-[min(82vh,760px)] w-full max-w-md flex-col overflow-hidden rounded-3xl bg-slate-900 shadow-2xl">
        <img id="story-viewer-image" src="" alt="Cerita warga" class="absolute inset-0 h-full w-full object-contain">
        <div class="relative z-10 flex items-start justify-between bg-gradient-to-b from-black/60 to-transparent p-4 text-white">
            <div>
                <p id="story-viewer-name" class="text-sm font-extrabold"></p>
                <p id="story-viewer-time" class="text-xs text-white/75"></p>
            </div>
            <form id="story-highlight-form" method="POST" action="{{ route('stories.highlights.store') }}">
                @csrf
                <input type="hidden" name="title" value="Cerita">
                <input id="story-highlight-id" type="hidden" name="story_ids[]" value="">
                <button id="story-highlight-button" type="submit" class="hidden rounded-full bg-white/20 px-3 py-2 text-xs font-bold text-white backdrop-blur hover:bg-white/30">
                    Tambahkan ke Sorotan
                </button>
            </form>
        </div>
        <p id="story-viewer-caption" class="relative z-10 mt-auto bg-gradient-to-t from-black/70 to-transparent p-5 text-sm text-white"></p>
    </article>
    <button type="button" onclick="changeStory(1)" class="absolute right-3 top-1/2 rounded-full bg-white/15 px-3 py-2 text-white hover:bg-white/25 sm:right-8" aria-label="Cerita berikutnya">›</button>
</div>

<script>
const storyViewerItems = @json($storyViewerData->values());
let activeStoryIndex = 0;

function openStoryComposer() {
    document.getElementById('story-composer').classList.replace('hidden', 'flex');
}

function closeStoryComposer() {
    document.getElementById('story-composer').classList.replace('flex', 'hidden');
}

function openStory(index) {
    if (!storyViewerItems[index]) return;

    activeStoryIndex = index;
    const story = storyViewerItems[index];
    document.getElementById('story-viewer-image').src = story.image;
    document.getElementById('story-viewer-name').textContent = story.username ? '@' + story.username : story.name;
    document.getElementById('story-viewer-time').textContent = story.created_at;
    document.getElementById('story-viewer-caption').textContent = story.caption || '';
    document.getElementById('story-highlight-id').value = story.id;
    document.getElementById('story-highlight-button').classList.toggle('hidden', !story.is_own || story.is_highlighted);
    document.getElementById('story-viewer').classList.replace('hidden', 'flex');
}

function closeStoryViewer() {
    document.getElementById('story-viewer').classList.replace('flex', 'hidden');
}

function changeStory(direction) {
    if (!storyViewerItems.length) return;
    openStory((activeStoryIndex + direction + storyViewerItems.length) % storyViewerItems.length);
}

function toggleFollowSide(userId, btn) {
    fetch('/users/' + userId + '/toggle-follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.following) {
                btn.className = 'mt-1.5 flex items-center gap-1 rounded-xl px-3 py-1 text-xs font-bold transition shadow-sm bg-slate-100 text-slate-700 hover:bg-slate-200';
                btn.innerText = 'Following';
            } else {
                btn.className = 'mt-1.5 flex items-center gap-1 rounded-xl px-3 py-1 text-xs font-bold transition shadow-sm bg-[#4154e8] text-white hover:bg-[#3243d4]';
                btn.innerText = 'Follow';
            }
        }
    });
}
</script>
@endsection
