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
    <!-- LEFT ASIDE: Elegant Community Hub -->
    <aside class="hidden space-y-4 xl:block">
        @php
            $totalPostsForBar = max($communityStats['total_posts'] ?? 1, 1);
        @endphp

        <!-- Trending Topik — Elegant Progress Bar Style -->
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-extrabold tracking-widest text-slate-400 uppercase">Trending Topik</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Berdasarkan postingan warga</p>
                </div>
                @if(!empty($activeType))
                    <a href="{{ route('customer.komunitas.list') }}"
                       class="rounded-xl bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-[#4154e8] hover:bg-indigo-100 transition">
                        Reset
                    </a>
                @endif
            </div>

            <div class="space-y-3">
                @php
                    $barColors = [
                        ['bar' => 'bg-[#4154e8]', 'badge' => 'bg-indigo-50 text-[#4154e8]'],
                        ['bar' => 'bg-violet-500', 'badge' => 'bg-violet-50 text-violet-600'],
                        ['bar' => 'bg-sky-500', 'badge' => 'bg-sky-50 text-sky-600'],
                        ['bar' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-600'],
                        ['bar' => 'bg-amber-500', 'badge' => 'bg-amber-50 text-amber-600'],
                    ];
                    $topicIndex = 0;
                @endphp

                <!-- All Posts row -->
                @php
                    $allPct = 100;
                    $isAllActive = empty($activeType);
                @endphp
                <a href="{{ route('customer.komunitas.list') }}"
                   class="group block rounded-2xl {{ $isAllActive ? 'bg-indigo-50/80 border border-indigo-200/60' : 'hover:bg-slate-50' }} px-3 py-2.5 transition">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold {{ $isAllActive ? 'text-[#4154e8]' : 'text-slate-700 group-hover:text-[#4154e8]' }} transition truncate max-w-[130px]">
                            Semua Postingan
                        </span>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-extrabold {{ $isAllActive ? 'bg-[#4154e8] text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $communityStats['total_posts'] ?? 0 }}
                        </span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-[#4154e8] transition-all duration-500" style="width: 100%"></div>
                    </div>
                </a>

                @forelse ($topicStats as $topic)
                    @php
                        $isCurrent = !empty($activeType) && $activeType === $topic->type;
                        $pct = round(($topic->total / $totalPostsForBar) * 100);
                        $pct = min($pct, 100);
                        $color = $barColors[$topicIndex % count($barColors)];
                        $topicIndex++;
                        $label = str_starts_with($topic->type, '#') ? $topic->type : '#' . $topic->type;
                    @endphp
                    <a href="{{ route('customer.komunitas.list', ['type' => $topic->type]) }}"
                       class="group block rounded-2xl {{ $isCurrent ? 'bg-indigo-50/80 border border-indigo-200/60' : 'hover:bg-slate-50' }} px-3 py-2.5 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold {{ $isCurrent ? 'text-[#4154e8]' : 'text-slate-700 group-hover:text-[#4154e8]' }} transition truncate max-w-[130px]">
                                {{ $label }}
                            </span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isCurrent ? 'bg-[#4154e8] text-white' : $color['badge'] }}">
                                {{ $topic->total }}
                            </span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $color['bar'] }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4">
                        <p class="text-xs text-slate-400">Belum ada topik.</p>
                        <p class="text-[11px] text-slate-300 mt-1">Buat postingan pertamamu!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Dynamic Community Stats -->
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
            <p class="mb-4 text-xs font-extrabold tracking-widest text-slate-400 uppercase">Statistik Komunitas</p>
            <div class="space-y-3.5">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-[#4154e8]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-4M9 20H4v-2a4 4 0 015-4m4 4a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    </span>
                    <div class="flex-1">
                        <p class="text-[11px] text-slate-400 font-medium">Warga Bergabung</p>
                        <p class="text-sm font-extrabold text-slate-900 leading-tight">{{ number_format($communityStats['total_users'] ?? 0) }} <span class="text-xs font-semibold text-slate-400">orang</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-violet-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <div class="flex-1">
                        <p class="text-[11px] text-slate-400 font-medium">Postingan Terbit</p>
                        <p class="text-sm font-extrabold text-slate-900 leading-tight">{{ number_format($communityStats['total_posts'] ?? 0) }} <span class="text-xs font-semibold text-slate-400">post</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    </span>
                    <div class="flex-1">
                        <p class="text-[11px] text-slate-400 font-medium">Cerita Aktif (24j)</p>
                        <p class="text-sm font-extrabold {{ ($communityStats['total_stories'] ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-900' }} leading-tight">{{ number_format($communityStats['total_stories'] ?? 0) }} <span class="text-xs font-semibold text-slate-400">aktif</span></p>
                    </div>
                </div>
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
                @php
                    $myActiveStory = $myStories->first();
                    $myStoryIds = $myStories->pluck('id')->all();
                @endphp
                <div class="relative flex w-16 shrink-0 flex-col items-center gap-2 group">
                    <button type="button"
                            @if ($myActiveStory)
                                onclick="openStory({{ $storyViewerData->search(fn ($item) => $item['id'] === $myActiveStory->id) }})"
                                data-story-user-id="{{ $authUser->id }}"
                                data-story-ids="{{ json_encode($myStoryIds) }}"
                            @else
                                onclick="openStoryComposer()"
                            @endif
                            class="story-user-btn rounded-full p-[2.5px] transition group-hover:scale-105">
                        <span id="story-ring-{{ $authUser->id }}"
                              class="story-ring block rounded-full p-[2px] ring-2 {{ $myActiveStory ? 'ring-rose-500 ring-offset-2' : 'ring-slate-200' }} transition">
                            @if ($myActiveStory)
                                <img src="{{ route('stories.image', $myActiveStory) }}" alt="" class="h-14 w-14 rounded-full border-2 border-white object-cover">
                            @elseif ($authUser->avatar)
                                <img src="{{ $authUser->avatar }}" alt="" onerror="this.classList.add('hidden');this.nextElementSibling.classList.remove('hidden');this.nextElementSibling.classList.add('flex')" class="h-14 w-14 rounded-full border-2 border-white object-cover">
                                <span class="hidden h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-[#4154e8] text-sm font-extrabold text-white">{{ $userInitials }}</span>
                            @else
                                <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-[#4154e8] text-sm font-extrabold text-white">
                                    {{ $userInitials }}
                                </span>
                            @endif
                        </span>
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
                        $storyIdsList = $userStories->pluck('id')->all();
                    @endphp
                    <button type="button"
                            onclick="openStory({{ $storyIndex }})"
                            data-story-user-id="{{ $firstStory->user->id }}"
                            data-story-ids="{{ json_encode($storyIdsList) }}"
                            class="story-user-btn flex w-16 shrink-0 flex-col items-center gap-2 group">
                        <span id="story-ring-{{ $firstStory->user->id }}"
                              class="story-ring rounded-full p-[2.5px] ring-2 ring-rose-500 ring-offset-2 transition group-hover:scale-105">
                            <img src="{{ route('stories.image', $firstStory) }}" alt="" onerror="this.classList.add('hidden');this.nextElementSibling.classList.remove('hidden');this.nextElementSibling.classList.add('flex')" class="h-14 w-14 rounded-full border-2 border-white object-cover">
                            <span class="hidden h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-[#4154e8] text-sm font-extrabold text-white">{{ strtoupper(mb_substr($firstStory->user->name, 0, 2)) }}</span>
                        </span>
                        <span class="w-16 truncate text-center text-[11px] font-semibold text-slate-700">{{ $firstStory->user->username ? '@' . $firstStory->user->username : $firstStory->user->name }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        @if(!empty($activeType))
            <div class="flex items-center justify-between rounded-2xl bg-indigo-50 border border-indigo-100 px-4 py-3 text-xs text-indigo-900 font-semibold shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#4154e8] text-white text-[11px] font-black">#</span>
                    <span>Menampilkan topik: <strong class="font-extrabold text-[#4154e8]">{{ $activeType }}</strong> ({{ $posts->count() }} postingan)</span>
                </div>
                <a href="{{ route('customer.komunitas.list') }}" class="rounded-xl bg-white px-2.5 py-1 text-[11px] font-extrabold text-slate-600 hover:text-slate-900 shadow-xs transition">
                    Tampilkan Semua &times;
                </a>
            </div>
        @endif

        <!-- Posts Feed -->
        @forelse ($posts as $post)
            <article class="feed-card bg-white rounded-[28px] overflow-hidden shadow-[0_12px_40px_rgba(76,84,210,0.08)] border border-slate-100">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if ($post->user->avatar)
                            <img src="{{ $post->user->avatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
                        @else
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-xs font-black text-white shadow-sm select-none">
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
                    <div onclick="openPostDetailModal({{ $post->id }})" class="aspect-video w-full overflow-hidden bg-slate-100 cursor-pointer group">
                        <img src="{{ $post->image }}" alt="Post image" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                    </div>
                @endif

                <div onclick="openPostDetailModal({{ $post->id }})" class="p-4 text-xs sm:text-sm text-slate-700 leading-relaxed cursor-pointer hover:text-slate-900 transition">
                    <p>{{ $post->caption }}</p>
                </div>

                <div class="flex items-center justify-between px-4 py-3 text-sm font-semibold border-t border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $reaction = $post->viewer_reaction;
                            $isSaved = $post->viewer_saved;
                        @endphp
                        <div class="inline-flex items-center rounded-full border border-slate-200 bg-white p-1 shadow-sm">
                            <button type="button" data-post-id="{{ $post->id }}" data-reaction="like" aria-pressed="{{ $reaction === 'like' ? 'true' : 'false' }}"
                                    onclick="togglePostReaction({{ $post->id }}, 'like')"
                                    class="post-reaction-button inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold transition {{ $reaction === 'like' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 10v12m0-12 4-8a3 3 0 0 1 3 3v3h5a2 2 0 0 1 2 2l-1.5 9a2 2 0 0 1-2 2H7m0-12H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/></svg>
                                <span>Suka</span>
                                <span id="like-count-{{ $post->id }}">{{ $post->likes_total }}</span>
                            </button>

                            <span class="mx-1 h-5 w-px bg-slate-200"></span>
                            <button type="button" data-post-id="{{ $post->id }}" data-reaction="dislike" aria-pressed="{{ $reaction === 'dislike' ? 'true' : 'false' }}"
                                    onclick="togglePostReaction({{ $post->id }}, 'dislike')"
                                    class="post-reaction-button inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold transition {{ $reaction === 'dislike' ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14V2m0 12-4 8a3 3 0 0 1-3-3v-3H5a2 2 0 0 1-2-2l1.5-9a2 2 0 0 1 2-2H17m0 12h3a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/></svg>
                                <span>Tidak suka</span>
                                <span id="dislike-count-{{ $post->id }}">{{ $post->dislikes_total }}</span>
                            </button>
                        </div>

                        <a href="{{ route('community.posts.show', ['id' => $post->id]) }}#komentar"
                           class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-emerald-600 hover:text-emerald-700">
                            @include('partials.icons', ['name' => 'comment', 'class' => 'h-4 w-4'])
                            <span><span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span> Komentar</span>
                        </a>

                        <a href="{{ route('chat', ['user' => $post->user->username ?? $post->user->id]) }}"
                           class="flex items-center gap-1.5 text-slate-500 hover:text-[#4154e8] transition text-xs font-semibold px-2">
                            @include('partials.icons', ['name' => 'send', 'class' => 'h-4 w-4'])
                            <span>Kirim DM</span>
                        </a>
                    </div>

                    <!-- Bookmark / Save button (Private Collection) -->
                    <button type="button"
                            data-save-post-id="{{ $post->id }}"
                            data-saved="{{ $isSaved ? 'true' : 'false' }}"
                            aria-pressed="{{ $isSaved ? 'true' : 'false' }}"
                            onclick="togglePostSave({{ $post->id }}, this)"
                            class="transition {{ $isSaved ? 'text-[#4154e8]' : 'text-slate-400 hover:text-[#4154e8]' }}"
                            title="{{ $isSaved ? 'Hapus dari simpanan' : 'Simpan ke koleksi privat' }}">
                        @include('partials.icons', ['name' => 'bookmark', 'class' => 'h-5 w-5'])
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
                        $followsYou = $person->isFollowing(Auth::id());
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
                                    data-follows-you="{{ $followsYou ? 'true' : 'false' }}"
                                    onclick="toggleFollowSide({{ $person->id }}, this)"
                                    class="mt-1.5 flex items-center gap-1 rounded-xl px-3 py-1 text-xs font-bold transition shadow-sm {{ $isFollowed ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#4154e8] text-white hover:bg-[#3243d4]' }}">
                                {{ $isFollowed ? 'Following' : ($followsYou ? 'Follow back' : 'Follow') }}
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

<!-- INSTAGRAM-STYLE STORY VIEWER MODAL (DENGAN LOGO BARENG & FLANKING SIDE PREVIEWS) -->
<div id="story-viewer" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-[#111111]/95 backdrop-blur-md select-none p-2 sm:p-6" role="dialog" aria-modal="true" aria-label="Lihat cerita warga">

    <!-- Top Left: Logo BARENG (putih bersih kontras) -->
    <a href="{{ route('customer.komunitas.list') }}" class="absolute left-5 sm:left-8 top-5 z-40 flex items-center gap-2.5 opacity-90 hover:opacity-100 transition" title="Komunitas BARENG">
        <img src="{{ asset('images/bareng-logo.svg') }}" alt="BARENG" class="h-7 sm:h-8 w-auto brightness-0 invert">
    </a>

    <!-- Top Right: Close Button -->
    <button type="button" onclick="closeStoryViewer()"
            class="absolute right-5 sm:right-8 top-5 z-40 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition backdrop-blur-md"
            aria-label="Tutup cerita">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>

    <!-- Main Stage: Side Prev Card + Active Card + Side Next Card -->
    <div class="relative flex items-center justify-center gap-4 sm:gap-8 w-full max-w-6xl h-full py-4">

        <!-- Left Prev Floating Arrow Button -->
        <button type="button" id="story-prev-btn" onclick="changeStory(-1)"
                class="hidden sm:flex z-30 h-11 w-11 items-center justify-center rounded-full bg-white/20 hover:bg-white/35 text-white transition backdrop-blur-md shadow-lg shrink-0 cursor-pointer"
                aria-label="Cerita sebelumnya">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Left Side Preview Card (Seperti di Instagram Web Desktop!) -->
        <div id="story-prev-card" onclick="changeStory(-1)"
             class="hidden xl:flex flex-col items-center justify-center relative w-44 h-[55vh] rounded-[26px] overflow-hidden cursor-pointer opacity-35 hover:opacity-75 transition-all duration-300 scale-90 border border-white/10 shadow-2xl bg-slate-900 group shrink-0">
            <img id="story-prev-card-img" src="" class="absolute inset-0 h-full w-full object-cover blur-xs opacity-50 group-hover:scale-105 transition duration-500">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="relative z-10 flex flex-col items-center text-center p-3">
                <div class="rounded-full p-1 ring-2 ring-rose-500 mb-2">
                    <img id="story-prev-card-avatar" src="" class="h-12 w-12 rounded-full object-cover">
                </div>
                <p id="story-prev-card-username" class="text-xs font-bold text-white truncate max-w-[120px]"></p>
                <p id="story-prev-card-time" class="text-[10px] text-white/70"></p>
            </div>
        </div>

        <!-- Center Active Story Card (Primary Canvas) -->
        <article id="story-active-card"
                 class="relative flex h-[min(88vh,780px)] w-full max-w-[420px] flex-col overflow-hidden rounded-[30px] bg-black shadow-[0_25px_60px_rgba(0,0,0,0.8)] border border-white/15 shrink-0">

            <!-- Segmented Progress Bars (Top) -->
            <div id="story-progress-container" class="absolute top-3 inset-x-3.5 z-30 flex gap-1.5 pointer-events-none">
                <!-- Dynamically rendered by JS -->
            </div>

            <!-- Active Story Header -->
            <div class="absolute top-6 inset-x-3.5 z-30 flex items-center justify-between text-white drop-shadow-md">
                <div class="flex items-center gap-2.5">
                    <span class="relative block h-9 w-9 shrink-0">
                        <img id="story-viewer-avatar" src="" alt="" class="h-9 w-9 rounded-full object-cover ring-2 ring-white/50">
                        <span id="story-viewer-avatar-fallback" class="absolute inset-0 hidden items-center justify-center rounded-full bg-[#4154e8] text-xs font-black text-white ring-2 ring-white/50"></span>
                    </span>
                    <div class="flex items-center gap-2 leading-tight">
                        <span id="story-viewer-name" class="text-xs font-extrabold text-white hover:underline cursor-pointer"></span>
                        <span class="text-white/40 text-xs">·</span>
                        <span id="story-viewer-time" class="text-[11px] text-white/80 font-medium"></span>
                    </div>
                </div>

                <!-- Action Controls: Pause, Highlight -->
                <div class="flex items-center gap-1.5">
                    <button type="button" id="story-pause-btn" onclick="toggleStoryPause()"
                            class="p-1.5 rounded-full hover:bg-white/20 text-white transition" title="Jeda/Lanjut">
                        <svg id="story-pause-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="6" y="4" width="4" height="16"></rect>
                            <rect x="14" y="4" width="4" height="16"></rect>
                        </svg>
                    </button>

                    <form id="story-highlight-form" method="POST" action="{{ route('stories.highlights.store') }}">
                        @csrf
                        <input type="hidden" name="title" value="Cerita">
                        <input id="story-highlight-id" type="hidden" name="story_ids[]" value="">
                        <button id="story-highlight-button" type="submit"
                                class="hidden rounded-full bg-white/20 hover:bg-white/30 px-2.5 py-1 text-[11px] font-bold text-white transition backdrop-blur-md">
                            Sorotan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Story Image -->
            <img id="story-viewer-image" src="" alt="Cerita warga" class="absolute inset-0 h-full w-full object-cover select-none">

            <!-- Click Navigation Touch Zones -->
            <div class="absolute inset-0 z-10 flex" id="story-touch-zones">
                <div class="w-1/3 h-full cursor-pointer" onclick="changeStory(-1)" title="Cerita sebelumnya"></div>
                <div class="w-2/3 h-full cursor-pointer" onclick="changeStory(1)" title="Cerita berikutnya"></div>
            </div>

            <!-- Bottom Gradient Overlay & Caption -->
            <div class="relative z-20 mt-auto bg-gradient-to-t from-black/90 via-black/40 to-transparent p-4 pb-2 text-white pointer-events-none">
                <p id="story-viewer-caption" class="text-xs sm:text-sm font-medium leading-relaxed whitespace-pre-wrap drop-shadow pointer-events-auto"></p>
            </div>

            <!-- Bottom Reply & Reaction Bar (Instagram Style) -->
            <div class="relative z-20 px-3.5 pb-4 pt-1 flex items-center gap-2 bg-black/60 backdrop-blur-xs">
                <div class="relative flex-1">
                    <input type="text" id="story-reply-input"
                           placeholder="Kirim pesan balasan..."
                           onkeydown="if(event.key === 'Enter') sendStoryReply()"
                           class="w-full rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs text-white placeholder:text-white/60 outline-none focus:border-white focus:bg-white/20 transition">
                </div>
                <button type="button" onclick="sendStoryReply()"
                        class="p-2 text-white hover:text-[#4154e8] transition" title="Kirim balasan">
                    @include('partials.icons', ['name' => 'send', 'class' => 'h-4 w-4'])
                </button>
                <button type="button" onclick="triggerStoryHeart()"
                        class="p-2 text-white hover:text-rose-500 transition group" title="Beri reaksi suka">
                    <svg class="h-5 w-5 group-hover:scale-125 transition text-rose-500 fill-rose-500" viewBox="0 0 24 24">
                        <path d="m12 21.35-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
            </div>

            <!-- Floating Hearts Container (Animated Reactions) -->
            <div id="story-floating-hearts" class="absolute inset-0 pointer-events-none overflow-hidden z-40"></div>
        </article>

        <!-- Right Side Preview Card (Seperti di Instagram Web Desktop!) -->
        <div id="story-next-card" onclick="changeStory(1)"
             class="hidden xl:flex flex-col items-center justify-center relative w-44 h-[55vh] rounded-[26px] overflow-hidden cursor-pointer opacity-35 hover:opacity-75 transition-all duration-300 scale-90 border border-white/10 shadow-2xl bg-slate-900 group shrink-0">
            <img id="story-next-card-img" src="" class="absolute inset-0 h-full w-full object-cover blur-xs opacity-50 group-hover:scale-105 transition duration-500">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="relative z-10 flex flex-col items-center text-center p-3">
                <div class="rounded-full p-1 ring-2 ring-rose-500 mb-2">
                    <img id="story-next-card-avatar" src="" class="h-12 w-12 rounded-full object-cover">
                </div>
                <p id="story-next-card-username" class="text-xs font-bold text-white truncate max-w-[120px]"></p>
                <p id="story-next-card-time" class="text-[10px] text-white/70"></p>
            </div>
        </div>

        <!-- Right Next Floating Arrow Button -->
        <button type="button" id="story-next-btn" onclick="changeStory(1)"
                class="hidden sm:flex z-30 h-11 w-11 items-center justify-center rounded-full bg-white/20 hover:bg-white/35 text-white transition backdrop-blur-md shadow-lg shrink-0 cursor-pointer"
                aria-label="Cerita berikutnya">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

<!-- THREADS / X STYLE COMMENTS MODAL -->
<div id="comments-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-3 sm:p-4 backdrop-blur-sm animate-fade-in" role="dialog" aria-modal="true" onclick="if(event.target === this) closeCommentsModal()">
    <div class="relative flex flex-col w-full max-w-xl max-h-[88vh] rounded-[28px] bg-white shadow-2xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-white/95 sticky top-0 z-10 backdrop-blur">
            <div class="flex items-center gap-2.5">
                <h3 class="text-base font-extrabold text-slate-900">Komentar</h3>
                <span id="modal-comments-count-pill" class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">0</span>
            </div>
            <button type="button" onclick="closeCommentsModal()" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition" aria-label="Tutup">
                @include('partials.icons', ['name' => 'close', 'class' => 'h-5 w-5'])
            </button>
        </div>

        <!-- Scrollable Modal Body: Post Snippet + Root Input + Threads -->
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" id="modal-comments-scroll-body">
            <!-- Original Post Preview (Threads/X style) -->
            <div id="modal-post-snippet" class="relative pb-2">
                <div class="flex items-start gap-3">
                    <div class="relative shrink-0 flex flex-col items-center">
                        <div id="modal-post-author-avatar-wrap">
                            <!-- populated by JS -->
                        </div>
                        <!-- Vertical line connecting to comment box -->
                        <div class="w-0.5 flex-1 bg-slate-200 my-1 min-h-[20px]"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span id="modal-post-author-name" class="text-xs font-extrabold text-slate-900 leading-tight"></span>
                            <span id="modal-post-author-handle" class="text-xs font-medium text-slate-400"></span>
                            <span class="text-slate-300">·</span>
                            <span id="modal-post-time" class="text-xs text-slate-400 font-medium"></span>
                        </div>
                        <p id="modal-post-caption" class="text-xs sm:text-sm text-slate-800 mt-1 leading-relaxed whitespace-pre-wrap"></p>
                        <div id="modal-post-image-wrap" class="mt-2.5 hidden">
                            <img id="modal-post-image" src="" alt="Post image" class="max-h-56 rounded-2xl object-cover border border-slate-100 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comment Input Form (Threads/X style) -->
            <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-3.5 transition focus-within:border-[#4154e8] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#4154e8]/20">
                <form id="comment-form" onsubmit="submitComment(event)" class="flex flex-col gap-2">
                    <input type="hidden" id="comment-parent-id" value="">
                    <!-- Reply Target Indicator -->
                    <div id="reply-indicator" class="hidden items-center justify-between bg-blue-50 text-[#4154e8] px-3 py-1 rounded-xl text-xs font-semibold">
                        <span id="reply-target-text">Membalas @username</span>
                        <button type="button" onclick="cancelReplyTarget()" class="text-slate-400 hover:text-slate-600 font-bold ml-2 text-sm">&times;</button>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 mt-0.5">
                            @if ($authUser && $authUser->avatar)
                                <img src="{{ $authUser->avatar }}" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4154e8] text-white text-[11px] font-black">
                                    {{ $userInitials }}
                                </div>
                            @endif
                        </div>
                        <textarea id="comment-input" rows="2" placeholder="Tulis balasan Anda..." required
                                  class="flex-1 bg-transparent border-0 p-0 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 outline-none resize-none"></textarea>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200/60 pt-2 mt-1">
                        <span class="text-[11px] text-slate-400 font-medium">Bahas dengan warga secara santun</span>
                        <button type="submit" id="comment-submit-btn"
                                class="rounded-full bg-[#4154e8] hover:bg-[#3243d4] px-4 py-1.5 text-xs font-extrabold text-white transition shadow-sm disabled:opacity-50">
                            Balas
                        </button>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            <div id="modal-comments-list" class="space-y-3 pt-2">
                <!-- Rendered by JS -->
            </div>
        </div>
    </div>
</div>

<script>
let activeCommentsPostId = null;
const reactionRequestSequence = new Map();
const saveRequestSequence = new Map();

function togglePostReaction(postId, reaction) {
    const buttons = [...document.querySelectorAll(`.post-reaction-button[data-post-id="${postId}"]`)];
    const oldReaction = buttons.find((button) => button.getAttribute('aria-pressed') === 'true')?.dataset.reaction || null;
    const oldLikes = Number(document.getElementById('like-count-' + postId)?.textContent || 0);
    const oldDislikes = Number(document.getElementById('dislike-count-' + postId)?.textContent || 0);
    const nextReaction = oldReaction === reaction ? null : reaction;
    const nextLikes = oldLikes + (nextReaction === 'like' ? 1 : 0) - (oldReaction === 'like' ? 1 : 0);
    const nextDislikes = oldDislikes + (nextReaction === 'dislike' ? 1 : 0) - (oldReaction === 'dislike' ? 1 : 0);
    const sequence = (reactionRequestSequence.get(postId) || 0) + 1;
    reactionRequestSequence.set(postId, sequence);

    window.animateReactionParticles(buttons.find((button) => button.dataset.reaction === reaction), reaction);
    updatePostReactionUI(postId, nextReaction, nextLikes, nextDislikes);

    fetch('/posts/' + postId + '/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reaction })
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.error || 'Reaksi gagal disimpan.');
        return data;
    })
    .then(data => {
        if (reactionRequestSequence.get(postId) === sequence) {
            updatePostReactionUI(postId, data.reaction, data.likes_count, data.dislikes_count);
        }
    })
    .catch((error) => {
        if (reactionRequestSequence.get(postId) === sequence) {
            updatePostReactionUI(postId, oldReaction, oldLikes, oldDislikes);
            window.alert(error.message || 'Reaksi gagal disimpan. Coba lagi.');
        }
    });
}

function updatePostReactionUI(postId, reaction, likes, dislikes) {
    const likeCount = document.getElementById('like-count-' + postId);
    const dislikeCount = document.getElementById('dislike-count-' + postId);
    if (likeCount) likeCount.textContent = likes;
    if (dislikeCount) dislikeCount.textContent = dislikes;

    document.querySelectorAll(`.post-reaction-button[data-post-id="${postId}"]`).forEach((button) => {
        const active = button.dataset.reaction === reaction;
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.classList.toggle('bg-emerald-50', active && button.dataset.reaction === 'like');
        button.classList.toggle('text-emerald-700', active && button.dataset.reaction === 'like');
        button.classList.toggle('bg-rose-50', active && button.dataset.reaction === 'dislike');
        button.classList.toggle('text-rose-700', active && button.dataset.reaction === 'dislike');
        button.classList.toggle('text-slate-600', !active);
    });
}

function togglePostSave(postId, btn) {
    const oldSaved = btn.dataset.saved === 'true';
    const nextSaved = !oldSaved;
    const sequence = (saveRequestSequence.get(postId) || 0) + 1;
    saveRequestSequence.set(postId, sequence);
    window.setPostSavedState(postId, nextSaved);

    fetch('/posts/' + postId + '/toggle-save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || data.error || 'Simpanan gagal diperbarui.');
        return data;
    })
    .then((data) => {
        if (saveRequestSequence.get(postId) === sequence) {
            window.setPostSavedState(postId, data.saved);
        }
    })
    .catch((error) => {
        if (saveRequestSequence.get(postId) === sequence) {
            window.setPostSavedState(postId, oldSaved);
            window.alert(error.message || 'Simpanan gagal diperbarui. Coba lagi.');
        }
    });
}

function openCommentsModal(postId) {
    activeCommentsPostId = postId;
    cancelReplyTarget();
    document.getElementById('comment-input').value = '';

    const modal = document.getElementById('comments-modal');
    modal.classList.replace('hidden', 'flex');

    const list = document.getElementById('modal-comments-list');
    list.innerHTML = `
        <div class="flex flex-col items-center justify-center py-10 text-slate-400">
            <div class="animate-spin h-6 w-6 border-2 border-[#4154e8] border-t-transparent rounded-full mb-2"></div>
            <span class="text-xs font-semibold">Memuat komentar...</span>
        </div>`;

    fetch('/posts/' + postId + '/comments', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Populate Post preview
            const post = data.post;
            document.getElementById('modal-post-author-name').textContent = post.author_name;
            document.getElementById('modal-post-author-handle').textContent = '@' + post.author_username;
            document.getElementById('modal-post-time').textContent = post.created_at;
            document.getElementById('modal-post-caption').textContent = post.caption || '';

            const avatarWrap = document.getElementById('modal-post-author-avatar-wrap');
            if (post.author_avatar) {
                avatarWrap.innerHTML = `<img src="${post.author_avatar}" class="h-10 w-10 rounded-full object-cover">`;
            } else {
                avatarWrap.innerHTML = `<div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#4154e8] text-white text-xs font-black">${post.author_initials}</div>`;
            }

            const imgWrap = document.getElementById('modal-post-image-wrap');
            const imgEl = document.getElementById('modal-post-image');
            if (post.image) {
                imgEl.src = post.image;
                imgWrap.classList.remove('hidden');
            } else {
                imgWrap.classList.add('hidden');
            }

            // Update count pill
            document.getElementById('modal-comments-count-pill').textContent = data.comments_count;
            const cardCount = document.getElementById('comment-count-' + postId);
            if (cardCount) cardCount.textContent = data.comments_count;

            renderModalComments(data.comments);
        }
    })
    .catch(() => {
        list.innerHTML = '<div class="py-8 text-center text-xs text-rose-500">Gagal memuat komentar.</div>';
    });
}

