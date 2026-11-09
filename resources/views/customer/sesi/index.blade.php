@extends('layouts.app')

@section('title', 'Sesi Aktif Gotong Royong')

@section('content')
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#4154e8]/10 px-3 py-1 text-xs font-bold text-[#4154e8]">
                    @include('partials.icons', ['name' => 'schedule', 'class' => 'h-3.5 w-3.5'])
                    Pusat Sesi Aktif
                </span>
                <span class="text-xs font-semibold text-slate-400">·</span>
                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ count($mySessions) }} Sesi Sedang Kamu Ikuti
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1e293b] tracking-tight">Monitoring Sesi Gotong Royong</h1>
            <p class="text-xs md:text-sm text-[#7e8ba0]">Pantau live status pesanan makan, belanja sembako, dan titipan paket di clustermu.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('makan') }}" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                + Sesi Makan
            </a>
            <a href="{{ route('belanja') }}" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                + Sesi Belanja
            </a>
            <a href="{{ route('kirim') }}" class="rounded-xl bg-[#4154e8] px-4 py-2.5 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 hover:bg-[#3243d4] transition">
                + Sesi Kirim
            </a>
        </div>
    </div>

    <!-- Section 1: Sesi yang Sedang Kamu Ikuti (Live Stepper Tracking) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg md:text-xl font-extrabold text-[#1e293b] flex items-center gap-2">
                <span>Sesi yang Sedang Kamu Ikuti</span>
                <span class="rounded-full bg-[#4154e8] text-white text-xs px-2.5 py-0.5 font-bold">
                    {{ count($mySessions) }}
                </span>
            </h2>
            <span class="text-xs text-slate-500">Pembaruan real-time setiap menit</span>
        </div>

        <div class="space-y-4">
            @foreach ($mySessions as $sesi)
                <div class="rounded-[28px] bg-white p-5 lg:p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-4">
                            <img src="{{ $sesi['image'] }}" alt="{{ $sesi['merchant'] }}" class="h-16 w-16 rounded-2xl object-cover shrink-0 ring-1 ring-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-extrabold uppercase {{ $sesi['type'] === 'makan' ? 'bg-amber-100 text-amber-800' : ($sesi['type'] === 'belanja' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                        Bareng {{ ucfirst($sesi['type']) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400">·</span>
                                    <span class="text-xs font-extrabold text-[#4154e8]">{{ $sesi['eta'] }}</span>
                                </div>
                                <h3 class="text-base md:text-lg font-extrabold text-[#1e293b] mt-0.5">{{ $sesi['title'] }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $sesi['host'] }} · Titik Temu: <strong class="text-slate-700">{{ $sesi['pickup_point'] }}</strong></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 self-start lg:self-center">
                            <button type="button" onclick="alert('Membuka grup chat koordinasi sesi ini...')" class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                @include('partials.icons', ['name' => 'mail', 'class' => 'h-4 w-4 text-[#4154e8]'])
                                Chat Koordinasi ({{ $sesi['participants_count'] }})
                            </button>
                            <button type="button" onclick="alert('Nomor kontak Host / Kurir: 0812-9876-5432')" class="flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3.5 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition">
                                @include('partials.icons', ['name' => 'phone', 'class' => 'h-4 w-4'])
                                Hubungi
                            </button>
                        </div>
                    </div>

                    <!-- Live Stepper Tracker -->
                    <div class="py-5">
                        <div class="grid grid-cols-4 gap-2 relative">
                            @php
                                $steps = [
                                    1 => '1. Kuota Terkumpul',
                                    2 => '2. Dikonfirmasi Toko',
                                    3 => '3. Dalam Perjalanan',
                                    4 => '4. Tiba di Drop Point',
                                ];
                            @endphp

                            @foreach ($steps as $stepNum => $stepTitle)
                                @php
                                    $isDone = $sesi['status_step'] >= $stepNum;
                                    $isCurrent = $sesi['status_step'] === $stepNum;
                                @endphp
                                <div class="flex flex-col items-center text-center">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-extrabold transition {{ $isDone ? 'bg-[#4154e8] text-white ring-4 ring-blue-100' : 'bg-slate-100 text-slate-400' }}">
                                        @if ($isDone && !$isCurrent)
                                            @include('partials.icons', ['name' => 'check', 'class' => 'h-4 w-4'])
                                        @else
                                            {{ $stepNum }}
                                        @endif
                                    </div>
                                    <p class="text-[11px] font-extrabold mt-2 {{ $isDone ? 'text-slate-800' : 'text-slate-400' }}">{{ $stepTitle }}</p>
                                    @if ($isCurrent)
                                        <span class="text-[9px] font-bold text-[#4154e8] bg-blue-50 px-2 py-0.5 rounded-full mt-0.5">Status Saat Ini</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detail Order & Savings Summary -->
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[11px]">Pesanan Kamu:</span>
                            <span class="font-extrabold text-slate-800 text-sm">{{ $sesi['my_items'] }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-slate-400 font-semibold block text-[11px]">Total Bayarmu:</span>
                                <span class="font-black text-[#4154e8] text-sm">{{ $sesi['my_total'] }}</span>
                            </div>
                            <div class="border-l border-slate-200 pl-4">
                                <span class="text-slate-400 font-semibold block text-[11px]">Dampak Hemat:</span>
                                <span class="font-extrabold text-emerald-600 text-xs">{{ $sesi['saved_ongkir'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section 2: Sesi Komunitas Terbuka di Sekitarmu -->
    <div class="space-y-4 pt-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg md:text-xl font-extrabold text-[#1e293b]">Sesi Terbuka di Sekitarmu yang Bisa Kamu Gabung</h2>
                <p class="text-xs text-slate-500">Bergabung sebelum waktu sesi berakhir untuk menikmati bagi ongkir otomatis</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Filter:</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">Radius < 800m</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($exploreSessions as $item)
                <div class="rounded-[24px] bg-white p-4 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="relative h-32 overflow-hidden rounded-2xl bg-slate-100 mb-3">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="h-full w-full object-cover">
                            <span class="absolute top-2 left-2 rounded-lg px-2 py-0.5 text-[10px] font-extrabold border shadow-sm {{ $item['type_color'] }}">
                                {{ $item['type_label'] }}
                            </span>
                            <span class="absolute bottom-2 right-2 rounded-lg bg-black/60 backdrop-blur-md px-2 py-0.5 text-[10px] font-bold text-white">
                                ⏳ {{ $item['closing_in'] }}
                            </span>
                        </div>

                        <h3 class="text-sm font-extrabold text-[#1e293b] leading-snug line-clamp-1">{{ $item['title'] }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                            @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-3 w-3 text-slate-400'])
                            {{ $item['distance'] }}
                        </p>

                        <div class="mt-3 space-y-1 rounded-xl bg-slate-50 p-2 text-[11px]">
                            <div class="flex justify-between font-semibold text-slate-600">
                                <span>Status Slot:</span>
                                <span class="font-bold text-[#4154e8]">{{ $item['slots'] }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-slate-600">
                                <span>Ongkirmu:</span>
                                <span class="font-extrabold text-emerald-600">{{ $item['ongkir_split'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-slate-400 truncate">Host: {{ $item['host'] }}</span>
                        <button type="button" onclick="alert('Berhasil bergabung ke sesi {{ $item['title'] }}!')" class="rounded-xl bg-[#4154e8] px-3.5 py-1.5 text-xs font-bold text-white hover:bg-[#3243d4] transition shrink-0">
                            Gabung Sesi &rarr;
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

