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
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-white text-lg font-black shadow-sm ring-2 ring-[#4154e8]/20 select-none group-hover:ring-[#4154e8] transition">
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
                            <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-[#4154e8] text-sm font-extrabold text-white">
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
                                <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-[#4154e8] text-sm font-extrabold text-white">
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
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-white text-xs font-black shadow-sm select-none">
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
                    <div class="flex items-center gap-5">
                        @php
                            $isLiked = $post->isLikedBy(Auth::user());
                            $isSaved = Auth::user() ? Auth::user()->savedPosts()->where('post_id', $post->id)->exists() : false;
                        @endphp
                        {{-- Like Button --}}
                        <button type="button"
                                onclick="togglePostLike({{ $post->id }}, this)"
                                class="flex items-center gap-1.5 transition {{ $isLiked ? 'text-rose-500 font-bold' : 'text-slate-500 hover:text-rose-500' }}"
                                id="like-btn-{{ $post->id }}">
                            <span class="like-icon flex items-center justify-center">
                                @if ($isLiked)
                                    @include('partials.icons', ['name' => 'heart-solid', 'class' => 'h-5 w-5 text-rose-500 fill-rose-500'])
                                @else
                                    @include('partials.icons', ['name' => 'heart', 'class' => 'h-5 w-5 text-slate-500'])
                                @endif
                            </span>
                            <span class="like-count text-xs font-bold" id="like-count-{{ $post->id }}">{{ $post->likes_count }}</span>
                        </button>

                        {{-- Comment Button (Threads/X style) --}}
                        <button type="button"
                                onclick="openCommentsModal({{ $post->id }})"
                                class="flex items-center gap-1.5 text-slate-500 hover:text-[#4154e8] transition"
                                id="comment-btn-{{ $post->id }}">
                            @include('partials.icons', ['name' => 'comment', 'class' => 'h-5 w-5'])
                            <span class="comment-count text-xs font-bold" id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                        </button>

                        {{-- DM author --}}
                        <a href="{{ route('chat', ['user' => $post->user->username ?? $post->user->id]) }}"
                           class="flex items-center gap-1.5 text-slate-500 hover:text-[#4154e8] transition text-xs font-semibold">
                            @include('partials.icons', ['name' => 'send', 'class' => 'h-4 w-4'])
                            <span>Kirim DM</span>
                        </a>
                    </div>

                    <!-- Bookmark / Save button (Private Collection) -->
                    <button type="button"
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

function togglePostLike(postId, btn) {
    fetch('/posts/' + postId + '/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const countEl = document.getElementById('like-count-' + postId);
            if (countEl) countEl.textContent = data.likes_count;

            const iconWrap = btn.querySelector('.like-icon');
            if (data.liked) {
                btn.className = 'flex items-center gap-1.5 transition text-rose-500 font-bold';
                iconWrap.innerHTML = `<svg class="h-5 w-5 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="m12 21.35-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>`;
            } else {
                btn.className = 'flex items-center gap-1.5 transition text-slate-500 hover:text-rose-500';
                iconWrap.innerHTML = `<svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.6-7 10-7 10z"/></svg>`;
            }
        }
    });
}

function togglePostSave(postId, btn) {
    fetch('/posts/' + postId + '/toggle-save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.saved) {
                btn.className = 'transition text-[#4154e8] hover:opacity-80';
                btn.title = 'Hapus dari simpanan';
            } else {
                btn.className = 'transition text-slate-400 hover:text-[#4154e8]';
                btn.title = 'Simpan ke koleksi privat';
            }
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