function closeCommentsModal() {
    const modal = document.getElementById('comments-modal');
    modal.classList.replace('flex', 'hidden');
    activeCommentsPostId = null;
    cancelReplyTarget();
}

function renderModalComments(comments) {
    window.currentDetailCommentsMap = window.currentDetailCommentsMap || {};
    if (comments && comments.length) {
        comments.forEach(c => {
            window.currentDetailCommentsMap[c.id] = c;
            if (c.replies && c.replies.length) {
                c.replies.forEach(r => { window.currentDetailCommentsMap[r.id] = r; });
            }
        });
    }

    const list = document.getElementById('modal-comments-list');
    if (!comments || comments.length === 0) {
        list.innerHTML = `
            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                <svg class="h-8 w-8 text-slate-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="text-xs font-bold text-slate-600">Belum ada komentar</p>
                <p class="text-[11px] text-slate-400">Jadilah yang pertama memulai obrolan!</p>
            </div>`;
        return;
    }

    list.innerHTML = comments.map(c => renderCommentItem(c)).join('');
}

function renderCommentItem(c, isReply = false) {
    const avatar = c.user_avatar
        ? `<img src="${c.user_avatar}" class="${isReply ? 'h-7 w-7' : 'h-9 w-9'} rounded-full object-cover">`
        : `<div class="flex ${isReply ? 'h-7 w-7 text-[10px]' : 'h-9 w-9 text-xs'} items-center justify-center rounded-full bg-[#4154e8] text-white font-black">${c.user_initials}</div>`;

    const hasReplies = c.replies && c.replies.length > 0;

    let repliesHtml = '';
    if (hasReplies) {
        repliesHtml = `
            <div class="relative pl-6 mt-2 space-y-2.5">
                <div class="absolute left-3.5 top-0 bottom-3 w-0.5 bg-slate-200"></div>
                ${c.replies.map(r => renderCommentItem(r, true)).join('')}
            </div>`;
    }

    return `
        <div class="comment-item group/item relative" id="comment-node-${c.id}">
            <div class="flex items-start gap-3">
                <div class="shrink-0 pt-0.5">
                    ${avatar}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="rounded-2xl bg-slate-50 p-3 text-left">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-extrabold text-slate-900 leading-tight">${c.user_name}</span>
                                <span class="text-[11px] text-slate-400 font-medium">@${c.user_username}</span>
                                <span class="text-slate-300">·</span>
                                <span class="text-[10px] text-slate-400 font-medium">${c.created_at}</span>
                            </div>
                            ${c.can_delete ? `
                                <button type="button" onclick="deleteComment(${c.id})" class="text-slate-400 hover:text-rose-600 transition p-1" title="Hapus komentar">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            ` : ''}
                        </div>
                        <p class="text-xs text-slate-800 mt-1 leading-relaxed whitespace-pre-wrap">${c.body}</p>
                    </div>

                    <!-- Action Bar under comment -->
                    <div class="flex items-center gap-3 px-2 pt-1">
                        <button type="button" onclick="setReplyTarget(${c.id}, '${c.user_username}')"
                                class="text-[11px] font-bold text-slate-500 hover:text-[#4154e8] transition">
                            Balas
                        </button>
                    </div>

                    ${repliesHtml}
                </div>
            </div>
        </div>`;
}

