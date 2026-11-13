@extends('layouts.app')

@section('title', 'Diskusi Komunitas')

@section('content')
<main class="mx-auto max-w-5xl space-y-6 px-4 pb-24">
    <a href="{{ route('community') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-[#4154e8]">
        <span aria-hidden="true">&larr;</span> Kembali ke komunitas
    </a>

    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 p-5">
            @if ($post->user?->avatar)
                <img src="{{ $post->user->avatar }}" alt="" onerror="this.classList.add('hidden');this.nextElementSibling.classList.remove('hidden');this.nextElementSibling.classList.add('flex')" class="h-11 w-11 rounded-full object-cover">
            @endif
            <span class="{{ $post->user?->avatar ? 'hidden ' : '' }}flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-xs font-black text-white">
                {{ strtoupper(mb_substr($post->user?->name ?? 'User', 0, 2)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-extrabold text-slate-900">{{ $post->user?->name ?? 'User' }}</p>
                <p class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }} · {{ $post->type }}</p>
            </div>
        </div>

        @if ($post->image)
            <img src="{{ $post->image }}" alt="Foto postingan" class="max-h-[70vh] w-full bg-slate-100 object-contain">
        @endif

        @if ($post->caption)
            <p class="whitespace-pre-line px-5 pt-4 text-sm leading-6 text-slate-800">{{ $post->caption }}</p>
        @endif

        <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 p-5">
            <div class="inline-flex items-center rounded-full border border-slate-200 bg-white p-1 shadow-sm">
                @foreach (['like' => ['Suka', 'likes_total'], 'dislike' => ['Tidak suka', 'dislikes_total']] as $reactionType => [$label, $countField])
                    <button type="button" data-reaction="{{ $reactionType }}" aria-pressed="{{ $reaction === $reactionType ? 'true' : 'false' }}"
                            onclick="toggleCommunityReaction('{{ $reactionType }}')"
                            class="community-reaction inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold transition {{ $reaction === $reactionType ? 'bg-blue-50 text-[#4154e8]' : 'text-slate-600 hover:bg-slate-50' }}">
                        @if ($reactionType === 'like')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 10v12m0-12 4-8a3 3 0 0 1 3 3v3h5a2 2 0 0 1 2 2l-1.5 9a2 2 0 0 1-2 2H7m0-12H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/></svg>
                        @else
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14V2m0 12-4 8a3 3 0 0 1-3-3v-3H5a2 2 0 0 1-2-2l1.5-9a2 2 0 0 1 2-2H17m0 12h3a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/></svg>
                        @endif
                        <span>{{ $label }}</span>
                        <span id="{{ $countField }}">{{ $post->{$countField} }}</span>
                    </button>
                    @if ($reactionType === 'like')
                        <span class="mx-1 h-5 w-px bg-slate-200"></span>
                    @endif
                @endforeach
            </div>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-600">
                @include('partials.icons', ['name' => 'comment', 'class' => 'h-4 w-4'])
                {{ $post->comments_count }} Komentar
            </span>
        </div>
    </article>

    <section id="komentar" class="space-y-5 scroll-mt-24">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#4154e8]">
                    @include('partials.icons', ['name' => 'comment', 'class' => 'h-5 w-5'])
                </span>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900">Tulis Tanggapan Anda</h1>
                    <p class="text-xs text-slate-500">Sampaikan opini atau apresiasi terhadap postingan ini secara santun.</p>
                </div>
            </div>
            <form id="community-comment-form" class="space-y-3">
                <input type="hidden" name="parent_id" id="community-comment-parent">
                <div id="community-reply-indicator" class="hidden items-center justify-between rounded-xl bg-blue-50 px-3 py-2 text-xs font-bold text-[#4154e8]">
                    <span id="community-reply-label"></span>
                    <button type="button" onclick="cancelCommunityReply()" class="text-[#4154e8]" aria-label="Batal membalas">&times;</button>
                </div>
                <textarea id="community-comment-body" name="body" rows="4" maxlength="1000" required
                          placeholder="Tuliskan tanggapan atau komentar Anda..."
                          class="w-full resize-y rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15"></textarea>
                <div class="flex items-center justify-between gap-3">
                    <p id="community-comment-status" class="text-xs text-slate-500" aria-live="polite"></p>
                    <button type="submit" id="community-comment-submit"
                            class="inline-flex items-center gap-2 rounded-full bg-[#4154e8] px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#3243d4] disabled:opacity-50">
                        @include('partials.icons', ['name' => 'send', 'class' => 'h-4 w-4'])
                        Kirim Komentar
                    </button>
                </div>
            </form>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-900">
                <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-[#4154e8] px-2 text-xs text-white">{{ $post->comments_count }}</span>
                Komentar
            </h2>
            <nav class="inline-flex rounded-full border border-slate-200 bg-slate-100 p-1 text-xs font-bold" aria-label="Urutkan komentar">
                @foreach (['top' => 'Teratas', 'newest' => 'Terbaru', 'oldest' => 'Terlama'] as $sortKey => $sortLabel)
                    <a href="{{ route('community.posts.show', ['id' => $post->id, 'sort' => $sortKey]) }}#komentar"
                       @class(['rounded-full px-3.5 py-2 transition', 'bg-white text-[#4154e8] shadow-sm' => $sort === $sortKey, 'text-slate-600 hover:text-slate-900' => $sort !== $sortKey])
                       @if ($sort === $sortKey) aria-current="page" @endif>{{ $sortLabel }}</a>
                @endforeach
            </nav>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            @forelse ($comments as $comment)
                <article class="flex gap-3 border-b border-slate-100 p-5 last:border-0 sm:p-6">
                    @if ($comment['user_avatar'])
                        <img src="{{ $comment['user_avatar'] }}" alt="" onerror="this.classList.add('hidden');this.nextElementSibling.classList.remove('hidden');this.nextElementSibling.classList.add('flex')" class="h-10 w-10 shrink-0 rounded-full object-cover">
                    @endif
                    <span class="{{ $comment['user_avatar'] ? 'hidden ' : '' }}flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-xs font-black text-white">
                        {{ $comment['user_initials'] }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-extrabold text-slate-900">{{ $comment['user_name'] }}</span>
                            @if ($comment['user_id'] === $post->user_id)
                                <span class="rounded-full bg-[#4154e8] px-2 py-0.5 text-[10px] font-bold text-white">Penulis</span>
                            @endif
                            <span class="text-xs text-slate-400">· {{ $comment['created_at'] }}</span>
                        </div>
                        <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">{{ $comment['body'] }}</p>
                        <button type="button" onclick="replyToCommunityComment({{ $comment['id'] }}, @js($comment['user_username']))"
                                class="mt-3 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:border-[#4154e8] hover:text-[#4154e8]">
                            Balas
                        </button>

                        @foreach ($comment['replies'] as $reply)
                            <div class="mt-4 flex gap-3 border-l-2 border-slate-200 pl-4">
                                @if ($reply['user_avatar'])
                                    <img src="{{ $reply['user_avatar'] }}" alt="" onerror="this.classList.add('hidden');this.nextElementSibling.classList.remove('hidden');this.nextElementSibling.classList.add('flex')" class="h-8 w-8 shrink-0 rounded-full object-cover">
                                @endif
                                <span class="{{ $reply['user_avatar'] ? 'hidden ' : '' }}flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-[10px] font-black text-white">
                                    {{ $reply['user_initials'] }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-extrabold text-slate-900">{{ $reply['user_name'] }}</span>
                                        <span class="text-[11px] text-slate-400">· {{ $reply['created_at'] }}</span>
                                    </div>
                                    <p class="mt-1 whitespace-pre-wrap break-words text-sm text-slate-700">{{ $reply['body'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-extrabold text-slate-700">Belum ada komentar</p>
                    <p class="mt-1 text-xs text-slate-500">Jadilah warga pertama yang memulai diskusi.</p>
                </div>
            @endforelse
        </div>
    </section>
</main>

<script>
const communityReactionUrl = @json(route('posts.like', $post->id));
const communityCommentUrl = @json(route('posts.comments.store', $post->id));
const communityCsrfToken = @json(csrf_token());

async function toggleCommunityReaction(reaction) {
    const buttons = [...document.querySelectorAll('.community-reaction')];
    const previousReaction = buttons.find((button) => button.getAttribute('aria-pressed') === 'true')?.dataset.reaction || null;
    const previousLikes = Number(document.getElementById('likes_total').textContent);
    const previousDislikes = Number(document.getElementById('dislikes_total').textContent);
    const nextReaction = previousReaction === reaction ? null : reaction;
    const nextLikes = previousLikes + (nextReaction === 'like' ? 1 : 0) - (previousReaction === 'like' ? 1 : 0);
    const nextDislikes = previousDislikes + (nextReaction === 'dislike' ? 1 : 0) - (previousReaction === 'dislike' ? 1 : 0);
    window.animateReactionParticles(buttons.find((button) => button.dataset.reaction === reaction), reaction);
    updateCommunityReaction(nextReaction, nextLikes, nextDislikes);

    try {
        const response = await fetch(communityReactionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': communityCsrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reaction }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Reaksi gagal disimpan.');
        }

        updateCommunityReaction(data.reaction, data.likes_count, data.dislikes_count);
    } catch (error) {
        updateCommunityReaction(previousReaction, previousLikes, previousDislikes);
        throw error;
    }
}

function updateCommunityReaction(reaction, likes, dislikes) {
    document.getElementById('likes_total').textContent = likes;
    document.getElementById('dislikes_total').textContent = dislikes;
    document.querySelectorAll('.community-reaction').forEach((button) => {
        const active = button.dataset.reaction === reaction;
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.classList.toggle('bg-blue-50', active);
        button.classList.toggle('text-[#4154e8]', active);
        button.classList.toggle('text-slate-600', !active);
    });
}

function replyToCommunityComment(commentId, username) {
    document.getElementById('community-comment-parent').value = commentId;
    document.getElementById('community-reply-label').textContent = `Membalas @${username}`;
    document.getElementById('community-reply-indicator').classList.replace('hidden', 'flex');
    const input = document.getElementById('community-comment-body');
    input.value = `@${username} `;
    input.focus();
}

function cancelCommunityReply() {
    document.getElementById('community-comment-parent').value = '';
    document.getElementById('community-comment-body').value = '';
    document.getElementById('community-reply-indicator').classList.replace('flex', 'hidden');
}

document.getElementById('community-comment-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = document.getElementById('community-comment-submit');
    const status = document.getElementById('community-comment-status');
    button.disabled = true;
    status.textContent = 'Mengirim komentar…';

    try {
        const response = await fetch(communityCommentUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': communityCsrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                body: document.getElementById('community-comment-body').value.trim(),
                parent_id: document.getElementById('community-comment-parent').value || null,
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || data.error || 'Komentar gagal dikirim.');
        }

        window.location.hash = 'komentar';
        window.location.reload();
    } catch (error) {
        status.textContent = error.message || 'Komentar gagal dikirim. Coba lagi.';
    } finally {
        button.disabled = false;
    }
});

document.querySelectorAll('.community-reaction').forEach((button) => {
    button.addEventListener('click', () => {
        toggleCommunityReaction(button.dataset.reaction).catch((error) => window.alert(error.message));
    });
});
</script>
@endsection
