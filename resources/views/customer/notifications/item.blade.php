@php
    $actor = $notif->actor;
    $post = $notif->post;
    $timeAgo = $notif->created_at->diffInDays(now()) > 30 ? $notif->created_at->format('M d') : $notif->created_at->diffForHumans(null, true, true);
    $actorInitials = $actor ? strtoupper(substr($actor->name, 0, 2)) : 'US';
    $isFollowing = $actor ? Auth::user()->isFollowing($actor->id) : false;
@endphp

<div class="flex items-center justify-between gap-3 py-2.5">
    <div class="flex items-center gap-3 min-w-0">
        <!-- Actor Avatar -->
        @if ($actor)
            <a href="{{ route('profile.show', $actor->username ?? $actor->id) }}" class="shrink-0">
                @if ($actor->avatar)
                    <img src="{{ $actor->avatar }}" alt="{{ $actor->name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-slate-800">
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-xs font-black ring-2 ring-slate-800">
                        {{ $actorInitials }}
                    </div>
                @endif
            </a>
        @else
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-600 text-xs font-bold shrink-0">
                @include('partials.icons', ['name' => 'bell', 'class' => 'h-5 w-5'])
            </div>
        @endif

        <!-- Message text -->
        <div class="min-w-0 text-xs text-slate-200 leading-snug">
            @if ($actor)
                <a href="{{ route('profile.show', $actor->username ?? $actor->id) }}" class="font-extrabold text-white hover:underline">
                    {{ $actor->username ?? $actor->name }}
                </a>
            @endif
            <span class="text-slate-300">
                {{ preg_replace('/^' . preg_quote($actor->username ?? '', '/') . '\s*/', '', $notif->message) }}
            </span>
            <span class="text-slate-500 font-medium text-[11px] ml-1">{{ $timeAgo }}</span>
        </div>
    </div>

    <!-- Right Side: Following Button or Post Thumbnail (Foto 4) -->
    <div class="shrink-0 pl-2">
        @if ($notif->type === 'follow' && $actor && $actor->id !== Auth::id())
            <button type="button"
                    onclick="toggleNotifFollow({{ $actor->id }}, this)"
                    class="rounded-xl px-4 py-1.5 text-xs font-bold transition {{ $isFollowing ? 'bg-[#262626] hover:bg-slate-700 text-white' : 'bg-[#0095f6] hover:bg-[#1877f2] text-white' }}">
                {{ $isFollowing ? 'Following' : 'Follow' }}
            </button>
        @elseif ($post && $post->image)
            <a href="{{ route('profile.show', $post->user->username ?? $post->user_id) }}" class="block h-10 w-10 overflow-hidden rounded-lg shadow-sm border border-slate-700 hover:opacity-80 transition">
                <img src="{{ $post->image }}" alt="Post thumbnail" class="h-full w-full object-cover">
            </a>
        @endif
    </div>
</div>
