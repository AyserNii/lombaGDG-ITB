@extends('layouts.app')

@section('title', 'Komunitas')

@section('content')
@php
    $rawName = trim($user['name'] ?? 'User');
    $nameParts = preg_split('/\s+/', $rawName);
    $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
@endphp
<div class="grid gap-5 xl:grid-cols-[220px_minmax(0,1fr)_320px]">
    <aside class="hidden space-y-4 xl:block">
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
            <div class="flex items-center gap-3">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-lg font-black shadow-sm ring-2 ring-[#4154e8]/20 select-none">
                    {{ $userInitials }}
                </div>
                <div>
                    <p class="font-extrabold leading-tight">{{ $user['name'] ?? 'Grace Stanley' }}</p>
                    <p class="text-xs text-muted">{{ $user['role'] ?? 'Customer · Depok' }}</p>
                </div>
            </div>
            <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                <div>
                    <p class="text-lg font-extrabold">128</p>
                    <p class="text-[10px] text-muted">Koneksi</p>
                </div>
                <div>
                    <p class="text-lg font-extrabold">24</p>
                    <p class="text-[10px] text-muted">Sesi</p>
                </div>
                <div>
                    <p class="text-lg font-extrabold">12</p>
                    <p class="text-[10px] text-muted">Post</p>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] bg-white p-5 text-sm shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
            <p class="mb-3 text-xs font-extrabold tracking-wide text-muted uppercase">Komunitasmu</p>
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

    <div class="space-y-4">
        <section class="rounded-[28px] bg-white p-4 shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
            <div class="flex gap-4 overflow-x-auto pb-1">
                @foreach ($stories as $story)
                    <div class="flex w-16 shrink-0 flex-col items-center gap-2">
                        <div class="{{ isset($story['add']) && $story['add'] ? 'ring-2 ring-[#4154e8]/40 bg-slate-100' : 'ring-story' }} rounded-full p-[3px]">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white {{ $story['bg'] ?? 'bg-[#4154e8]' }} text-white font-extrabold text-sm select-none shadow-sm">
                                {{ $story['initials'] ?? 'US' }}
                            </div>
                        </div>
                        <p class="truncate text-[11px] font-semibold">{{ $story['name'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-[28px] bg-white p-4 shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-xs font-black shadow-sm select-none">
                    {{ $userInitials }}
                </div>
                <input disabled class="w-full rounded-full bg-[#f4f6ff] px-4 py-3 text-sm text-muted" value="Bagikan sesi, hemat, atau momen bareng...">
            </div>
            <div class="mt-3 flex gap-2 text-xs font-bold text-[#4c54d2]">
                <span class="flex items-center gap-1.5 rounded-full bg-[#eef2fb] px-3 py-1.5">
                    @include('partials.icons', ['name' => 'camera', 'class' => 'h-3.5 w-3.5'])
                    Foto
                </span>
                <span class="flex items-center gap-1.5 rounded-full bg-[#eef2fb] px-3 py-1.5">
                    @include('partials.icons', ['name' => 'pin', 'class' => 'h-3.5 w-3.5'])
                    Sesi
                </span>
                <span class="flex items-center gap-1.5 rounded-full bg-[#eef2fb] px-3 py-1.5">
                    @include('partials.icons', ['name' => 'event', 'class' => 'h-3.5 w-3.5'])
                    Event
                </span>
            </div>
        </section>

        @foreach ($posts as $post)
            <article class="feed-card">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $post['avatar_bg'] ?? 'bg-[#4154e8]' }} text-white text-xs font-black shadow-sm select-none">
                            {{ $post['initials'] ?? 'US' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-extrabold">{{ $post['user'] }}</p>
                                <span class="hidden rounded-full border px-2 py-0.5 text-[10px] font-bold sm:inline-flex items-center gap-1 {{ $post['badge_color'] }}">
                                    @include('partials.icons', ['name' => $post['type_icon'], 'class' => 'h-3 w-3'])
                                    {{ $post['type'] }}
                                </span>
                            </div>
                            <p class="text-[11px] text-muted">{{ $post['role'] }} · {{ $post['time'] }}</p>
                        </div>
                    </div>
                    <button class="rounded-xl p-2 text-muted hover:bg-slate-100">
                        @include('partials.icons', ['name' => 'dots-vertical', 'class' => 'h-4 w-4'])
                    </button>
                </div>
                <p class="px-4 pb-3 text-sm leading-6">{{ $post['text'] }}</p>
                <img src="{{ $post['photo'] }}" alt="" class="aspect-[4/3] w-full object-cover">
                <div class="flex items-center justify-between px-4 py-3 text-sm font-semibold">
                    <div class="flex gap-4">
                        <button data-like class="flex items-center gap-1.5 text-[#4c54d2]">
                            @include('partials.icons', ['name' => 'heart'])
                            <span>{{ $post['likes'] }}</span>
                        </button>
                        <span class="flex items-center gap-1.5 text-muted">
                            @include('partials.icons', ['name' => 'comment'])
                            {{ $post['comments'] }}
                        </span>
                        <span class="flex items-center gap-1.5 text-muted">
                            @include('partials.icons', ['name' => 'send'])
                            Bagikan
                        </span>
                    </div>
                    @include('partials.icons', ['name' => 'bookmark'])
                </div>
                <div class="flex items-center gap-3 border-t border-slate-100 px-4 py-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-[10px] font-black select-none">
                        {{ $userInitials }}
                    </div>
                    <input disabled class="w-full rounded-full bg-[#f4f6ff] px-4 py-2 text-sm text-muted" value="Tulis komentar...">
                </div>
            </article>
        @endforeach
    </div>

    <aside class="space-y-4">
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
            <h2 class="text-sm font-extrabold">Orang yang mungkin kamu kenal</h2>
            <div class="mt-4 space-y-4">
                @foreach ([
                    ['name' => 'Andi Pratama', 'initials' => 'AP', 'bg' => 'bg-amber-600', 'meta' => 'Sering pesan di Beji · 12 koneksi bersama'],
                    ['name' => 'Sari Wulandari', 'initials' => 'SW', 'bg' => 'bg-teal-600', 'meta' => 'Creator BARENG BELANJA'],
                    ['name' => 'Budi Santoso', 'initials' => 'BS', 'bg' => 'bg-rose-600', 'meta' => 'Kirim ke Depok Lama · 8 sesi bersama'],
                ] as $person)
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $person['bg'] }} text-white text-xs font-black shadow-sm select-none">
                            {{ $person['initials'] }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold">{{ $person['name'] }}</p>
                            <p class="text-[11px] leading-4 text-muted">{{ $person['meta'] }}</p>
                            <button class="mt-2 flex items-center gap-1 rounded-full border border-[#4c54d2] px-3 py-1 text-xs font-bold text-[#4c54d2] hover:bg-[#4c54d2]/5 transition">
                                @include('partials.icons', ['name' => 'users', 'class' => 'h-3.5 w-3.5'])
                                Connect
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(76,84,210,0.08)]">
            <h2 class="text-sm font-extrabold">Sesi yang lagi ramai</h2>
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
                            <p class="text-sm font-bold">{{ $sess['title'] }}</p>
                            <p class="text-[11px] text-muted">{{ $sess['sub'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </aside>
</div>
@endsection
