@extends('layouts.app')

@section('title', '@' . ($targetUser->username ?? $targetUser->name) . ' · Profil')

@section('content')
@php
    $rawName = trim($targetUser->name ?? 'User');
    $nameParts = preg_split('/\s+/', $rawName);
    $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
    $activeTab = request()->query('tab', 'posts');
@endphp

<div class="mx-auto max-w-4xl space-y-6 pb-12">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    <!-- Instagram Style Profile Card -->
    <div class="rounded-[32px] bg-white p-6 sm:p-10 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 sm:gap-12">
            <!-- Avatar with Instagram Note... bubble -->
            <div class="relative shrink-0 flex flex-col items-center">
                <!-- Note Bubble (Foto 1) -->
                @if ($targetUser->note || $isOwnProfile)
                    <div class="relative mb-2.5 z-10">
                        <button type="button"
                                @if($isOwnProfile) onclick="openNoteModal()" title="Klik untuk ubah note" @endif
                                class="group relative flex items-center gap-1.5 rounded-2xl bg-white/95 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-md ring-1 ring-slate-200/80 backdrop-blur-sm transition hover:scale-105 active:scale-95">
                            <span class="truncate max-w-[140px]">{{ $targetUser->note ?: 'Note...' }}</span>
                            @if ($isOwnProfile)
                                <span class="text-[#4154e8] opacity-0 group-hover:opacity-100 transition">@include('partials.icons', ['name' => 'edit', 'class' => 'h-3 w-3'])</span>
                            @endif
                            <!-- Bubble tail -->
                            <span class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 h-2.5 w-2.5 rotate-45 bg-white ring-1 ring-slate-200/80 [clip-path:polygon(100%_100%,0%_100%,100%_0%)]"></span>
                        </button>
                    </div>
                @endif

                <div class="relative">
                    @if (!empty($targetUser->avatar))
                        <img src="{{ $targetUser->avatar }}" alt="{{ $targetUser->name }}"
                             class="h-28 w-28 sm:h-36 sm:w-36 rounded-full object-cover ring-4 ring-slate-100 shadow-lg">
                    @else
                        <div class="flex h-28 w-28 sm:h-36 sm:w-36 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] text-3xl sm:text-4xl font-black shadow-lg ring-4 ring-slate-100 select-none">
                            {{ $userInitials }}
                        </div>
                    @endif

                    @if ($isOwnProfile)
                        <a href="{{ route('settings') }}" title="Ganti foto profil"
                           class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full bg-[#4154e8] text-white ring-2 ring-white shadow transition hover:scale-110">
                            @include('partials.icons', ['name' => 'camera', 'class' => 'h-4 w-4'])
                        </a>
                    @endif
                </div>
            </div>

            <!-- Profile Info & Actions -->
            <div class="flex-1 text-center sm:text-left space-y-4">
                <!-- Username & Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
                    <div class="flex items-center justify-center sm:justify-start gap-3">
                        <h1 class="text-xl sm:text-2xl font-black text-[#1e293b] tracking-tight">
                            {{ $targetUser->username ?? $targetUser->name }}
                        </h1>
                    </div>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        @if ($isOwnProfile)
                            <button type="button" onclick="openCreatePostModal()"
                                    class="flex items-center gap-1.5 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-4 py-2 text-xs font-bold text-white transition active:scale-95 shadow-sm">
                                @include('partials.icons', ['name' => 'plus', 'class' => 'h-3.5 w-3.5'])
                                Buat Post
                            </button>
                            <a href="{{ route('settings') }}"
                               class="flex items-center gap-1.5 rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-800 transition hover:bg-slate-200 active:scale-95 shadow-sm">
                                Edit profile
                            </a>
                            <a href="{{ route('settings') }}"
                               class="flex items-center gap-1.5 rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-800 transition hover:bg-slate-200 active:scale-95 shadow-sm">
                                View archive
                            </a>
                        @else
                            <!-- Follow / Unfollow Button for other user -->
                            <button type="button"
                                    id="follow-btn-{{ $targetUser->id }}"
                                    onclick="toggleFollow({{ $targetUser->id }})"
                                    class="rounded-xl px-5 py-2 text-xs font-extrabold transition shadow-sm active:scale-95 {{ $isFollowing ? 'bg-slate-100 text-slate-800 hover:bg-slate-200' : 'bg-[#4154e8] text-white hover:bg-[#3243d4] shadow-[#4154e8]/25' }}">
                                <span id="follow-text-{{ $targetUser->id }}">{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                            </button>

                            <a href="{{ route('chat', ['user' => $targetUser->username ?? $targetUser->id]) }}"
                               class="flex items-center gap-1.5 rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-800 transition hover:bg-slate-200 active:scale-95 shadow-sm">
                                Message
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Stats (Posts, Followers, Following) -->
                <div class="flex items-center justify-center sm:justify-start gap-8 pt-1 text-sm">
                    <div>
                        <span class="font-black text-[#1e293b]">{{ $postsCount }}</span>
                        <span class="text-slate-500 font-medium ml-1">posts</span>
                    </div>

                    <button type="button" onclick="openFollowModal('followers', {{ $targetUser->id }})" class="hover:opacity-75 transition">
                        <span id="followers-count-display" class="font-black text-[#1e293b]">{{ $followersCount }}</span>
                        <span class="text-slate-500 font-medium ml-1">followers</span>
                    </button>

                    <button type="button" onclick="openFollowModal('following', {{ $targetUser->id }})" class="hover:opacity-75 transition">
                        <span class="font-black text-[#1e293b]">{{ $followingCount }}</span>
                        <span class="text-slate-500 font-medium ml-1">following</span>
                    </button>
                </div>

                <!-- Full Name, Bio, and Website -->
                <div class="space-y-1.5 pt-1 text-xs sm:text-sm">
                    <p class="font-extrabold text-[#1e293b]">{{ $targetUser->name }}</p>

                    @if ($targetUser->bio)
                        <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $targetUser->bio }}</p>
                    @endif

                    @if ($targetUser->website)
                        <div class="flex items-center gap-1 text-[#4154e8] font-bold">
                            @include('partials.icons', ['name' => 'link', 'class' => 'h-3.5 w-3.5'])
                            <a href="{{ $targetUser->website }}" target="_blank" rel="noopener noreferrer" class="hover:underline truncate max-w-xs">
                                {{ preg_replace('#^https?://#', '', $targetUser->website) }}
                            </a>
                        </div>
                    @endif

                    <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2 text-[11px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5">
                            @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-3 w-3 text-[#4154e8]'])
                            {{ $targetUser->location ?? 'Beji, Depok' }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 text-emerald-700">
                            @include('partials.icons', ['name' => 'shield', 'class' => 'h-3 w-3 text-emerald-600'])
                            {{ $targetUser->role ?? 'Customer' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Story Highlights (Foto 1) -->
        <div class="mt-8 border-t border-slate-100 pt-6">
            <div class="flex items-center gap-6 overflow-x-auto pb-2">
                @foreach ($highlights as $hl)
                    @php
                        $firstHighlightStory = $hl->stories->first();
                        $highlightIndex = $highlights->flatMap(fn ($item) => $item->stories)->search(fn ($story) => $story->id === $firstHighlightStory?->id);
                    @endphp
                    <button type="button" onclick="openProfileHighlight({{ $highlightIndex }})" class="flex shrink-0 flex-col items-center gap-1.5 group">
                        <div class="rounded-full p-[2.5px] ring-2 ring-slate-200 group-hover:ring-[#4154e8] transition">
                            @if ($firstHighlightStory)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($firstHighlightStory->image) }}" alt="" class="h-16 w-16 rounded-full border-2 border-white object-cover shadow-sm transition group-hover:scale-105 sm:h-[72px] sm:w-[72px]">
                            @else
                                <span class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-white bg-slate-100 text-slate-400 sm:h-[72px] sm:w-[72px]">?</span>
                            @endif
                        </div>
                        <span class="max-w-20 truncate text-xs font-semibold text-slate-700">{{ $hl->title }}</span>
                    </button>
                @endforeach

                @if ($isOwnProfile)
                    <button type="button" onclick="openHighlightComposer()" class="flex shrink-0 flex-col items-center gap-1.5 group">
                        <div class="rounded-full p-[2.5px] ring-2 ring-dashed ring-slate-300 transition group-hover:ring-[#4154e8]">
                            <span class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-white bg-slate-50 text-slate-400 transition group-hover:bg-blue-50 group-hover:text-[#4154e8] sm:h-[72px] sm:w-[72px]">
                                @include('partials.icons', ['name' => 'plus', 'class' => 'h-6 w-6'])
                            </span>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">Baru</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Instagram Tab Navigation -->
    <div class="flex items-center justify-center border-t border-slate-200/80 text-xs font-bold tracking-wider uppercase">
        <!-- Posts Tab -->
        <a href="?tab=posts"
           class="flex items-center gap-2 border-t-2 py-4 px-6 transition {{ $activeTab === 'posts' ? 'border-[#1e293b] text-[#1e293b]' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <rect x="3" y="3" width="7" height="7" stroke-width="2" stroke-linecap="round"/>
                <rect x="14" y="3" width="7" height="7" stroke-width="2" stroke-linecap="round"/>
                <rect x="14" y="14" width="7" height="7" stroke-width="2" stroke-linecap="round"/>
                <rect x="3" y="14" width="7" height="7" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>POSTS</span>
        </a>

        <!-- Saved Tab: STRICTLY SHOWN ONLY FOR OWN PROFILE (Private as requested!) -->
        @if ($isOwnProfile)
            <a href="?tab=saved"
               class="flex items-center gap-2 border-t-2 py-4 px-6 transition {{ $activeTab === 'saved' ? 'border-[#1e293b] text-[#1e293b]' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <span>SAVED</span>
                <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full font-bold ml-1">Privat</span>
            </a>
        @endif
    </div>

    <!-- Tab Content -->
    @if ($activeTab === 'saved' && $isOwnProfile)
        <!-- SAVED POSTS (Private) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between rounded-2xl bg-slate-100/80 px-4 py-3 text-xs text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-slate-700">@include('partials.icons', ['name' => 'lock', 'class' => 'h-3 w-3'])</span>
                    <span><strong>Koleksi Tersimpan Pribadi</strong> — Hanya kamu yang dapat melihat postingan yang kamu simpan. Orang lain tidak bisa melihatnya.</span>
                </div>
                <span class="font-extrabold text-slate-700">{{ $savedPosts->count() }} item</span>
            </div>

            @if ($savedPosts->isEmpty())
                <div class="rounded-3xl bg-white p-12 text-center text-slate-400 border border-slate-100">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                    </div>
                    <p class="font-bold text-slate-700 text-sm">Belum ada postingan yang disimpan</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Klik ikon bookmark di postingan komunitas atau sesi gotong royong untuk menyimpannya ke koleksi privat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-3 gap-2 sm:gap-6">
                    @foreach ($savedPosts as $p)
                        <div class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-100 cursor-pointer shadow-sm">
                            <img src="{{ $p->image }}" alt="Saved Post" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            <!-- Overlay on Hover (Persis Foto 1: Solid SVG Icons, Centered, White Drop-shadow) -->
                            <div class="absolute inset-0 flex items-center justify-center gap-6 bg-black/50 opacity-0 group-hover:opacity-100 transition duration-200 text-white select-none">
                                <div class="flex items-center gap-2 font-black text-base sm:text-lg">
                                    @include('partials.icons', ['name' => 'heart-solid', 'class' => 'h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow'])
                                    <span>{{ $p->likes_count }}</span>
                                </div>
                                <div class="flex items-center gap-2 font-black text-base sm:text-lg">
                                    @include('partials.icons', ['name' => 'comment-solid', 'class' => 'h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow'])
                                    <span>{{ $p->comments_count }}</span>
                                </div>
                            </div>
                            <span class="absolute top-2 right-2 rounded-lg bg-black/60 px-2 py-1 text-[10px] font-extrabold text-white backdrop-blur-sm">
                                {{ $p->type }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <!-- POSTS GRID -->
        @if ($posts->isEmpty())
            <div class="rounded-3xl bg-white p-12 text-center text-slate-400 border border-slate-100">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                    @include('partials.icons', ['name' => 'camera', 'class' => 'h-8 w-8'])
                </div>
                <p class="font-bold text-slate-700 text-sm">Belum ada postingan</p>
                <p class="text-xs text-slate-400 mt-1">Postingan momen sesi, gotong royong, dan hemat warga akan muncul di sini.</p>
                @if ($isOwnProfile)
                    <button type="button" onclick="openCreatePostModal()" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-4 py-2 text-xs font-bold text-white transition shadow-sm">
                        @include('partials.icons', ['name' => 'plus', 'class' => 'h-3.5 w-3.5']) Buat Postingan Pertama
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-3 gap-2 sm:gap-6">
                @foreach ($posts as $post)
                    <div class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-100 cursor-pointer shadow-sm">
                        <img src="{{ $post->image }}" alt="{{ $post->caption }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <!-- Overlay on Hover (Persis Foto 1: Solid SVG Icons, Centered, White Drop-shadow) -->
                        <div class="absolute inset-0 flex items-center justify-center gap-6 bg-black/50 opacity-0 group-hover:opacity-100 transition duration-200 text-white select-none">
                            <div class="flex items-center gap-2 font-black text-base sm:text-lg">
                                @include('partials.icons', ['name' => 'heart-solid', 'class' => 'h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow'])
                                <span>{{ $post->likes_count }}</span>
                            </div>
                            <div class="flex items-center gap-2 font-black text-base sm:text-lg">
                                @include('partials.icons', ['name' => 'comment-solid', 'class' => 'h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow'])
                                <span>{{ $post->comments_count }}</span>
                            </div>

                            <!-- Toggle Bookmark Button -->
                            <button type="button" onclick="event.stopPropagation(); toggleSavePost({{ $post->id }})" class="absolute top-3 right-3 p-1.5 rounded-full bg-black/40 hover:bg-black/70 text-white transition" title="Simpan secara privat">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>

@if ($isOwnProfile)
    <div id="highlight-composer" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="highlight-composer-title">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between">
                <h2 id="highlight-composer-title" class="text-lg font-extrabold text-slate-900">Buat sorotan</h2>
                <button type="button" onclick="closeHighlightComposer()" class="rounded-full p-2 text-slate-500 hover:bg-slate-100" aria-label="Tutup">@include('partials.icons', ['name' => 'close', 'class' => 'h-5 w-5'])</button>
            </div>
            <p class="mt-1 text-sm text-slate-500">Pilih cerita dari arsipmu. Cerita dalam sorotan tetap tampil setelah 24 jam.</p>
            @if ($archivedStories->isEmpty())
                <p class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">Belum ada cerita untuk disorot. Bagikan cerita lebih dulu dari halaman komunitas.</p>
                <a href="{{ route('community') }}" class="mt-4 inline-flex rounded-xl bg-[#4154e8] px-4 py-2.5 text-sm font-bold text-white">Buat cerita</a>
            @else
                <form method="POST" action="{{ route('stories.highlights.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <label class="block text-sm font-bold text-slate-700">
                        Nama sorotan
                        <input type="text" name="title" maxlength="40" required value="{{ old('title') }}" class="mt-2 block w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-normal outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                        @error('title') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <div class="max-h-64 space-y-2 overflow-y-auto">
                        @foreach ($archivedStories as $story)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-100 p-2 hover:bg-slate-50">
                                <input type="checkbox" name="story_ids[]" value="{{ $story->id }}" class="h-4 w-4 rounded border-slate-300 text-[#4154e8] focus:ring-[#4154e8]">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($story->image) }}" alt="" class="h-12 w-12 rounded-lg object-cover">
                                <span class="min-w-0 flex-1 truncate text-sm text-slate-700">{{ $story->caption ?: 'Cerita ' . $story->created_at->format('d M Y') }}</span>
                                <span class="text-xs text-slate-400">{{ $story->created_at->format('d/m/y') }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('story_ids') <span class="block text-xs text-rose-600">{{ $message }}</span> @enderror
                    <button type="submit" class="w-full rounded-xl bg-[#4154e8] px-4 py-3 text-sm font-extrabold text-white hover:bg-[#3243d4]">Simpan ke sorotan</button>
                </form>
            @endif
        </div>
    </div>

@endif

<div id="profile-highlight-viewer" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-3 sm:p-8" role="dialog" aria-modal="true" aria-label="Sorotan cerita">
    <button type="button" onclick="closeProfileHighlight()" class="absolute right-4 top-4 rounded-full bg-white/15 p-3 text-white hover:bg-white/25" aria-label="Tutup">@include('partials.icons', ['name' => 'close', 'class' => 'h-5 w-5'])</button>
    <button type="button" onclick="changeProfileHighlight(-1)" class="absolute left-3 top-1/2 rounded-full bg-white/15 px-3 py-2 text-white sm:left-8" aria-label="Cerita sebelumnya">‹</button>
    <article class="relative flex h-[min(82vh,760px)] w-full max-w-md flex-col overflow-hidden rounded-3xl bg-slate-900">
        <img id="profile-highlight-image" src="" alt="Sorotan cerita" class="absolute inset-0 h-full w-full object-contain">
        <p id="profile-highlight-caption" class="relative z-10 mt-auto bg-gradient-to-t from-black/70 to-transparent p-5 text-sm text-white"></p>
    </article>
    <button type="button" onclick="changeProfileHighlight(1)" class="absolute right-3 top-1/2 rounded-full bg-white/15 px-3 py-2 text-white sm:right-8" aria-label="Cerita berikutnya">›</button>
</div>

<!-- Modal: Followers & Following list -->
<div id="follow-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-fade-in">
    <div class="w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl border border-slate-100 animate-scale-up">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 id="follow-modal-title" class="text-base font-extrabold text-[#1e293b]">Followers</h3>
            <button type="button" onclick="closeFollowModal()" class="p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Search in modal -->
        <div class="mt-3">
            <input type="text" id="follow-search-input" placeholder="Cari akun..." oninput="filterModalUsers(this.value)"
                   class="w-full rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-[#4154e8]/30">
        </div>

        <div id="follow-modal-list" class="mt-4 max-h-80 overflow-y-auto space-y-3 divide-y divide-slate-50">
            <!-- Dynamic Users inserted here -->
            <div class="text-center py-6 text-xs text-slate-400">Memuat data...</div>
        </div>
    </div>
</div>

<!-- Modal: Edit Status Note (Foto 1 "Note...") -->
<div id="note-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-[#1e293b]">Bagikan Catatan Status</h3>
            <button type="button" onclick="closeNoteModal()" class="p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('profile.note') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Kamu (maks 60 karakter)</label>
                <input type="text" name="note" value="{{ $targetUser->note }}" maxlength="60"
                       placeholder="Misal: Sedang mencari teman makan siang"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                <p class="text-[11px] text-slate-400 mt-1">Catatan ini akan tampil mengambang di atas foto profilmu seperti di Instagram.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeNoteModal()" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-[#4154e8] px-5 py-2 text-xs font-extrabold text-white shadow-md hover:bg-[#3243d4] transition">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentModalUsers = [];
const profileHighlightItems = @json($highlights->flatMap(fn ($highlight) => $highlight->stories->map(fn ($story) => [
    'image' => \Illuminate\Support\Facades\Storage::disk('public')->url($story->image),
    'caption' => $story->caption,
]))->values());
let activeProfileHighlightIndex = 0;

function openHighlightComposer() {
    const composer = document.getElementById('highlight-composer');
    if (composer) composer.classList.replace('hidden', 'flex');
}

function closeHighlightComposer() {
    const composer = document.getElementById('highlight-composer');
    if (composer) composer.classList.replace('flex', 'hidden');
}

function openProfileHighlight(index) {
    if (!profileHighlightItems[index]) return;

    activeProfileHighlightIndex = index;
    document.getElementById('profile-highlight-image').src = profileHighlightItems[index].image;
    document.getElementById('profile-highlight-caption').textContent = profileHighlightItems[index].caption || '';
    document.getElementById('profile-highlight-viewer').classList.replace('hidden', 'flex');
}

function closeProfileHighlight() {
    document.getElementById('profile-highlight-viewer').classList.replace('flex', 'hidden');
}

function changeProfileHighlight(direction) {
    if (!profileHighlightItems.length) return;
    const nextIndex = (activeProfileHighlightIndex + direction + profileHighlightItems.length) % profileHighlightItems.length;
    openProfileHighlight(nextIndex);
}

@if ($errors->has('title') || $errors->has('story_ids'))
window.addEventListener('DOMContentLoaded', openHighlightComposer);
@endif

function toggleFollow(userId) {
    const btn = document.getElementById('follow-btn-' + userId);
    const textSpan = document.getElementById('follow-text-' + userId);

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
                if (btn) {
                    btn.classList.remove('bg-[#4154e8]', 'text-white', 'hover:bg-[#3243d4]');
                    btn.classList.add('bg-slate-100', 'text-slate-800', 'hover:bg-slate-200');
                }
                if (textSpan) textSpan.innerText = 'Following';
            } else {
                if (btn) {
                    btn.classList.remove('bg-slate-100', 'text-slate-800', 'hover:bg-slate-200');
                    btn.classList.add('bg-[#4154e8]', 'text-white', 'hover:bg-[#3243d4]');
                }
                if (textSpan) textSpan.innerText = 'Follow';
            }

            const followersDisplay = document.getElementById('followers-count-display');
            if (followersDisplay && data.followers_count !== undefined) {
                followersDisplay.innerText = data.followers_count;
            }
        }
    })
    .catch(err => console.error(err));
}

