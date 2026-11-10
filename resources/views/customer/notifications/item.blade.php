@php
    $actor = $notif->actor;
    $post = $notif->post;
    $timeAgo = $notif->created_at->diffInDays(now()) > 30 ? $notif->created_at->format('M d') : $notif->created_at->diffForHumans(null, true, true);
    $actorInitials = $actor ? strtoupper(substr($actor->name, 0, 2)) : 'US';
    $isFollowing = $actor ? Auth::user()->isFollowing($actor->id) : false;
@endphp

<div class="flex items-center justify-between gap-3 py-3 hover:bg-slate-50/80 px-2 rounded-2xl transition">
    <div class="flex items-center gap-3.5 min-w-0">
        <!-- Actor Avatar -->
        @if ($actor)
            <a href="{{ route('profile.show', $actor->username ?? $actor->id) }}" class="relative shrink-0">
                @if ($actor->avatar)
                    <img src="{{ $actor->avatar }}" alt="{{ $actor->name }}" class="h-11 w-11 rounded-full object-cover shadow-sm ring-2 ring-slate-100">
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
                @endif
            </a>
        @else
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] text-xs font-bold shrink-0">
                @include('partials.icons', ['name' => 'bell', 'class' => 'h-5 w-5'])
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
                {{ preg_replace('/^' . preg_quote($actor->username ?? '', '/') . '\s*/', '', $notif->message) }}
            </span>
            <span class="text-slate-400 font-semibold text-[11px] ml-1">{{ $timeAgo }}</span>
        </div>
    </div>

    <!-- Right Side: Following Button or Post Thumbnail -->
    <div class="shrink-0 pl-2">
        @if ($notif->type === 'follow' && $actor && $actor->id !== Auth::id())
            <button type="button"
                    onclick="toggleNotifFollow({{ $actor->id }}, this)"
                    class="rounded-xl px-4 py-1.5 text-xs font-extrabold transition {{ $isFollowing ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-[#4154e8] hover:bg-[#3243d4] text-white shadow-sm' }}">
                {{ $isFollowing ? 'Following' : 'Follow' }}
            </button>
        @elseif ($post && $post->image)
            <a href="{{ route('profile.show', $post->user->username ?? $post->user_id) }}" class="block h-11 w-11 overflow-hidden rounded-xl shadow-sm border border-slate-200 hover:opacity-80 transition">
                <img src="{{ $post->image }}" alt="Post thumbnail" class="h-full w-full object-cover">
            </a>
        @endif
    </div>
</div>
