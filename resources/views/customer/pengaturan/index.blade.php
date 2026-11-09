@extends('layouts.app')

@section('title', 'Pengaturan Akun & Preferensi')

@section('content')
@php
    $rawName = trim($user['name'] ?? 'User');
    $nameParts = preg_split('/\s+/', $rawName);
    $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
    $radius = (int) ($user['matching_radius'] ?? 800);
@endphp

<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#4154e8]/10 px-3 py-1 text-xs font-bold text-[#4154e8]">
                    @include('partials.icons', ['name' => 'settings', 'class' => 'h-3.5 w-3.5'])
                    Pusat Pengaturan
                </span>
                <span class="text-xs font-semibold text-slate-400">·</span>
                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Akun Aktif
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1e293b] tracking-tight">Pengaturan & Profil</h1>
            <p class="text-xs md:text-sm text-[#7e8ba0]">Kelola profil, titik temu paket, radius matching tetangga, dan notifikasi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                &larr; Kembali ke Dashboard
            </a>
            <button type="submit" form="settings-form" class="flex items-center gap-2 rounded-xl bg-[#4154e8] px-5 py-2.5 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/25 hover:bg-[#3243d4] transition active:scale-[0.99]">
                @include('partials.icons', ['name' => 'check', 'class' => 'h-4 w-4'])
                Simpan Perubahan
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 shadow-sm animate-fade-in">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white font-bold">
                @include('partials.icons', ['name' => 'check', 'class' => 'h-4 w-4'])
            </span>
            <div class="text-xs">
                <p class="font-bold text-emerald-900">Perubahan Berhasil Disimpan!</p>
                <p class="text-emerald-700 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-xs font-bold text-red-700 shadow-sm">
            <div class="flex items-center gap-2 mb-1 text-red-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Periksa kembali isian formulir:
            </div>
            <ul class="list-disc list-inside font-normal space-y-0.5 ml-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Overview Card -->
    <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-2xl font-black shadow-md ring-4 ring-[#edf1f7] select-none">
                        {{ $userInitials }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white ring-2 ring-white" title="Cluster Terverifikasi">
                        @include('partials.icons', ['name' => 'check', 'class' => 'h-3.5 w-3.5'])
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-extrabold text-[#1e293b]">{{ $user['name'] ?? 'Grace Stanley' }}</h2>
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-[10px] font-extrabold text-[#4154e8] border border-blue-200">
                            {{ $user['badge'] ?? 'Top Saver' }}
                        </span>
                    </div>
                    <p class="text-xs text-[#7e8ba0] mt-0.5">{{ $user['email'] ?? 'grace@bareng.id' }} · {{ $user['role'] ?? 'Customer' }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                            @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-3 w-3 text-[#4154e8]'])
                            {{ $user['location'] ?? 'Beji, Depok' }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                            @include('partials.icons', ['name' => 'shield', 'class' => 'h-3 w-3 text-emerald-600'])
                            ID Terverifikasi Cluster
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Mini Bar -->
            <div class="grid grid-cols-3 gap-3 border-t sm:border-t-0 sm:border-l border-slate-100 pt-4 sm:pt-0 sm:pl-6 text-center">
                <div class="rounded-2xl bg-[#f8faff] p-3">
                    <p class="text-xs text-slate-400 font-semibold">Total Sesi</p>
                    <p class="text-lg font-extrabold text-[#1e293b] mt-0.5">{{ $user['sessions_count'] ?? 12 }}</p>
                </div>
                <div class="rounded-2xl bg-[#f8faff] p-3">
                    <p class="text-xs text-slate-400 font-semibold">Uang Hemat</p>
                    <p class="text-lg font-extrabold text-emerald-600 mt-0.5">{{ $user['savings'] ?? 'Rp 95.4k' }}</p>
                </div>
                <div class="rounded-2xl bg-[#f8faff] p-3">
                    <p class="text-xs text-slate-400 font-semibold">CO₂ Dicegah</p>
                    <p class="text-lg font-extrabold text-[#4154e8] mt-0.5">{{ $user['co2_saved'] ?? '28.4 kg' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form id="settings-form" action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Column: Personal Data & Pickup Points -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Section: Informasi Profil -->
                <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#4154e8]/10 text-[#4154e8]">
                            @include('partials.icons', ['name' => 'users', 'class' => 'h-5 w-5'])
                        </span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#1e293b]">Data Diri & Kontak</h3>
                            <p class="text-xs text-[#7e8ba0]">Informasi dasar identitas kamu di jaringan BARENG</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="name" class="block text-xs font-bold text-[#1d355d] mb-1.5">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user['name'] ?? '') }}" required
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-xs font-bold text-[#1d355d] mb-1.5">Alamat Email</label>
                                <input type="email" id="email" value="{{ $user['email'] ?? 'grace@bareng.id' }}" disabled
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 cursor-not-allowed">
                                <span class="text-[10px] text-slate-400 mt-1 block">Email login utama (terverifikasi)</span>
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-[#1d355d] mb-1.5">No. WhatsApp</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user['phone'] ?? '081234567890') }}"
                                       placeholder="08123456789"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                                <span class="text-[10px] text-slate-400 mt-1 block">Untuk koordinasi penjemputan sesi</span>
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-xs font-bold text-[#1d355d] mb-1.5">Wilayah Domisili / Cluster</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $user['location'] ?? 'Beji, Depok') }}" required
                                   placeholder="Contoh: Cluster Beji Indah, Margonda"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                            <span class="text-[10px] text-slate-400 mt-1 block">Menentukan komunitas terdekat dan rekomendasi merchant</span>
                        </div>
                    </div>
                </div>

                <!-- Section: Titik Temu & Radius Matching -->
                <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-600">
                            @include('partials.icons', ['name' => 'map-pin', 'class' => 'h-5 w-5'])
                        </span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#1e293b]">Titik Temu & Radius Matching</h3>
                            <p class="text-xs text-[#7e8ba0]">Lokasi drop-off paket dan jarak pencarian tetangga otomatis</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="pickup_point" class="block text-xs font-bold text-[#1d355d] mb-1.5">Titik Temu / Drop Point Utama</label>
                            <input type="text" id="pickup_point" name="pickup_point" value="{{ old('pickup_point', $user['pickup_point'] ?? 'Pos Satpam Cluster Beji Blok B-12') }}"
                                   placeholder="Contoh: Pos Satpam Cluster Beji Blok B-12 / Lobby Apartemen Margonda"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                            <span class="text-[10px] text-slate-400 mt-1 block">Titik aman pengambilan pesanan makan, belanja sembako, dan paket kirim</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#1d355d] mb-2">Radius Smart Matching Otomatis</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                @foreach ([
                                    ['val' => 300, 'label' => '300 Meter', 'sub' => '1 Cluster'],
                                    ['val' => 500, 'label' => '500 Meter', 'sub' => '1 RT/RW'],
                                    ['val' => 800, 'label' => '800 Meter', 'sub' => 'Rekomendasi'],
                                    ['val' => 1200, 'label' => '1.2 KM', 'sub' => 'Maksimal'],
                                ] as $opt)
                                    <label class="relative flex flex-col p-3 rounded-2xl border text-center cursor-pointer transition hover:border-[#4154e8] {{ $radius === $opt['val'] ? 'border-[#4154e8] bg-blue-50/50 text-[#4154e8]' : 'border-slate-200 bg-white text-slate-600' }}">
                                        <input type="radio" name="matching_radius" value="{{ $opt['val'] }}" {{ $radius === $opt['val'] ? 'checked' : '' }} class="sr-only">
                                        <span class="text-xs font-extrabold">{{ $opt['label'] }}</span>
                                        <span class="text-[10px] opacity-75 mt-0.5">{{ $opt['sub'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-bold text-[#1d355d] mb-1.5">Catatan Khusus untuk Kurir / Host</label>
                            <textarea id="notes" name="notes" rows="2"
                                      placeholder="Contoh: Jika saya sedang di kantor, titipkan ke satpam Pak Joko."
                                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">{{ old('notes', $user['notes'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Preferences & Security -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Section: Preferensi Gotong Royong & Notifikasi -->
                <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600">
                            @include('partials.icons', ['name' => 'leaf', 'class' => 'h-5 w-5'])
                        </span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#1e293b]">Preferensi & Gaya Hidup</h3>
                            <p class="text-xs text-[#7e8ba0]">Pengaturan otomatisasi dan notifikasi</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <label class="flex items-start gap-3 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                            <input type="checkbox" name="eco_mode" value="1" {{ !empty($user['eco_mode']) ? 'checked' : '' }}
                                   class="mt-1 h-4 w-4 rounded border-slate-300 text-[#4154e8] focus:ring-[#4154e8]">
                            <div>
                                <span class="text-xs font-bold text-[#1d355d] block">Mode Kemasan Hijau (Eco Friendly)</span>
                                <span class="text-[11px] text-slate-500 leading-relaxed block mt-0.5">
                                    Prioritaskan merchant tanpa plastik berlebih & gabungkan kantong pesanan tetangga.
                                </span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                            <input type="checkbox" name="notif_whatsapp" value="1" {{ !empty($user['notif_whatsapp']) ? 'checked' : '' }}
                                   class="mt-1 h-4 w-4 rounded border-slate-300 text-[#4154e8] focus:ring-[#4154e8]">
                            <div>
                                <span class="text-xs font-bold text-[#1d355d] block">Notifikasi WhatsApp Otomatis</span>
                                <span class="text-[11px] text-slate-500 leading-relaxed block mt-0.5">
                                    Dapatkan peringatan ketika ada sesi diskon belanja atau makanan baru di clustermu.
                                </span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                            <input type="checkbox" name="notif_email" value="1" {{ !empty($user['notif_email']) ? 'checked' : '' }}
                                   class="mt-1 h-4 w-4 rounded border-slate-300 text-[#4154e8] focus:ring-[#4154e8]">
                            <div>
                                <span class="text-xs font-bold text-[#1d355d] block">Laporan Hemat & Dampak CO₂</span>
                                <span class="text-[11px] text-slate-500 leading-relaxed block mt-0.5">
                                    Kirim ringkasan mingguan rupiah yang berhasil kamu hemat bersama tetangga.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section: Keamanan & Ganti Password -->
                <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-600">
                            @include('partials.icons', ['name' => 'shield', 'class' => 'h-5 w-5'])
                        </span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#1e293b]">Keamanan & Password</h3>
                            <p class="text-xs text-[#7e8ba0]">Kosongkan jika tidak ingin mengubah password</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3.5">
                        <div>
                            <label for="current_password" class="block text-xs font-bold text-[#1d355d] mb-1">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                                       placeholder="Masukkan password lama"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-11 text-xs outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                                <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#4154e8] transition">
                                    <svg class="h-4 w-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg class="h-4 w-4 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="new_password" class="block text-xs font-bold text-[#1d355d] mb-1">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" minlength="6" autocomplete="new-password"
                                       placeholder="Minimal 6 karakter"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-11 text-xs outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                                <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#4154e8] transition">
                                    <svg class="h-4 w-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg class="h-4 w-4 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-bold text-[#1d355d] mb-1">Ulangi Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" minlength="6" autocomplete="new-password"
                                       placeholder="Ulangi password baru"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-11 text-xs outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                                <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#4154e8] transition">
                                    <svg class="h-4 w-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg class="h-4 w-4 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Save Action Mobile/Side -->
                <div class="rounded-[28px] bg-gradient-to-br from-[#4154e8] to-[#2d3ec4] p-6 text-white shadow-lg shadow-[#4154e8]/20">
                    <h4 class="text-sm font-extrabold">Simpan Perubahan</h4>
                    <p class="text-xs text-white/80 mt-1 leading-relaxed">
                        Data profil & titik temu kamu langsung diperbarui di sistem pencocokan otomatis tetangga.
                    </p>
                    <button type="submit" class="mt-4 w-full flex items-center justify-center gap-2 rounded-xl bg-white py-3 text-xs font-extrabold text-[#4154e8] shadow-sm transition hover:bg-white/90 active:scale-[0.99]">
                        @include('partials.icons', ['name' => 'check', 'class' => 'h-4 w-4'])
                        SIMPAN PENGATURAN SEKARANG
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