function setReplyTarget(commentId, username) {
    document.getElementById('comment-parent-id').value = commentId;
    document.getElementById('reply-target-text').textContent = 'Membalas @' + username;
    document.getElementById('reply-indicator').classList.remove('hidden');
    document.getElementById('reply-indicator').classList.add('flex');

    const input = document.getElementById('comment-input');
    input.value = '@' + username + ' ';
    input.focus();
}

function cancelReplyTarget() {
    document.getElementById('comment-parent-id').value = '';
    const indicator = document.getElementById('reply-indicator');
    if (indicator) {
        indicator.classList.remove('flex');
        indicator.classList.add('hidden');
    }
}

function submitComment(event) {
    event.preventDefault();
    if (!activeCommentsPostId) return;

    const input = document.getElementById('comment-input');
    const body = input.value.trim();
    if (!body) return;

    const parentId = document.getElementById('comment-parent-id').value;
    const submitBtn = document.getElementById('comment-submit-btn');
    submitBtn.disabled = true;

    fetch('/posts/' + activeCommentsPostId + '/comments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            body: body,
            parent_id: parentId || null
        })
    })
    .then(r => r.json())
    .then(data => {
        submitBtn.disabled = false;
        if (data.success) {
            input.value = '';
            cancelReplyTarget();

            // Refresh comments
            openCommentsModal(activeCommentsPostId);
        } else {
            alert(data.error || 'Gagal mengirim komentar.');
        }
    })
    .catch(() => {
        submitBtn.disabled = false;
        alert('Terjadi kesalahan saat mengirim komentar.');
    });
}

