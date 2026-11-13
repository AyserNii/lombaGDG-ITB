@php
    $actor = $notif->actor;
    $post = $notif->post;
    $timeAgo = $notif->created_at->diffInDays(now()) > 30 ? $notif->created_at->format('M d') : $notif->created_at->diffForHumans(null, true, true);
    $actorInitials = $actor ? strtoupper(substr($actor->name, 0, 2)) : 'US';
    $isFollowing = $actor ? Auth::user()->isFollowing($actor->id) : false;
    $followsYou = $actor ? $actor->isFollowing(Auth::id()) : false;
@endphp

<div class="flex items-center justify-between gap-3 py-3 hover:bg-slate-50/80 px-2 rounded-2xl transition">
    <div class="flex items-center gap-3.5 min-w-0">
        <!-- Actor Avatar -->
        @if ($actor)
            <a href="{{ route('profile.show', $actor->username ?? $actor->id) }}" class="relative shrink-0">
                @if ($actor->avatar)
                    <span class="relative block h-11 w-11">
                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-[#4154e8] text-xs font-black text-white">{{ $actorInitials }}</span>
                        <img src="{{ $actor->avatar }}" alt="{{ $actor->name }}"
                             onerror="this.remove()"
                             class="relative h-11 w-11 rounded-full object-cover shadow-sm ring-2 ring-slate-100">
                    </span>
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#4154e8] text-white text-xs font-black shadow-sm ring-2 ring-slate-100">
                        {{ $actorInitials }}
                    </div>
                @endif

                @if ($notif->type === 'like')
                    <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-100">
                        <svg class="h-3 w-3 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </span>
                @elseif ($notif->type === 'follow')
                    <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-100">
                        <svg class="h-3 w-3 text-[#4154e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m14-9a3 3 0 1 0-6 0 3 3 0 0 0 6 0zM12 7a4 4 0 1 0-8 0 4 4 0 0 0 8 0z"/></svg>
                    </span>
                @elseif ($notif->type === 'comment')
                    <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-100">
                        <svg class="h-3 w-3 text-[#4154e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </span>
                @elseif ($notif->type === 'message')
                    <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-100">
                        @include('partials.icons', ['name' => 'dm', 'class' => 'h-3 w-3 text-emerald-600'])
                    </span>
                @endif
            </a>
        @else
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] text-xs font-bold shrink-0">
                @include('partials.icons', ['name' => 'bell-ring', 'class' => 'h-5 w-5'])
            </div>
        @endif

        <!-- Message text -->
        <div class="min-w-0 text-xs text-slate-800 leading-snug">
            @if ($actor)
                <a href="{{ route('profile.show', $actor->username ?? $actor->id) }}" class="font-extrabold text-slate-900 hover:text-[#4154e8] transition">
                    {{ $actor->username ?? $actor->name }}
                </a>
            @endif
            <span class="text-slate-600">
                @if ($notif->type === 'follow')
                    mulai mengikuti kamu.
                @elseif ($notif->type === 'like')
                    menyukai postingan kamu.
                @elseif ($notif->type === 'comment')
                    mengomentari postingan kamu.
                @else
                    {{ $notif->message }}
                @endif
            </span>
            <span class="text-slate-400 font-semibold text-[11px] ml-1">{{ $timeAgo }}</span>
            @if ($post && in_array($notif->type, ['like', 'comment'], true))
                <button type="button" onclick="openPostDetailModal({{ $post->id }})"
                        class="mt-2 flex w-full max-w-sm items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 p-2 text-left hover:bg-indigo-50">
                    @if ($post->image)
                        <img src="{{ $post->image }}" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                    @endif
                    <span class="min-w-0">
                        <span class="block truncate text-[10px] font-bold text-slate-500">Postingan: {{ $post->caption ?: 'Foto komunitas' }}</span>
                        @if ($notif->type === 'comment' && $notif->comment)
                            <span class="mt-0.5 block line-clamp-2 text-[10px] text-slate-700">Komentar: {{ $notif->comment->body }}</span>
                        @endif
                    </span>
                </button>
            @elseif ($notif->type === 'comment' && $notif->comment)
                <p class="mt-1 text-[11px] text-slate-600">Komentar: {{ $notif->comment->body }}</p>
            @endif
        </div>
    </div>

    <!-- Right Side: Following Button or Post Thumbnail -->
    <div class="shrink-0 pl-2">
        @if ($notif->type === 'follow' && $actor && $actor->id !== Auth::id())
            <button type="button"
                    data-follows-you="{{ $followsYou ? 'true' : 'false' }}"
                    onclick="toggleNotifFollow({{ $actor->id }}, this)"
                    class="rounded-xl px-4 py-1.5 text-xs font-extrabold transition {{ $isFollowing ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-[#4154e8] hover:bg-[#3243d4] text-white shadow-sm' }}">
                {{ $isFollowing ? 'Following' : ($followsYou ? 'Follow back' : 'Follow') }}
            </button>
        @elseif ($notif->type === 'message' && $actor)
            <a href="{{ route('chat', ['user' => $actor->username ?? $actor->id]) }}"
               class="rounded-xl bg-[#4154e8] px-3 py-2 text-[11px] font-extrabold text-white hover:bg-[#3243d4]">
                Balas
            </a>
        @endif
    </div>
</div>