function toggleSavePost(postId) {
    fetch('/posts/' + postId + '/toggle-save', {
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
            alert(data.message);
        }
    });
}

function openFollowModal(type, userId) {
    const modal = document.getElementById('follow-modal');
    const title = document.getElementById('follow-modal-title');
    const list = document.getElementById('follow-modal-list');

    title.innerText = type === 'followers' ? 'Followers' : 'Following';
    modal.classList.remove('hidden');

    fetch('/users/' + userId + '/' + type)
        .then(res => res.json())
        .then(data => {
            currentModalUsers = data.users || [];
            renderModalUsers(currentModalUsers);
        });
}

function closeFollowModal() {
    document.getElementById('follow-modal').classList.add('hidden');
}

function filterModalUsers(keyword) {
    const q = keyword.toLowerCase();
    const filtered = currentModalUsers.filter(u => u.name.toLowerCase().includes(q) || u.username.toLowerCase().includes(q));
    renderModalUsers(filtered);
}

function renderModalUsers(users) {
    const list = document.getElementById('follow-modal-list');
    if (!users.length) {
        list.innerHTML = '<div class="text-center py-6 text-xs text-slate-400">Tidak ada akun ditemukan.</div>';
        return;
    }

    list.innerHTML = users.map(u => `
        <div class="flex items-center justify-between py-2 pt-3">
            <a href="${u.profile_url}" class="flex items-center gap-3 min-w-0">
                ${u.avatar ? `
                    <img src="${u.avatar}" class="h-10 w-10 rounded-full object-cover">
                ` : `
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-xs font-black">
                        ${u.initials}
                    </div>
                `}
                <div class="min-w-0 text-left">
                    <p class="text-xs font-bold text-slate-900 truncate leading-tight">${u.username}</p>
                    <p class="text-[11px] text-slate-500 truncate">${u.name}</p>
                </div>
            </a>

            ${!u.is_self ? `
                <button type="button"
                        onclick="toggleFollowFromModal(${u.id}, this)"
                        class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition shadow-sm ${u.is_following ? 'bg-slate-100 text-slate-800 hover:bg-slate-200' : 'bg-[#4154e8] text-white hover:bg-[#3243d4]'}">
                    ${u.is_following ? 'Following' : 'Follow'}
                </button>
            ` : ''}
        </div>
    `).join('');
}

function toggleFollowFromModal(userId, btn) {
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
                btn.className = 'rounded-xl px-3.5 py-1.5 text-xs font-bold transition shadow-sm bg-slate-100 text-slate-800 hover:bg-slate-200';
                btn.innerText = 'Following';
            } else {
                btn.className = 'rounded-xl px-3.5 py-1.5 text-xs font-bold transition shadow-sm bg-[#4154e8] text-white hover:bg-[#3243d4]';
                btn.innerText = 'Follow';
            }
        }
    });
}

function openNoteModal() {
    document.getElementById('note-modal').classList.remove('hidden');
}

function closeNoteModal() {
    document.getElementById('note-modal').classList.add('hidden');
}
</script>
@endsection