function deleteComment(commentId) {
    if (typeof promptDeleteComment === 'function') {
        promptDeleteComment(commentId);
        return;
    }

    if (!confirm('Yakin ingin menghapus komentar ini?')) return;

    fetch('/comments/' + commentId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update counters
            document.getElementById('modal-comments-count-pill').textContent = data.comments_count;
            if (activeCommentsPostId) {
                const cardCount = document.getElementById('comment-count-' + activeCommentsPostId);
                if (cardCount) cardCount.textContent = data.comments_count;
            }

            // Remove node from DOM
            const node = document.getElementById('comment-node-' + commentId);
            if (node) {
                node.style.transition = 'opacity 0.2s, transform 0.2s';
                node.style.opacity = '0';
                node.style.transform = 'scale(0.95)';
                setTimeout(() => node.remove(), 200);
            }
        } else {
            alert(data.error || 'Gagal menghapus komentar.');
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan saat menghapus komentar.');
    });
}
// ══════════════════════════════════════════════════════════════════════════
// INSTAGRAM-STYLE STORY VIEWER ENGINE (WITH LOGO BARENG & PROGRESS BARS)
// ══════════════════════════════════════════════════════════════════════════
const storyViewerItems = @json($storyViewerData->values());
let activeStoryIndex = 0;
let storyProgressInterval = null;
let storyProgressStartTime = 0;
let storyDurationMs = 5000;
let storyPausedProgress = 0;
let isStoryPaused = false;

