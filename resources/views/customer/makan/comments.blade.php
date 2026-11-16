@extends('layouts.app')

@section('title', 'Komentar · Bareng Makan')

@section('content')
@php
    $hostParts = preg_split('/\s+/', trim($sesi->host->name));
    $hostInitials = strtoupper(mb_substr($hostParts[0] ?? 'W', 0, 1) . (isset($hostParts[1]) ? mb_substr($hostParts[1], 0, 1) : ''));
@endphp

<main class="mx-auto max-w-4xl space-y-6 pb-24">
    <nav aria-label="Breadcrumb" class="flex items-center">
        <div class="inline-flex items-center gap-2 rounded-2xl border border-white/80 bg-white/80 px-3 py-2.5 shadow-sm backdrop-blur sm:px-4">
            <a href="{{ route('makan') }}" class="group inline-flex items-center gap-2 rounded-xl px-2 py-1 text-xs font-bold text-slate-500 transition hover:bg-indigo-50 hover:text-[#4154e8]">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition group-hover:bg-indigo-100 group-hover:text-[#4154e8]">
                    @include('partials.icons', ['name' => 'food', 'class' => 'h-4 w-4'])
                </span>
                Bareng Makan
            </a>
            <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
            </svg>
            <span aria-current="page" class="inline-flex items-center gap-2 px-2 text-xs font-extrabold text-[#4154e8]">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50">
                    @include('partials.icons', ['name' => 'comment', 'class' => 'h-4 w-4'])
                </span>
                Komentar
            </span>
        </div>
    </nav>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3.5 text-xs font-bold text-emerald-800 shadow-sm">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-hidden="true">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:p-7">
            @if ($sesi->image_url)
                <img src="{{ $sesi->image_url }}" alt="Foto {{ $sesi->merchant_name }}" class="h-28 w-full rounded-2xl bg-amber-50 object-cover sm:h-24 sm:w-32">
            @else
                <span class="flex h-24 w-full shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 sm:w-24">
                    @include('partials.icons', ['name' => 'food', 'class' => 'h-8 w-8'])
                </span>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-extrabold uppercase tracking-[0.15em] text-amber-700">{{ $sesi->category }}</p>
                <h1 class="mt-1 truncate text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $sesi->merchant_name }}</h1>
                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $sesi->description ?: 'Diskusi dan koordinasi pesanan bersama warga.' }}</p>
                <p class="mt-2 text-[11px] font-semibold text-slate-500">
                    Titik kumpul: <span class="text-slate-700">{{ $sesi->pickup_point }}</span>
                    <span class="px-1 text-slate-300">·</span>
                    Host: <span class="text-slate-700">{{ $sesi->host->name }}</span>
                </p>
            </div>
            <a href="{{ route('makan') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-extrabold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800">
                <span aria-hidden="true">&larr;</span> Kembali ke sesi
            </a>
        </div>
    </section>

    <section id="komentar" class="scroll-mt-24 space-y-5">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-[#4154e8]">
                    @include('partials.icons', ['name' => 'comment', 'class' => 'h-5 w-5'])
                </span>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Mulai percakapan</h2>
                    <p class="text-xs text-slate-500">Tanya menu, ongkir, atau koordinasikan titik kumpul dengan warga.</p>
                </div>
            </div>
            @if ($errors->has('comment'))
                <p role="alert" class="mb-3 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs font-semibold text-rose-700">{{ $errors->first('comment') }}</p>
            @endif
            <form method="POST" action="{{ route('makan.sessions.comments.store', $sesi) }}" class="space-y-3">
                @csrf
                <label for="food-session-comment" class="sr-only">Tulis komentar</label>
                <textarea id="food-session-comment" name="comment" rows="4" maxlength="1000" required placeholder="Tulis pesanmu di sini…"
                          class="w-full resize-y rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15">{{ old('comment') }}</textarea>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-[11px] text-slate-400">Diskusi ini membantu warga menyelaraskan pesanan bersama.</p>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#4154e8] px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-[#3243d4] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#4154e8]">
                        @include('partials.icons', ['name' => 'send', 'class' => 'h-4 w-4'])
                        Kirim komentar
                    </button>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-extrabold text-slate-900">{{ $comments->total() }} komentar</h2>
            <span class="text-xs font-medium text-slate-500">Terbaru</span>
        </div>
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            @forelse ($comments as $comment)
                @php
                    $commentParts = preg_split('/\s+/', trim($comment->user->name));
                    $commentInitials = strtoupper(mb_substr($commentParts[0] ?? 'W', 0, 1) . (isset($commentParts[1]) ? mb_substr($commentParts[1], 0, 1) : ''));
                @endphp
                <article class="flex gap-3 border-b border-slate-100 p-5 last:border-0 sm:p-6">
                    @if ($comment->user->avatar)
                        <img src="{{ $comment->user->avatar }}" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-extrabold text-[#4154e8]">{{ $commentInitials }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-extrabold text-slate-900">{{ $comment->user->username ? '@' . $comment->user->username : $comment->user->name }}</span>
                            @if ($comment->user_id === $sesi->host_id)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Host</span>
                            @endif
                            <time class="text-xs text-slate-400">· {{ $comment->created_at->diffForHumans() }}</time>
                        </div>
                        <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-6 text-slate-700">{{ $comment->comment }}</p>
                    </div>
                </article>
            @empty
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-extrabold text-slate-700">Belum ada komentar</p>
                    <p class="mt-1 text-xs text-slate-500">Jadilah warga pertama yang memulai diskusi.</p>
                </div>
            @endforelse
        </div>
        @if ($comments->hasPages())
            <div>{{ $comments->links() }}</div>
        @endif
    </section>
</main>
@endsection
