@extends('layouts.app')

@section('title', 'Bareng Belanja · Sembako & Minimarket Gotong Royong')

@section('content')
<div class="space-y-6">

    <!-- Hero Banner with Stats -->
    <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-r from-[#1e2a5e] via-[#2d3a82] to-[#4154e8] p-6 lg:p-8 text-white shadow-xl shadow-[#4154e8]/10">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute right-1/4 -bottom-10 h-48 w-48 rounded-full bg-emerald-400/10 blur-xl"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 items-center gap-6">
            <div class="lg:col-span-8 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 backdrop-blur-md px-3.5 py-1 text-xs font-bold text-white shadow-sm">
                        @include('partials.icons', ['name' => 'bag', 'class' => 'h-3.5 w-3.5'])
                        Bareng Belanja Kebutuhan & Sembako
                    </span>
                    <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                        ⚡ Hemat s/d 40% dari Diskon Grosir
                    </span>
                </div>
                <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight leading-tight">
                    Belanja Bareng Tetangga, Bebas Ongkir & Capai Diskon Grosir!
                </h1>
                <p class="text-xs md:text-sm text-white/80 max-w-2xl leading-relaxed">
                    Gabungkan pesanan belanjaan harianmu dari supermarket, minimarket, dan toko grosir terdekat di sekitar <strong class="text-white">{{ $user['location'] ?? 'Beji, Depok' }}</strong>.
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <button type="button" onclick="alert('Fitur Buat Sesi Belanja Baru dibuka untuk clustermu!')" class="flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-xs font-extrabold text-[#1e2a5e] shadow-lg shadow-black/10 hover:bg-slate-100 transition active:scale-[0.99]">
                        @include('partials.icons', ['name' => 'plus', 'class' => 'h-4 w-4 text-[#4154e8]'])
                        Mulai Sesi Belanja Baru
                    </button>
                    <a href="{{ route('sesi') }}" class="flex items-center gap-2 rounded-2xl bg-white/15 backdrop-blur border border-white/25 px-5 py-3 text-xs font-bold text-white hover:bg-white/25 transition">
                        @include('partials.icons', ['name' => 'schedule', 'class' => 'h-4 w-4'])
                        Lihat Sesi Aktif Saya
                    </a>
                </div>
            </div>

            <div class="lg:col-span-4 grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                    <p class="text-[11px] text-white/70 font-semibold">Sesi Aktif di Cluster</p>
                    <p class="text-2xl font-black text-white mt-1">{{ count($activeSessions) }} Sesi</p>
                    <p class="text-[10px] text-emerald-300 mt-1 font-bold">Siap digabung sekarang</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                    <p class="text-[11px] text-white/70 font-semibold">Rata-rata Hemat</p>
                    <p class="text-2xl font-black text-white mt-1">Rp 28.5k</p>
                    <p class="text-[10px] text-emerald-300 mt-1 font-bold">Per transaksi belanja</p>
                </div>
                <div class="col-span-2 rounded-2xl bg-emerald-500/20 backdrop-blur-md border border-emerald-400/30 p-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-emerald-500 text-white">
                            @include('partials.icons', ['name' => 'leaf', 'class' => 'h-4 w-4'])
                        </span>
                        <div class="text-left">
                            <p class="text-xs font-bold text-white leading-tight">Gotong Royong Hijau</p>
                            <p class="text-[10px] text-white/70">1 kurir mengantar belanja 4 tetangga sekaligus</p>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-emerald-300">-65% Emisi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="flex items-center justify-between gap-4 overflow-x-auto pb-1">
        <div class="flex items-center gap-2">
            <button type="button" class="rounded-full bg-[#4154e8] px-4 py-2 text-xs font-bold text-white shadow-sm">
                Semua Kategori ({{ count($activeSessions) }})
            </button>
            <button type="button" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition shadow-sm border border-slate-100">
                🥬 Sayur & Buah Segar
            </button>
            <button type="button" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition shadow-sm border border-slate-100">
                🍚 Sembako & Grosir Beras
            </button>
            <button type="button" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition shadow-sm border border-slate-100">
                🏪 Minimarket & Harian
            </button>
            <button type="button" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition shadow-sm border border-slate-100">
                🛒 Supermarket Bulanan
            </button>
        </div>

        <div class="hidden sm:flex items-center gap-2 text-xs font-bold text-slate-500">
            @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-4 w-4 text-[#4154e8]'])
            <span>Radius: <strong class="text-slate-800">{{ $user['location'] ?? 'Beji' }} (800m)</strong></span>
        </div>
    </div>

    <!-- Active Group Buying Sessions -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg md:text-xl font-extrabold text-[#1e293b]">Sesi Belanja Sedang Dibuka di Sekitarmu</h2>
                <p class="text-xs text-slate-500">Klik "Gabung Keranjang" untuk menambahkan item belanjaanmu ke sesi tetangga</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Matching
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5">
            @foreach ($activeSessions as $sesi)
                <div class="rounded-[28px] bg-white p-5 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-start gap-4">
                            <img src="{{ $sesi['image'] }}" alt="{{ $sesi['store'] }}" class="h-20 w-20 rounded-2xl object-cover shrink-0 ring-1 ring-slate-100">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-block rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-extrabold text-[#4154e8]">
                                        {{ $sesi['category'] }}
                                    </span>
                                    <span class="flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">
                                        @include('partials.icons', ['name' => 'clock', 'class' => 'h-3 w-3'])
                                        {{ $sesi['time_left'] }}
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-[#1e293b] mt-1 truncate">{{ $sesi['store'] }}</h3>
                                <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                    @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-3 w-3 text-slate-400'])
                                    {{ $sesi['distance'] }}
                                </p>
                            </div>
                        </div>

                        <!-- Progress Bar to Minimum Order -->
                        <div class="mt-4 rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-slate-700">Terkumpul: <strong>Rp {{ number_format($sesi['current_amount'], 0, ',', '.') }}</strong></span>
                                <span class="text-slate-500 font-semibold">Target Min: Rp {{ number_format($sesi['target_amount'], 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-[#4154e8] to-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $sesi['percent'] }}%"></div>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-[11px]">
                                <span class="font-extrabold text-emerald-600">
                                    Kurang Rp {{ number_format($sesi['target_amount'] - $sesi['current_amount'], 0, ',', '.') }} lagi untuk {{ $sesi['saving_est'] }}!
                                </span>
                                <span class="font-bold text-slate-400">{{ $sesi['percent'] }}%</span>
                            </div>
                        </div>

                        <!-- Popular items in pooled basket -->
                        <div class="mt-3.5">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Item Dalam Keranjang Bersama:</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($sesi['popular_items'] as $item)
                                    <span class="rounded-lg bg-white border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $item }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Footer Card -->
                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-extrabold text-xs">
                                {{ $sesi['host_avatar'] }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 leading-tight">{{ $sesi['host'] }}</p>
                                <p class="text-[10px] text-slate-400">{{ $sesi['participants_count'] }} tetangga telah gabung · {{ $sesi['pickup_point'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="alert('Kamu berhasil mengajukan gabung ke sesi belanja {{ $sesi['store'] }}!')" class="w-full sm:w-auto flex items-center justify-center gap-1.5 rounded-xl bg-[#4154e8] px-4 py-2.5 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 hover:bg-[#3243d4] transition active:scale-[0.99]">
                                @include('partials.icons', ['name' => 'cart', 'class' => 'h-4 w-4'])
                                Gabung Keranjang Ini
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Partner Stores with Bulk Discounts -->
    <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-extrabold text-[#1e293b]">Toko & Mitra Grosir Rekomendasi</h3>
                <p class="text-xs text-slate-500">Buka sesi baru untuk toko di bawah ini dan undang tetangga sekitar</p>
            </div>
            <span class="text-xs font-bold text-[#4154e8]">Mitra Terverifikasi BARENG</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($stores as $store)
                <div class="rounded-2xl border border-slate-100 p-4 hover:border-[#4154e8] transition bg-white group flex flex-col justify-between">
                    <div>
                        <div class="h-28 overflow-hidden rounded-xl bg-slate-100 mb-3">
                            <img src="{{ $store['image'] }}" alt="{{ $store['name'] }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[10px] font-bold text-slate-400">{{ $store['category'] }}</span>
                            <span class="flex items-center gap-0.5 text-xs font-extrabold text-amber-500">★ {{ $store['rating'] }}</span>
                        </div>
                        <h4 class="text-sm font-extrabold text-[#1e293b] mt-1">{{ $store['name'] }}</h4>
                        <p class="text-[11px] text-emerald-600 font-semibold mt-1 leading-snug">{{ $store['promo'] }}</p>
                    </div>

                    <button type="button" onclick="alert('Form pembuatan sesi belanja di {{ $store['name'] }} berhasil disiapkan!')" class="mt-3.5 w-full rounded-xl bg-slate-100 hover:bg-[#4154e8] hover:text-white py-2 text-xs font-bold text-slate-700 transition">
                        Buka Sesi Belanja &rarr;
                    </button>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

