@extends('layouts.app')

@section('title', 'Bareng Kirim · Logistik & Titip Paket Rute Searah')

@section('content')
<div class="space-y-6">

    <!-- Hero Banner with Stats -->
    <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-r from-[#1c243a] via-[#243354] to-[#4154e8] p-6 lg:p-8 text-white shadow-xl shadow-[#4154e8]/10">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute right-1/4 -bottom-10 h-48 w-48 rounded-full bg-blue-400/10 blur-xl"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 items-center gap-6">
            <div class="lg:col-span-8 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 backdrop-blur-md px-3.5 py-1 text-xs font-bold text-white shadow-sm">
                        @include('partials.icons', ['name' => 'truck', 'class' => 'h-3.5 w-3.5'])
                        Bareng Kirim Logistik Searah
                    </span>
                    <span class="rounded-full bg-blue-500/20 px-3 py-1 text-xs font-bold text-blue-300 border border-blue-400/30">
                        🌱 Zero Empty Trips · Hemat Ongkir s/d 67%
                    </span>
                </div>
                <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight leading-tight">
                    Kirim Paket Searah Bareng Tetangga, Tanpa Biaya Kurir Mahal!
                </h1>
                <p class="text-xs md:text-sm text-white/80 max-w-2xl leading-relaxed">
                    Titipkan barang ke tetangga atau kurir komuter yang sedang menuju arah yang sama dari <strong class="text-white">{{ $user['location'] ?? 'Beji, Depok' }}</strong>. Kendaraan tidak jalan kosong, ongkir dibagi bersama.
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <button type="button" onclick="alert('Form Buat Rute Kirim Baru dibuka!')" class="flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-xs font-extrabold text-[#1c243a] shadow-lg shadow-black/10 hover:bg-slate-100 transition active:scale-[0.99]">
                        @include('partials.icons', ['name' => 'plus', 'class' => 'h-4 w-4 text-[#4154e8]'])
                        Buka Rute / Titip Paket Baru
                    </button>
                    <a href="{{ route('sesi') }}" class="flex items-center gap-2 rounded-2xl bg-white/15 backdrop-blur border border-white/25 px-5 py-3 text-xs font-bold text-white hover:bg-white/25 transition">
                        @include('partials.icons', ['name' => 'schedule', 'class' => 'h-4 w-4'])
                        Pantau Pengiriman Aktif
                    </a>
                </div>
            </div>

            <div class="lg:col-span-4 grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                    <p class="text-[11px] text-white/70 font-semibold">Rute Berangkat Hari Ini</p>
                    <p class="text-2xl font-black text-white mt-1">{{ count($routes) }} Rute</p>
                    <p class="text-[10px] text-blue-300 mt-1 font-bold">Kapasitas siap tampung</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                    <p class="text-[11px] text-white/70 font-semibold">Ongkir Rata-Rata</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1">Rp 6k-8k</p>
                    <p class="text-[10px] text-emerald-300 mt-1 font-bold">Normal Rp 18k-28k</p>
                </div>
                <div class="col-span-2 rounded-2xl bg-blue-500/20 backdrop-blur-md border border-blue-400/30 p-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-500 text-white">
                            @include('partials.icons', ['name' => 'leaf', 'class' => 'h-4 w-4'])
                        </span>
                        <div class="text-left">
                            <p class="text-xs font-bold text-white leading-tight">Pengurangan Emisi Karbon</p>
                            <p class="text-[10px] text-white/70">Mengurangi perjalanan kendaraan terpisah hingga 70%</p>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-blue-300">28.4 kg CO₂</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Delivery Simulator -->
    <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-[#1e293b]">Simulasi Titip Paket Kilat Searah</h3>
                <p class="text-xs text-slate-500">Cek estimasi ongkir hemat berdasarkan rute tetangga di clustermu</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                @include('partials.icons', ['name' => 'sparkles', 'class' => 'h-3.5 w-3.5'])
                Smart Route Pooling
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Titik Asal / Pickup</label>
                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-700">
                    @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-4 w-4 text-[#4154e8] shrink-0'])
                    <span class="truncate">{{ $user['location'] ?? 'Cluster Beji Indah, Depok' }}</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tujuan Pengiriman</label>
                <select class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 outline-none focus:border-[#4154e8]">
                    <option>Margonda & Kampus UI Depok (Hemat 67%)</option>
                    <option>Stasiun Depok Lama & ITC Depok (Hemat 66%)</option>
                    <option>Cinere & Gandul (Hemat 65%)</option>
                    <option>Lenteng Agung & Jakarta Selatan (Hemat 64%)</option>
                </select>
            </div>

            <div>
                <button type="button" onclick="alert('Rute ditemukan! Ditemukan 2 tetangga yang berangkat ke arah tersebut hari ini.')" class="w-full rounded-xl bg-[#4154e8] hover:bg-[#3243d4] py-2.5 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 transition">
                    Cari Rute Tetangga Cocok &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- Active Pooling Routes Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg md:text-xl font-extrabold text-[#1e293b]">Rute Kirim Berangkat Hari Ini</h2>
                <p class="text-xs text-slate-500">Pilih rute yang sesuai dengan tujuan paketmu dan titipkan langsung</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#4154e8] border border-blue-200">
                {{ count($routes) }} Rute Terjadwal
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($routes as $route)
                <div class="rounded-[28px] bg-white p-5 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Header with Route & Status -->
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                            <div>
                                <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-extrabold text-[#4154e8]">
                                    @include('partials.icons', ['name' => 'truck', 'class' => 'h-3 w-3'])
                                    {{ $route['vehicle'] }}
                                </span>
                                <h3 class="text-base font-extrabold text-[#1e293b] mt-1.5 flex items-center gap-1.5">
                                    <span>{{ $route['origin'] }}</span>
                                    <span class="text-slate-400 font-normal">&rarr;</span>
                                    <span class="text-[#4154e8]">{{ $route['destination'] }}</span>
                                </h3>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 border border-emerald-200 shrink-0">
                                {{ $route['status'] }}
                            </span>
                        </div>

                        <!-- Schedule & Capacity Grid -->
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-center">
                            <div class="rounded-2xl bg-slate-50 p-2.5 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-semibold">Jadwal Berangkat</p>
                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $route['departure_time'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-2.5 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-semibold">Kapasitas Slot</p>
                                <p class="text-xs font-extrabold text-blue-600 mt-0.5">
                                    {{ $route['slots_filled'] }}/{{ $route['slots_total'] }} Slot Terisi
                                </p>
                            </div>
                            <div class="col-span-2 sm:col-span-1 rounded-2xl bg-emerald-50 p-2.5 border border-emerald-100">
                                <p class="text-[10px] text-emerald-600 font-semibold">Ongkir Bareng</p>
                                <p class="text-xs font-extrabold text-emerald-700 mt-0.5">{{ $route['cost_per_item'] }}</p>
                                <span class="text-[9px] line-through text-slate-400">{{ $route['normal_cost'] }}</span>
                            </div>
                        </div>

                        <!-- Package notes & drop point -->
                        <div class="mt-3.5 space-y-1.5 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 text-[11px]">Jenis Paket:</span>
                                <span class="font-semibold">{{ $route['package_sizes'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 text-[11px]">Titik Drop:</span>
                                <span class="font-semibold">{{ $route['drop_point'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with Host and CTA -->
                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-extrabold text-xs">
                                {{ $route['carrier_avatar'] }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 leading-tight">{{ $route['carrier_name'] }}</p>
                                <p class="text-[10px] text-slate-400">{{ $route['carrier_role'] }}</p>
                            </div>
                        </div>

                        <button type="button" onclick="alert('Pengajuan titip paket ke rute {{ $route['destination'] }} berhasil dibuat! Host akan mengonfirmasi.')" class="flex items-center justify-center gap-1.5 rounded-xl bg-[#4154e8] px-4 py-2 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 hover:bg-[#3243d4] transition active:scale-[0.99]">
                            @include('partials.icons', ['name' => 'box', 'class' => 'h-4 w-4'])
                            Titip di Rute Ini
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cluster Drop-off Info -->
    <div class="rounded-[28px] bg-slate-50 p-6 border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-[#4154e8] shadow-sm">
                    @include('partials.icons', ['name' => 'shield', 'class' => 'h-5 w-5'])
                </span>
                <div>
                    <h4 class="text-sm font-extrabold text-[#1e293b]">Keamanan & Garansi Paket Gotong Royong</h4>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                        Setiap paket diverifikasi dengan kode OTP serah terima di titik temu dan dilindungi jaminan keamanan komunitas BARENG.
                    </p>
                </div>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-xl shrink-0 self-start sm:self-center">
                100% Terpantau GPS Sesi
            </span>
        </div>
    </div>

</div>
@endsection

