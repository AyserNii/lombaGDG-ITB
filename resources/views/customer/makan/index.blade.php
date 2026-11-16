@extends('layouts.app')

@section('title', 'Bareng Makan · BARENG')

@section('content')
<div class="mx-auto max-w-7xl space-y-7">
    <section class="relative isolate overflow-hidden rounded-[32px] bg-gradient-to-r from-[#2c1810] via-[#6b341c] to-[#d97706] px-6 py-7 text-white shadow-[0_18px_50px_rgba(74,38,18,0.2)] md:px-8 md:py-8">
        <div class="pointer-events-none absolute -right-28 -top-40 h-96 w-96 rounded-full border border-white/10"></div>
        <div class="pointer-events-none absolute -bottom-52 right-1/4 h-80 w-80 rounded-full border border-white/[0.07]"></div>

        <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(340px,0.72fr)] lg:items-center">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/20 px-3.5 py-1.5 text-[11px] font-extrabold text-white">
                        @include('partials.icons', ['name' => 'food', 'class' => 'h-3.5 w-3.5'])
                        Bareng Makan · {{ $locationLabel }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-400/15 px-3.5 py-1.5 text-[11px] font-extrabold text-emerald-200">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.36-6.36-2.12 2.12M7.76 16.24l-2.12 2.12m12.72 0-2.12-2.12M7.76 7.76 5.64 5.64"/>
                            <circle cx="12" cy="12" r="4"/>
                        </svg>
                        Ongkir dibagi bersama peserta
                    </span>
                </div>
                <h1 class="mt-5 max-w-3xl text-3xl font-extrabold leading-[1.12] tracking-tight md:text-4xl xl:text-[3.1rem]">
                    Makan bareng tetangga, ongkir lebih ringan.
                </h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-amber-50/80 md:text-base">
                    Pilih sesi dari warung sekitar {{ $locationLabel }}, atau buka pesanan baru dan tentukan titik kumpulnya.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="button"
                            data-open-food-create
                            @disabled(!$canHostSession)
                            class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-[#2c1810] shadow-sm transition hover:bg-amber-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#2c1810] disabled:cursor-not-allowed disabled:opacity-50">
                        @include('partials.icons', ['name' => 'plus', 'class' => 'h-4 w-4'])
                        Buka sesi makan
                    </button>
                    <a href="{{ route('sesi') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur-sm transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                        @include('partials.icons', ['name' => 'schedule', 'class' => 'h-4 w-4'])
                        Lihat sesi saya
                    </a>
                </div>
                @unless ($canHostSession)
                    <p class="mt-3 text-xs text-amber-100">
                        Lengkapi lokasi profil agar bisa membuka sesi untuk warga sekitar.
                        <a href="{{ route('settings') }}" class="font-bold underline underline-offset-2">Atur lokasi</a>
                    </p>
                @endunless
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/[0.12] px-4 py-4 text-center backdrop-blur-sm sm:px-5">
                    <p class="text-[11px] font-semibold text-white/70">Sesi aktif di lokasi</p>
                    <p class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $activeSessionCount }} Sesi</p>
                    <p class="mt-1 text-[10px] font-bold text-emerald-300">Siap dipilih</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/[0.12] px-4 py-4 text-center backdrop-blur-sm sm:px-5">
                    <p class="text-[11px] font-semibold text-white/70">Warga bergabung</p>
                    <p class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $activeParticipantCount }} Orang</p>
                    <p class="mt-1 text-[10px] font-bold text-emerald-300">Di sesi yang terbuka</p>
                </div>
                <div class="col-span-2 flex items-center gap-3 rounded-2xl border border-emerald-300/20 bg-emerald-400/15 px-4 py-3.5 backdrop-blur-sm sm:px-5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-400 text-emerald-950">
                        @include('partials.icons', ['name' => 'food', 'class' => 'h-4 w-4'])
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-extrabold text-white">Ongkir dihitung lebih transparan</p>
                        <p class="mt-0.5 text-[10px] leading-4 text-emerald-100">Perkiraan per orang mengikuti jumlah peserta sesi.</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-emerald-300/15 px-2.5 py-1 text-[10px] font-extrabold text-emerald-200">Bareng Makan</span>
                </div>
            </div>
        </div>
    </section>

    <section aria-labelledby="food-sessions-heading">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">Pesanan warga</p>
                <h2 id="food-sessions-heading" class="mt-1 text-xl font-extrabold tracking-tight text-[#1e293b]">
                    Sesi makan yang sedang dibuka
                </h2>
                <p class="mt-1 text-sm text-slate-500">Pilih sesi yang titik kumpulnya paling mudah untukmu.</p>
            </div>

            <form method="GET" action="{{ route('makan') }}" role="search" class="flex w-full gap-2 sm:max-w-sm">
                <label for="food-session-search" class="sr-only">Cari warung atau kategori</label>
                <input id="food-session-search"
                       name="q"
                       type="search"
                       value="{{ $searchTerm }}"
                       maxlength="100"
                       placeholder="Cari warung atau kategori"
                       class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
                <button type="submit" aria-label="Cari warung atau kategori" title="Cari"
                        class="inline-flex items-center justify-center rounded-xl bg-[#1e293b] px-4 py-2.5 text-white transition hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                    @include('partials.icons', ['name' => 'search', 'class' => 'h-4 w-4'])
                </button>
            </form>
        </div>

        @if ($activeSessions->isEmpty())
            <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                    @include('partials.icons', ['name' => 'food', 'class' => 'h-5 w-5'])
                </span>
                <h3 class="mt-4 text-base font-extrabold text-slate-800">
                    {{ $searchTerm !== '' ? 'Belum ada sesi yang cocok' : 'Belum ada sesi makan di lokasimu' }}
                </h3>
                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                    {{ $searchTerm !== '' ? 'Coba kata kunci lain, atau buka sesi baru untuk warung pilihanmu.' : 'Buka sesi pertama dan ajak warga sekitar memesan bersama.' }}
                </p>
                @if ($searchTerm !== '')
                    <a href="{{ route('makan') }}" class="mt-4 inline-flex text-sm font-bold text-amber-800 hover:text-amber-900">Hapus pencarian</a>
                @endif
            </div>
        @else
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                @foreach ($activeSessions as $sesi)
                    @php
                        $participantCount = (int) $sesi->participants_count;
                        $slotsMax = max(1, (int) $sesi->slots_max);
                        $progress = min(100, (int) round($participantCount / $slotsMax * 100));
                        $ongkirPerPerson = (int) ceil($sesi->normal_ongkir / max(1, $participantCount));
                        $hostParts = preg_split('/\s+/', trim($sesi->host->name));
                        $hostInitials = strtoupper(
                            mb_substr($hostParts[0] ?? 'W', 0, 1)
                            . (isset($hostParts[1]) ? mb_substr($hostParts[1], 0, 1) : '')
                        );
                        $isFull = $participantCount >= $slotsMax;
                    @endphp

                    <article class="overflow-hidden rounded-[26px] border border-slate-200/80 bg-white shadow-[0_8px_32px_rgba(30,40,110,0.06)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_38px_rgba(30,40,110,0.1)]">
                        <div class="flex gap-4 p-5">
                            @if ($sesi->image_url)
                                <img src="{{ $sesi->image_url }}" alt="{{ $sesi->merchant_name }}" class="h-[88px] w-[88px] shrink-0 rounded-2xl bg-amber-50 object-cover">
                            @else
                                <div class="flex h-[88px] w-[88px] shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 via-orange-50 to-rose-100 text-amber-800">
                                    @include('partials.icons', ['name' => 'food', 'class' => 'h-7 w-7'])
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="rounded-md bg-amber-50 px-2 py-1 text-[10px] font-extrabold uppercase tracking-wide text-amber-800">
                                        {{ $sesi->category }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600">
                                        @include('partials.icons', ['name' => 'clock', 'class' => 'h-3 w-3'])
                                        Tutup {{ $sesi->order_deadline?->format('d/m H:i') ?? 'sesuai host' }}
                                    </span>
                                </div>
                                <h3 class="mt-2 truncate text-base font-extrabold text-[#1e293b]">{{ $sesi->merchant_name }}</h3>
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                    {{ $sesi->description ?: 'Sesi pesanan bersama warga sekitar.' }}
                                </p>
                            </div>
                        </div>

                        <div class="mx-5 rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Kapasitas</p>
                                    <p class="mt-1 text-sm font-extrabold text-slate-800">{{ $participantCount }} dari {{ $slotsMax }} warga</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Ongkir per orang*</p>
                                    <p class="mt-1 text-sm font-extrabold text-amber-800">Rp {{ number_format($ongkirPerPerson, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-amber-500 transition-[width] duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="mt-2 text-[10px] text-slate-400">*Perkiraan dari ongkir penuh dibagi peserta saat ini.</p>
                        </div>

                        <div class="px-5 pt-4">
                            <div class="flex items-start gap-3">
                                @if ($sesi->host->avatar)
                                    <img src="{{ $sesi->host->avatar }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover">
                                @else
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-[10px] font-extrabold text-white">{{ $hostInitials }}</span>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-slate-800">{{ $sesi->host->name }} <span class="font-medium text-slate-400">membuka sesi</span></p>
                                    <p class="mt-1 flex items-center gap-1 truncate text-[11px] text-slate-500">
                                        @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-3 w-3 shrink-0'])
                                        Titik kumpul: {{ $sesi->pickup_point }}
                                    </p>
                                </div>
                            </div>

                            @if ($sesi->popular_menus)
                                <div class="mt-4 flex flex-wrap gap-1.5">
                                    @foreach ($sesi->popular_menus as $menu)
                                        <span class="rounded-lg border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600">{{ $menu }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-1">
                                <form method="POST" action="{{ route('makan.sessions.like', $sesi) }}" data-food-like-form>
                                    @csrf
                                    <button type="submit"
                                            aria-label="{{ $sesi->is_liked_by_user ? 'Hapus suka' : 'Sukai sesi' }}"
                                            aria-pressed="{{ $sesi->is_liked_by_user ? 'true' : 'false' }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-bold transition {{ $sesi->is_liked_by_user ? 'bg-rose-50 text-rose-600' : 'text-slate-500 hover:bg-rose-50 hover:text-rose-600' }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $sesi->is_liked_by_user ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/>
                                        </svg>
                                        <span data-food-like-count>{{ $sesi->likes_count }}</span>
                                    </button>
                                </form>
                                <a href="{{ route('makan.sessions.comments', $sesi) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                                        aria-label="Buka diskusi {{ $sesi->merchant_name }}">
                                    @include('partials.icons', ['name' => 'comment', 'class' => 'h-4 w-4'])
                                    {{ $sesi->comments_count }}
                                </a>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" data-open-food-session data-session-id="{{ $sesi->id }}"
                                   class="inline-flex items-center rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-extrabold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                                    Lihat detail
                                </button>
                                @if ($sesi->has_joined)
                                    <span class="rounded-xl bg-emerald-50 px-3.5 py-2.5 text-xs font-extrabold text-emerald-700">Kamu sudah bergabung</span>
                                @elseif ($isFull)
                                    <span class="rounded-xl bg-slate-100 px-3.5 py-2.5 text-xs font-extrabold text-slate-500">Slot penuh</span>
                                @else
                                    <button type="button"
                                            data-open-food-join
                                            data-session-id="{{ $sesi->id }}"
                                            data-session-title="{{ $sesi->merchant_name }}"
                                            data-join-url="{{ route('makan.sessions.join', $sesi) }}"
                                            class="inline-flex items-center gap-2 rounded-xl bg-[#d97706] px-4 py-2.5 text-xs font-extrabold text-white shadow-sm shadow-amber-700/15 transition hover:bg-[#b45309] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
                                        @include('partials.icons', ['name' => 'food', 'class' => 'h-4 w-4'])
                                        Gabung sesi
                                    </button>
                                @endif
                            </div>
                        </div>

                        <template data-food-session-template="{{ $sesi->id }}">
                            <div class="space-y-5">
                                @if ($sesi->image_url)
                                    <img src="{{ $sesi->image_url }}" alt="Foto {{ $sesi->merchant_name }}" class="max-h-64 w-full rounded-2xl bg-amber-50 object-cover">
                                @endif
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-amber-700">{{ $sesi->category }}</p>
                                    <h3 class="mt-1 text-xl font-extrabold text-slate-900">{{ $sesi->merchant_name }}</h3>
                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $sesi->description ?: 'Sesi pesanan bersama warga sekitar.' }}</p>
                                </div>
                                <div class="grid gap-3 rounded-2xl bg-slate-50 p-4 text-sm sm:grid-cols-2">
                                    <p><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Titik kumpul</span><span class="mt-1 block font-semibold text-slate-800">{{ $sesi->pickup_point }}</span></p>
                                    <p><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Batas pesanan</span><span class="mt-1 block font-semibold text-slate-800">{{ $sesi->order_deadline?->format('d/m/Y H:i') ?? 'Sesuai keputusan host' }}</span></p>
                                    <p><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Peserta</span><span class="mt-1 block font-semibold text-slate-800">{{ $participantCount }} dari {{ $slotsMax }} warga</span></p>
                                    <p><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Ongkir per orang saat ini</span><span class="mt-1 block font-semibold text-slate-800">Rp {{ number_format($ongkirPerPerson, 0, ',', '.') }}</span></p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($sesi->popular_menus ?? [] as $menu)
                                        <span class="rounded-lg border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600">{{ $menu }}</span>
                                    @endforeach
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                    <p class="text-xs text-slate-500">Dibuka oleh <span class="font-bold text-slate-800">{{ $sesi->host->name }}</span></p>
                                    <a href="{{ route('makan.sessions.comments', $sesi) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-50 px-3.5 py-2.5 text-xs font-extrabold text-amber-800 transition hover:bg-amber-100">
                                        @include('partials.icons', ['name' => 'comment', 'class' => 'h-4 w-4'])
                                        Lihat komentar ({{ $sesi->comments_count }})
                                    </a>
                                </div>
                            </div>
                        </template>

                    </article>
                @endforeach
            </div>

            @if ($activeSessions->hasPages())
                <div class="mt-6">{{ $activeSessions->links() }}</div>
            @endif
        @endif
    </section>
</div>

<dialog id="food-session-create-dialog" aria-labelledby="food-session-create-title" class="m-auto max-h-[92vh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-3xl bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/55 backdrop:backdrop-blur-sm">
    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-amber-700">Mulai pesanan bersama</p>
            <h2 id="food-session-create-title" class="mt-1 text-xl font-extrabold">Buka sesi makan</h2>
            <p class="mt-1 text-xs text-slate-500">Warga di {{ $locationLabel }} bisa melihat dan bergabung.</p>
        </div>
        <button type="button" data-close-food-dialog aria-label="Tutup modal" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
    </div>
    <form method="POST" action="{{ route('makan.sessions.store') }}" data-food-session-create-form data-photo-upload-url="{{ route('makan.sessions.images.store') }}" class="space-y-4 px-6 py-5">
        @csrf
        <input type="hidden" name="_form" value="create">
        <input type="hidden" name="image_path" value="{{ old('image_path') }}" data-food-photo-path>
        @if ($errors->any() && old('_form') === 'create')
            <p role="alert" class="rounded-xl bg-rose-50 px-3.5 py-2.5 text-xs font-semibold text-rose-700">{{ $errors->first() }}</p>
        @endif
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-xs font-bold text-slate-700">Nama warung
                <input name="merchant_name" type="text" maxlength="120" required value="{{ old('merchant_name') }}" placeholder="Contoh: Soto Ayam Bu Rini" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
            </label>
            <label class="block text-xs font-bold text-slate-700">Kategori
                <input name="category" type="text" maxlength="80" required value="{{ old('category') }}" placeholder="Contoh: Makanan rumahan" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
            </label>
        </div>
        <label class="block text-xs font-bold text-slate-700">Cerita singkat <span class="font-medium text-slate-400">· opsional</span>
            <textarea name="description" rows="2" maxlength="1000" placeholder="Info menu atau cara pesanan akan dikumpulkan" class="mt-1.5 w-full resize-y rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">{{ old('description') }}</textarea>
        </label>
        <label class="block text-xs font-bold text-slate-700">Titik kumpul
            <input name="pickup_point" type="text" maxlength="160" required value="{{ old('pickup_point') }}" placeholder="Contoh: Pos satpam Blok C" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
        </label>
        <div class="grid gap-4 sm:grid-cols-3">
            <label class="block text-xs font-bold text-slate-700">Batas pesanan
                <input name="order_deadline" type="datetime-local" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" required value="{{ old('order_deadline') }}" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3 py-3 text-xs font-medium outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
            </label>
            <label class="block text-xs font-bold text-slate-700">Maksimal warga
                <input name="slots_max" type="number" min="2" max="20" required value="{{ old('slots_max', 5) }}" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
            </label>
            <label class="block text-xs font-bold text-slate-700">Ongkir penuh (Rp)
                <input name="normal_ongkir" type="number" min="0" max="1000000" required value="{{ old('normal_ongkir', 15000) }}" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
            </label>
        </div>
        <label class="block text-xs font-bold text-slate-700">Menu yang tersedia <span class="font-medium text-slate-400">· pisahkan dengan baris baru atau koma</span>
            <textarea name="popular_menus" rows="2" maxlength="1000" placeholder="Nasi ayam&#10;Es teh" class="mt-1.5 w-full resize-y rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">{{ old('popular_menus') }}</textarea>
        </label>
        <div class="space-y-3" data-food-photo-uploader data-initial-photo-url="{{ old('image_path') ? route('makan.sessions.images.show', ['fileName' => basename(old('image_path'))]) : '' }}">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-bold text-slate-700">Foto warung <span class="font-medium text-slate-400">· opsional</span></p>
                <span class="text-[10px] font-semibold text-slate-400">JPG, PNG, WEBP · maks. 5 MB</span>
            </div>
            <div data-food-photo-dropzone class="relative min-h-[216px] overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 transition">
                <input id="food-session-photo" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" data-food-photo-input>
                <div data-food-photo-placeholder class="flex min-h-[212px] flex-col items-center justify-center gap-2 p-4 text-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        @include('partials.icons', ['name' => 'camera', 'class' => 'h-5 w-5'])
                    </span>
                    <p class="text-xs font-bold text-slate-700">Tarik foto ke sini atau pilih dari perangkat</p>
                    <label for="food-session-photo" class="cursor-pointer rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-bold text-slate-700 transition hover:border-amber-300 hover:text-amber-800">Pilih foto</label>
                    <p class="text-[10px] text-slate-400">Foto akan diperiksa sebelum sesi diterbitkan.</p>
                </div>
                <div data-food-photo-preview-wrap class="hidden relative h-[212px] w-full">
                    <img data-food-photo-preview alt="Pratinjau foto warung" class="h-full w-full bg-slate-100 object-cover">
                    <div data-food-photo-loading class="hidden absolute inset-0 items-center justify-center bg-slate-950/25">
                        <span class="h-9 w-9 animate-spin rounded-full border-[3px] border-white/40 border-t-white" aria-label="Memproses foto"></span>
                    </div>
                    <div class="absolute inset-x-0 bottom-0 bg-slate-950/75 p-3 text-white backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p data-food-photo-name class="truncate text-xs font-bold"></p>
                                <p data-food-photo-status class="mt-1 text-[10px] text-white/75" aria-live="polite"></p>
                            </div>
                            <label for="food-session-photo" class="cursor-pointer rounded-lg bg-white/15 px-2.5 py-1.5 text-[10px] font-bold transition hover:bg-white/25">Ganti</label>
                            <button type="button" data-food-photo-remove class="rounded-lg bg-white/15 px-2.5 py-1.5 text-[10px] font-bold transition hover:bg-rose-500/80">Hapus</button>
                        </div>
                        <div data-food-photo-progress-wrap class="hidden mt-2" aria-live="polite">
                            <div class="mb-1 flex items-center justify-between gap-3 text-[10px] font-semibold text-white/80">
                                <span data-food-photo-progress-label>Mengunggah foto…</span>
                                <span data-food-photo-progress-value>0%</span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-white/25">
                                <div data-food-photo-progress-bar role="progressbar" aria-label="Progres unggah foto" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" class="h-full w-0 rounded-full bg-amber-300 transition-[width] duration-150"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p data-food-photo-error role="alert" class="hidden rounded-xl bg-rose-50 px-3 py-2 text-[11px] font-semibold leading-5 text-rose-700"></p>
            @error('image_path')
                <p role="alert" class="rounded-xl bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-700">{{ $message }}</p>
            @enderror
            <label class="block text-xs font-bold text-slate-700">Atau tempel tautan foto
                <input name="image_url" type="url" maxlength="2048" value="{{ old('image_url') }}" placeholder="https://..." data-food-photo-url
                       class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
            </label>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
            <button type="button" data-close-food-dialog class="rounded-xl px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Batal</button>
            <button type="submit" class="rounded-xl bg-amber-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">Terbitkan sesi</button>
        </div>
    </form>
</dialog>

<dialog id="food-session-join-dialog" aria-labelledby="food-session-join-title" class="m-auto max-h-[92vh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-3xl bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/55 backdrop:backdrop-blur-sm">
    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-amber-700">Titip pesanan</p>
            <h2 id="food-session-join-title" data-food-join-title class="mt-1 text-xl font-extrabold">Gabung sesi makan</h2>
            <p class="mt-1 text-xs text-slate-500">Rincian ini akan dikirim ke host sesi.</p>
        </div>
        <button type="button" data-close-food-dialog aria-label="Tutup modal" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
    </div>
    <form method="POST" action="#" data-food-join-form class="space-y-4 px-6 py-5">
        @csrf
        <input type="hidden" name="_form" value="join">
        <input type="hidden" name="food_session_id" data-food-join-id value="{{ old('food_session_id') }}">
        @if ($errors->any() && old('_form') === 'join')
            <p role="alert" class="rounded-xl bg-rose-50 px-3.5 py-2.5 text-xs font-semibold text-rose-700">{{ $errors->first() }}</p>
        @endif
        <label class="block text-xs font-bold text-slate-700">Pesananmu
            <textarea name="order_items" rows="3" maxlength="500" required placeholder="Contoh: 1 nasi ayam, sambal dipisah" class="mt-1.5 w-full resize-y rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">{{ old('_form') === 'join' ? old('order_items') : '' }}</textarea>
        </label>
        <label class="block text-xs font-bold text-slate-700">Perkiraan harga makanan (Rp)
            <input name="estimated_cost" type="number" min="0" max="10000000" required value="{{ old('_form') === 'join' ? old('estimated_cost') : '' }}" placeholder="25000" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
        </label>
        <label class="block text-xs font-bold text-slate-700">Catatan untuk host <span class="font-medium text-slate-400">· opsional</span>
            <textarea name="notes" rows="2" maxlength="500" placeholder="Contoh: tanpa acar" class="mt-1.5 w-full resize-y rounded-xl border border-slate-200 px-3.5 py-3 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">{{ old('_form') === 'join' ? old('notes') : '' }}</textarea>
        </label>
        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
            <button type="button" data-close-food-dialog class="rounded-xl px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Batal</button>
            <button type="submit" class="rounded-xl bg-amber-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">Kirim pesanan</button>
        </div>
    </form>
</dialog>

<dialog id="food-session-detail-dialog" aria-labelledby="food-session-detail-title" class="m-auto max-h-[92vh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-3xl bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/55 backdrop:backdrop-blur-sm">
    <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-amber-700">Bareng Makan</p>
            <h2 id="food-session-detail-title" class="mt-1 text-lg font-extrabold text-slate-900">Detail sesi</h2>
        </div>
        <button type="button" data-close-food-dialog aria-label="Tutup detail sesi" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
    </div>
    <div data-food-session-detail-content class="px-6 py-5"></div>
</dialog>

@if ($errors->any())
    <div data-food-form-error
         data-form="{{ old('_form') }}"
         data-session-id="{{ old('food_session_id') }}"
         data-message="{{ $errors->first() }}"
         class="hidden"></div>
@endif
@endsection