// ─── TRACKING CERITA YANG SUDAH DILIHAT (BORDER ABU-ABU) ─────────────────
function getViewedStoryIds() {
    try {
        return JSON.parse(localStorage.getItem('bareng_viewed_stories') || '[]');
    } catch (e) {
        return [];
    }
}

function markStoryAsViewed(storyId) {
    if (!storyId) return;
    let viewed = getViewedStoryIds();
    if (!viewed.includes(storyId)) {
        viewed.push(storyId);
        try {
            localStorage.setItem('bareng_viewed_stories', JSON.stringify(viewed));
        } catch (e) {}
    }
    updateAllStoryRings();
}

function updateAllStoryRings() {
    const viewed = getViewedStoryIds();
    document.querySelectorAll('.story-user-btn').forEach(btn => {
        const rawIds = btn.getAttribute('data-story-ids');
        const userId = btn.getAttribute('data-story-user-id');
        if (!rawIds || !userId) return;

        try {
            const storyIds = JSON.parse(rawIds);
            const ringEl = document.getElementById('story-ring-' + userId);
            if (!ringEl || !Array.isArray(storyIds) || storyIds.length === 0) return;

            // Cek apakah SEMUA cerita user ini sudah pernah dilihat
            const allSeen = storyIds.every(id => viewed.includes(id));

            if (allSeen) {
                // Borderline menjadi ABU-ABU
                ringEl.classList.remove('ring-rose-500');
                ringEl.classList.add('ring-slate-300', 'ring-offset-1', 'opacity-70');
            } else {
                // Borderline tetap gradien / rose
                ringEl.classList.remove('ring-slate-300', 'opacity-70');
                ringEl.classList.add('ring-rose-500');
            }
        } catch (e) {}
    });
}

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

    // Tandai cerita saat ini sebagai telah dilihat & update border abu-abu
    markStoryAsViewed(story.id);

    // Populate active card
    const imgEl = document.getElementById('story-viewer-image');
    imgEl.src = story.image;

    const avatarEl = document.getElementById('story-viewer-avatar');
    const avatarFallback = document.getElementById('story-viewer-avatar-fallback');
    avatarEl.classList.remove('hidden');
    avatarFallback.classList.add('hidden');
    avatarFallback.classList.remove('flex');
    avatarFallback.textContent = story.user_initials || story.name.slice(0, 2).toUpperCase();
    avatarEl.onerror = () => {
        avatarEl.classList.add('hidden');
        avatarFallback.classList.remove('hidden');
        avatarFallback.classList.add('flex');
    };
    if (story.user_avatar) {
        avatarEl.src = story.user_avatar;
    } else {
        avatarEl.classList.add('hidden');
        avatarFallback.classList.remove('hidden');
        avatarFallback.classList.add('flex');
    }

    document.getElementById('story-viewer-name').textContent = story.username ? '@' + story.username : story.name;
    document.getElementById('story-viewer-time').textContent = story.created_at;
    document.getElementById('story-viewer-caption').textContent = story.caption || '';
    document.getElementById('story-highlight-id').value = story.id;
    document.getElementById('story-highlight-button').classList.toggle('hidden', !story.is_own || story.is_highlighted);

    const replyInput = document.getElementById('story-reply-input');
    if (replyInput) {
        replyInput.value = '';
        replyInput.placeholder = `Kirim balasan ke @${story.username || story.name}...`;
    }

    // Render Segmented Progress Bars
    renderStoryProgressBars(story);

    // Update Flanking Side Preview Cards (Instagram style preview kiri dan kanan)
    updateSidePreviewCards(index);

    // Show modal & start timer
    const modal = document.getElementById('story-viewer');
    modal.classList.replace('hidden', 'flex');
    document.body.style.overflow = 'hidden';

    startStoryTimer();
}

