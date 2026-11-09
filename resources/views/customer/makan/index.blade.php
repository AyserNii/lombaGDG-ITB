@extends('layouts.app')

@section('title', 'Bareng Makan · Pesan Makanan Bagi Ongkir Tetangga')

@section('content')
<div class="space-y-6">

    <!-- Hero Banner with Stats -->
    <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-r from-[#2c1810] via-[#4d2616] to-[#d97706] p-6 lg:p-8 text-white shadow-xl shadow-amber-500/10">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute right-1/4 -bottom-10 h-48 w-48 rounded-full bg-amber-400/10 blur-xl"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 items-center gap-6">
            <div class="lg:col-span-8 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 backdrop-blur-md px-3.5 py-1 text-xs font-bold text-white shadow-sm">
                        @include('partials.icons', ['name' => 'food', 'class' => 'h-3.5 w-3.5'])
                        Bareng Makan Bersama Tetangga
                    </span>
                    <span class="rounded-full bg-amber-400/20 px-3 py-1 text-xs font-bold text-amber-200 border border-amber-300/30">
                        🍜 Ongkir Dibagi Rata · Hemat s/d 75%
                    </span>
                </div>
                <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight leading-tight">
                    Pesan Makanan Favorit, Ongkir Dibagi Rata dengan Tetangga Sekitar!
                </h1>
                <p class="text-xs md:text-sm text-white/80 max-w-2xl leading-relaxed">
                    Gabung ke sesi pesanan makanan yang sedang dibuka tetangga di <strong class="text-white">{{ $user['location'] ?? 'Beji, Depok' }}</strong>. Satu kurir jalan untuk beberapa pesanan sekaligus ke titik temu clustermu.
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <button type="button" onclick="alert('Form Buka Sesi Makan Baru dibuka!')" class="flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-xs font-extrabold text-[#2c1810] shadow-lg shadow-black/10 hover:bg-slate-100 transition active:scale-[0.99]">
                        @include('partials.icons', ['name' => 'plus', 'class' => 'h-4 w-4 text-[#d97706]'])
                        Buka Sesi Makan Baru
                    </button>
                    <a href="{{ route('sesi') }}" class="flex items-center gap-2 rounded-2xl bg-white/15 backdrop-blur border border-white/25 px-5 py-3 text-xs font-bold text-white hover:bg-white/25 transition">
                        @include('partials.icons', ['name' => 'schedule', 'class' => 'h-4 w-4'])
                        Sesi Makan Saya
                    </a>
                </div>
            </div>

            <div class="lg:col-span-4 grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                    <p class="text-[11px] text-white/70 font-semibold">Sesi Makan Aktif</p>
                    <p class="text-2xl font-black text-white mt-1">{{ count($activeSessions) }} Warung</p>
                    <p class="text-[10px] text-amber-300 mt-1 font-bold">Siap dipesan bareng</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                    <p class="text-[11px] text-white/70 font-semibold">Ongkir Rata-Rata</p>
                    <p class="text-2xl font-black text-amber-300 mt-1">Rp 3.5k</p>
                    <p class="text-[10px] text-white/80 mt-1 font-semibold">Hemat dari Rp 15k</p>
                </div>
                <div class="col-span-2 rounded-2xl bg-amber-500/20 backdrop-blur-md border border-amber-400/30 p-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-amber-500 text-white">
                            @include('partials.icons', ['name' => 'leaf', 'class' => 'h-4 w-4'])
                        </span>
                        <div class="text-left">
                            <p class="text-xs font-bold text-white leading-tight">Kurangi Sampah & Kemasan</p>
                            <p class="text-[10px] text-white/70">Kemasan dipadukan dalam 1 kantong ramah lingkungan</p>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-amber-300">-70% Plastik</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Food Sessions Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg md:text-xl font-extrabold text-[#1e293b]">Sesi Makan Siang & Malam Sedang Buka</h2>
                <p class="text-xs text-slate-500">Pilih menu favoritmu dan gabung sebelum sesi ditutup otomatis</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                {{ count($activeSessions) }} Sesi Tersedia
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($activeSessions as $sesi)
                <div class="rounded-[28px] bg-white p-5 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-start gap-4">
                            <img src="{{ $sesi['image'] }}" alt="{{ $sesi['merchant'] }}" class="h-20 w-20 rounded-2xl object-cover shrink-0 ring-1 ring-slate-100">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-block rounded-lg bg-amber-50 px-2 py-0.5 text-[10px] font-extrabold text-amber-700">
                                        {{ $sesi['category'] }}
                                    </span>
                                    <span class="flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg">
                                        @include('partials.icons', ['name' => 'clock', 'class' => 'h-3 w-3'])
                                        {{ $sesi['time_left'] }}
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-[#1e293b] mt-1 truncate">{{ $sesi['merchant'] }}</h3>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                    <span class="font-extrabold text-amber-500">★ {{ $sesi['rating'] }}</span>
                                    <span>·</span>
                                    <span>{{ $sesi['distance'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity & Pricing Grid -->
                        <div class="mt-4 grid grid-cols-2 gap-2.5 text-center">
                            <div class="rounded-2xl bg-slate-50 p-2.5 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-semibold">Slot Terisi</p>
                                <p class="text-xs font-extrabold text-[#4154e8] mt-0.5">
                                    {{ $sesi['slots_filled'] }}/{{ $sesi['slots_max'] }} Tetangga Gabung
                                </p>
                            </div>
                            <div class="rounded-2xl bg-amber-50 p-2.5 border border-amber-100">
                                <p class="text-[10px] text-amber-700 font-semibold">Ongkir Bareng</p>
                                <p class="text-xs font-extrabold text-amber-900 mt-0.5">{{ $sesi['ongkir_split'] }}</p>
                                <span class="text-[9px] line-through text-slate-400">{{ $sesi['normal_ongkir'] }}</span>
                            </div>
                        </div>

                        <!-- Popular Menus -->
                        <div class="mt-3.5">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Menu Terpopuler di Sesi Ini:</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($sesi['popular_menus'] as $menu)
                                    <span class="rounded-lg bg-white border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $menu }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Footer Card -->
                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-800 font-extrabold text-xs">
                                {{ $sesi['host_avatar'] }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 leading-tight">{{ $sesi['host'] }}</p>
                                <p class="text-[10px] text-slate-400">Titik drop: {{ $sesi['pickup_point'] }}</p>
                            </div>
                        </div>

                        <button type="button" onclick="alert('Kamu berhasil mengajukan pesanan ke sesi {{ $sesi['merchant'] }}!')" class="flex items-center justify-center gap-1.5 rounded-xl bg-[#d97706] hover:bg-[#b45309] px-4 py-2 text-xs font-extrabold text-white shadow-md shadow-amber-500/20 transition active:scale-[0.99]">
                            @include('partials.icons', ['name' => 'food', 'class' => 'h-4 w-4'])
                            Gabung Pesan Makan
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