function renderStoryProgressBars(currentStory) {
    const container = document.getElementById('story-progress-container');
    if (!container) return;

    const totalBars = currentStory.user_stories_total || 1;
    const currentIndex = currentStory.user_story_index || 0;

    let barsHtml = '';
    for (let i = 0; i < totalBars; i++) {
        let innerStyle = 'width: 0%';
        if (i < currentIndex) {
            innerStyle = 'width: 100%';
        }
        barsHtml += `
            <div class="h-1 flex-1 rounded-full bg-white/25 overflow-hidden">
                <div id="story-progress-bar-${i}" class="h-full bg-white rounded-full transition-all duration-75" style="${innerStyle}"></div>
            </div>`;
    }
    container.innerHTML = barsHtml;
}

function startStoryTimer() {
    clearInterval(storyProgressInterval);
    isStoryPaused = false;
    storyPausedProgress = 0;
    updatePauseIcon(false);

    const story = storyViewerItems[activeStoryIndex];
    const currentIndex = story ? (story.user_story_index || 0) : 0;
    const currentBar = document.getElementById(`story-progress-bar-${currentIndex}`);

    storyProgressStartTime = Date.now();

    storyProgressInterval = setInterval(() => {
        if (isStoryPaused) return;

        const elapsed = Date.now() - storyProgressStartTime;
        const pct = Math.min((elapsed / storyDurationMs) * 100, 100);

        if (currentBar) {
            currentBar.style.width = pct + '%';
        }

        if (elapsed >= storyDurationMs) {
            clearInterval(storyProgressInterval);
            // Selesai -> lanjut ke cerita berikutnya
            changeStory(1);
        }
    }, 50);
}

function toggleStoryPause() {
    isStoryPaused = !isStoryPaused;
    if (isStoryPaused) {
        storyPausedProgress = Date.now() - storyProgressStartTime;
    } else {
        storyProgressStartTime = Date.now() - storyPausedProgress;
    }
    updatePauseIcon(isStoryPaused);
}

function updatePauseIcon(paused) {
    const icon = document.getElementById('story-pause-icon');
    if (!icon) return;
    if (paused) {
        icon.innerHTML = `<polygon points="5 3 19 12 5 21 5 3" fill="currentColor"></polygon>`;
    } else {
        icon.innerHTML = `<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>`;
    }
}

function updateSidePreviewCards(index) {
    const prevCard = document.getElementById('story-prev-card');
    const nextCard = document.getElementById('story-next-card');

    // Prev card
    if (index > 0 && storyViewerItems[index - 1]) {
        const pStory = storyViewerItems[index - 1];
        document.getElementById('story-prev-card-img').src = pStory.image;
        document.getElementById('story-prev-card-avatar').src = pStory.user_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(pStory.name)}&background=4154e8&color=fff`;
        document.getElementById('story-prev-card-username').textContent = '@' + (pStory.username || pStory.name);
        document.getElementById('story-prev-card-time').textContent = pStory.created_at;
        prevCard.classList.remove('invisible');
    } else {
        prevCard.classList.add('invisible');
    }

    // Next card
    if (index < storyViewerItems.length - 1 && storyViewerItems[index + 1]) {
        const nStory = storyViewerItems[index + 1];
        document.getElementById('story-next-card-img').src = nStory.image;
        document.getElementById('story-next-card-avatar').src = nStory.user_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(nStory.name)}&background=4154e8&color=fff`;
        document.getElementById('story-next-card-username').textContent = '@' + (nStory.username || nStory.name);
        document.getElementById('story-next-card-time').textContent = nStory.created_at;
        nextCard.classList.remove('invisible');
    } else {
        nextCard.classList.add('invisible');
    }
}

function closeStoryViewer() {
    clearInterval(storyProgressInterval);
    const modal = document.getElementById('story-viewer');
    modal.classList.replace('flex', 'hidden');
    document.body.style.overflow = '';
}

function changeStory(direction) {
    if (!storyViewerItems.length) return;

    const nextIndex = activeStoryIndex + direction;
    if (nextIndex >= 0 && nextIndex < storyViewerItems.length) {
        openStory(nextIndex);
    } else if (nextIndex >= storyViewerItems.length) {
        // Semua cerita sudah selesai dilihat!
        closeStoryViewer();
    }
}

// Balas Cerita ke Obrolan DM
function sendStoryReply() {
    const input = document.getElementById('story-reply-input');
    const text = input ? input.value.trim() : '';
    if (!text) return;

    const story = storyViewerItems[activeStoryIndex];
    if (!story || !story.user_id) return;

    fetch('{{ route("chat.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            receiver_id: story.user_id,
            message: `Membalas cerita: "${text}"`,
            media_url: story.image
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            showStoryToast('Balasan terkirim ke obrolan warga!');
            triggerStoryHeart();
        }
    })
    .catch(() => {
        showStoryToast('Gagal mengirim balasan.');
    });
}

function triggerStoryHeart() {
    const container = document.getElementById('story-floating-hearts');
    if (!container) return;

    for (let i = 0; i < 4; i++) {
        setTimeout(() => {
            const heart = document.createElement('div');
            heart.className = 'absolute text-rose-500 pointer-events-none animate-float-heart';
            heart.style.left = (35 + Math.random() * 30) + '%';
            heart.style.bottom = '20px';
            heart.innerHTML = `
                <svg class="h-9 w-9 fill-rose-500 drop-shadow-lg" viewBox="0 0 24 24">
                    <path d="m12 21.35-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>`;
            container.appendChild(heart);
            setTimeout(() => heart.remove(), 1200);
        }, i * 150);
    }
}

function showStoryToast(msg) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-20 left-1/2 -translate-x-1/2 z-50 rounded-2xl bg-black/80 px-4 py-2 text-xs font-bold text-white shadow-xl backdrop-blur-md animate-fade-in';
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}

// Keyboard shortcuts (ArrowLeft, ArrowRight, Escape, Space)
window.addEventListener('keydown', (e) => {
    const modal = document.getElementById('story-viewer');
    if (modal && !modal.classList.contains('hidden')) {
        if (e.key === 'ArrowRight') changeStory(1);
        if (e.key === 'ArrowLeft') changeStory(-1);
        if (e.key === 'Escape') closeStoryViewer();
        if (e.key === ' ' && e.target.tagName !== 'INPUT') {
            e.preventDefault();
            toggleStoryPause();
        }
    }
});

// Update border cerita abu-abu saat pertama kali halaman dimuat
window.addEventListener('DOMContentLoaded', updateAllStoryRings);

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
                btn.className = data.follows_you
                    ? 'mt-1.5 flex items-center gap-1 rounded-xl px-3 py-1 text-xs font-bold transition shadow-sm bg-[#4154e8] text-white hover:bg-[#3243d4]'
                    : 'mt-1.5 flex items-center gap-1 rounded-xl px-3 py-1 text-xs font-bold transition shadow-sm bg-slate-100 text-slate-700 hover:bg-slate-200';
                btn.innerText = data.follows_you ? 'Follow back' : 'Follow';
            }
        }
    });
}
</script>
@endsection
