@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
@php
    $rawName = trim($user['name'] ?? 'User');
    $nameParts = preg_split('/\s+/', $rawName);
    $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
    $currentGender = $user['gender'] ?? 'prefer_not_to_say';
    $completionPct = $completion ?? 65;
    $joinDate = isset($userModel->created_at) ? $userModel->created_at->translatedFormat('F Y') : 'November 2026';
@endphp

<div class="mx-auto max-w-4xl space-y-6 pb-24">

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-3.5 text-xs font-bold text-emerald-800 shadow-sm animate-fade-in">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
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

    <!-- CARD 1: KELENGKAPAN PROFIL (PERSIS FOTO 2) -->
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-[#4154e8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <h2 class="text-sm font-extrabold text-slate-800 tracking-tight">Kelengkapan Profil</h2>
            </div>
            <span class="inline-flex items-center justify-center rounded-full border border-[#4154e8]/40 bg-[#4154e8]/5 px-3 py-0.5 text-xs font-extrabold text-[#4154e8]">
                {{ $completionPct }}%
            </span>
        </div>

        <!-- Progress Bar -->
        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full rounded-full bg-[#4154e8] transition-all duration-700" style="width: {{ $completionPct }}%;"></div>
        </div>

        <p class="text-xs text-slate-500 font-medium">
            Profil yang lengkap membuat sertifikat dan portofoliomu jauh lebih meyakinkan bagi calon perekrut.
        </p>
    </div>

    <!-- MAIN FORM: INFORMASI PROFIL DIRI (PERSIS FOTO 2, 3, 4, 5) -->
    <form id="profile-edit-form" method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Hidden input for base64 cropped avatar -->
        <input type="hidden" name="avatar_cropped" id="avatar_cropped">

        <!-- CARD 2: INFORMASI PROFIL DIRI -->
        <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
            
            <!-- Header Card (Foto 2) -->
            <div class="flex items-start gap-3 pb-2">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#4154e8]/10 text-[#4154e8]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 leading-tight">Informasi Profil Diri</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola identitas utama yang tampil di profil publik, komunitas, dan sertifikat</p>
                </div>
            </div>

            <!-- Inner Bordered Section (Foto 2 & 3) -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 text-center space-y-4">
                
                <!-- Avatar Circle with Camera Badge -->
                <div class="relative mx-auto w-28 h-28">
                    @if (!empty($user['avatar']))
                        <img id="avatar-preview" src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}"
                             class="h-28 w-28 rounded-full object-cover shadow-sm ring-4 ring-slate-100">
                    @else
                        <div id="avatar-preview-fallback" class="flex h-28 w-28 items-center justify-center rounded-full bg-[#e0e7ff] text-[#4154e8] text-3xl font-black shadow-sm ring-4 ring-slate-100 select-none">
                            {{ $userInitials }}
                        </div>
                        <img id="avatar-preview" src="" alt="Avatar" class="h-28 w-28 rounded-full object-cover shadow-sm ring-4 ring-slate-100 hidden">
                    @endif

                    <!-- Small Camera Badge Button -->
                    <button type="button" onclick="document.getElementById('avatar_file').click()"
                            class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-md border border-slate-200 text-slate-700 hover:text-[#4154e8] hover:border-[#4154e8] transition"
                            title="Ganti Foto">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>

                <!-- User identity in center -->
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">{{ $user['name'] }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $user['email'] ?? 'user@bareng.id' }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-slate-50 border border-slate-200/80 px-3 py-1 text-[11px] font-semibold text-slate-600">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Bergabung {{ $joinDate }}</span>
                    </div>
                </div>

                <!-- Button: Pilih & Sesuaikan Foto (Foto 3) -->
                <div class="pt-2">
                    <input type="file" id="avatar_file" accept="image/*" class="hidden" onchange="initAvatarCrop(this)">
                    <button type="button" onclick="document.getElementById('avatar_file').click()"
                            class="inline-flex items-center gap-2 rounded-full border border-[#4154e8] bg-[#f0f4ff] hover:bg-[#e0e7ff] px-6 py-2.5 text-xs font-bold text-[#4154e8] shadow-sm transition active:scale-95">
                        <svg class="h-4 w-4 text-[#4154e8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Pilih & Sesuaikan Foto</span>
                    </button>
                    <p class="text-[11px] text-slate-400 mt-2">Format JPG, PNG, atau WebP. Foto akan dipotong 1:1 agar proporsional di seluruh sistem.</p>
                </div>
            </div>

            <!-- Form Inputs (Foto 4) -->
            <div class="space-y-5 pt-2">

                <!-- 1. Nama Lengkap * -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400 pointer-events-none">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input type="text" name="name" value="{{ old('name', $user['name']) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15 transition">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Nama ini yang tercetak di sertifikat resmi. Jika diubah, sertifikat yang sudah kamu dapatkan akan otomatis diperbarui dengan nama baru.
                    </p>
                </div>

                <!-- 2. Nama Pengguna (Username) (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-1.5">
                        Nama Pengguna (Username) <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400 pointer-events-none">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </span>
                        <input type="text" name="username" value="{{ old('username', $user['username'] ?? '') }}"
                               placeholder="contoh: alexander_dev"
                               class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15 transition">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Gunakan huruf kecil, angka, titik, strip, atau garis bawah tanpa spasi. Digunakan untuk tautan unik profilmu.
                    </p>
                </div>

                <!-- 3. Headline Singkat (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-1.5">
                        Headline Singkat <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400 pointer-events-none">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                <line x1="7" y1="8" x2="17" y2="8"></line>
                                <line x1="7" y1="12" x2="13" y2="12"></line>
                            </svg>
                        </span>
                        <input type="text" name="headline" value="{{ old('headline', $user['headline'] ?? '') }}"
                               placeholder="Contoh: Mahasiswa Teknik Informatika · Tertarik di UI/UX & Web Dev"
                               class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15 transition">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Satu baris ringkas tentang fokusmu saat ini, jurusan, atau bidang keahlian yang sedang kamu tekuni.
                    </p>
                </div>

                <!-- 4. Tentang Saya (Bio) (Opsional) with Rich Toolbar (Foto 4) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-900">
                            Tentang Saya (Bio) <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <span id="bio-counter" class="text-[10px] text-slate-400 font-semibold">0 / 500</span>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden focus-within:border-[#4154e8] focus-within:ring-2 focus-within:ring-[#4154e8]/15 transition">
                        <!-- Toolbar Formatting (Foto 4) -->
                        <div class="flex items-center gap-1 px-3 py-2 border-b border-slate-100 bg-slate-50/60 text-slate-600 text-xs">
                            <button type="button" onclick="formatBio('bold')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center font-bold text-xs" title="Bold">B</button>
                            <button type="button" onclick="formatBio('italic')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center italic text-xs font-serif" title="Italic">I</button>
                            <button type="button" onclick="formatBio('underline')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center underline text-xs" title="Underline">U</button>
                            
                            <span class="h-4 w-[1px] bg-slate-200 mx-1"></span>

                            <button type="button" onclick="formatBio('heading')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center font-black text-xs" title="Heading">T</button>
                            
                            <span class="h-4 w-[1px] bg-slate-200 mx-1"></span>

                            <button type="button" onclick="formatBio('bullet')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center text-xs" title="Bullet List">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                            </button>
                            <button type="button" onclick="formatBio('number')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center text-xs font-mono font-bold" title="Numbered List">1.</button>
                            <button type="button" onclick="formatBio('quote')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center text-xs font-serif font-black" title="Quote">“</button>
                            
                            <span class="h-4 w-[1px] bg-slate-200 mx-1"></span>

                            <button type="button" onclick="formatBio('link')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center text-xs" title="Tautan Link">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            </button>
                            <button type="button" onclick="formatBio('clear')" class="h-7 w-7 rounded hover:bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500" title="Bersihkan Format">Tx</button>
                        </div>

                        <!-- Textarea -->
                        <textarea id="bio-textarea" name="bio" rows="4" maxlength="500"
                                  placeholder="Ceritakan singkat tentang dirimu, latar belakang, dan keterampilan yang ingin kamu kembangkan..."
                                  oninput="document.getElementById('bio-counter').innerText = this.value.length + ' / 500'"
                                  class="w-full px-4 py-3 text-xs font-medium text-slate-800 outline-none resize-none leading-relaxed">{{ old('bio', $user['bio'] ?? '') }}</textarea>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Bisa ditebalkan, dibuat poin-poin daftar, atau diberi tautan. Tiga sampai empat kalimat sudah cukup efektif.
                    </p>
                </div>

            </div>

            <!-- CARD FOOTER (PERSIS FOTO 5) -->
            <div class="border-t border-slate-100 pt-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-[11px] text-slate-500 font-medium">
                    <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Perubahan data profil akan langsung diterapkan di seluruh sistem.</span>
                </div>

                <button type="submit"
                        class="w-full sm:w-auto rounded-full bg-[#4154e8] hover:bg-[#3243d4] px-8 py-3 text-xs font-bold text-white shadow-md shadow-[#4154e8]/25 transition active:scale-95 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>

        <!-- CARD 3: PREFERENSI PRIVASI & GOTONG ROYONG -->
        <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-5">
            <div class="flex items-start gap-3 pb-2">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#4154e8]/10 text-[#4154e8]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 leading-tight">Privasi & Preferensi Komunitas</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Atur privasi jenis kelamin serta lokasi pengantaran bersama warga</p>
                </div>
            </div>

            <!-- Gender Section -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-900">Jenis Kelamin (Gender)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'male' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                        <span class="text-xs font-bold">Pria (Male)</span>
                        <input type="radio" name="gender" value="male" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'male' ? 'checked' : '' }}>
                    </label>

                    <label class="relative flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'female' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                        <span class="text-xs font-bold">Wanita (Female)</span>
                        <input type="radio" name="gender" value="female" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'female' ? 'checked' : '' }}>
                    </label>

                    <label class="relative flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'custom' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                        <span class="text-xs font-bold">Lainnya / Custom</span>
                        <input type="radio" name="gender" value="custom" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'custom' ? 'checked' : '' }}>
                    </label>

                    <label class="relative flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'prefer_not_to_say' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                        <div>
                            <span class="text-xs font-bold">Prefer not to say</span>
                            <span class="block text-[10px] text-slate-400">Tidak ingin memberitahu</span>
                        </div>
                        <input type="radio" name="gender" value="prefer_not_to_say" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'prefer_not_to_say' ? 'checked' : '' }}>
                    </label>
                </div>
            </div>

            <!-- Location & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-1.5">Lokasi / Cluster</label>
                    <input type="text" name="location" value="{{ old('location', $user['location'] ?? 'Beji, Depok') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user['phone'] ?? '') }}" placeholder="0812xxxxxxx"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15 transition">
                </div>
            </div>
        </div>

    </form>
</div>

<!-- ========================================================================= -->
<!-- AVATAR CIRCULAR CROP MODAL (ALTA INSTAGRAM / WHATSAPP STYLE) -->
<!-- ========================================================================= -->
<div id="avatar-crop-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md">
    <div class="w-full max-w-md rounded-[28px] bg-white p-6 shadow-2xl border border-slate-100 space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-900 tracking-tight">Sesuaikan Foto Profil</h3>
            <button type="button" onclick="closeAvatarCropModal()" class="p-1 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Crop Canvas Viewport with Circular Mask -->
        <div class="relative mx-auto w-[280px] h-[280px] overflow-hidden rounded-2xl bg-slate-900 flex items-center justify-center cursor-grab active:cursor-grabbing select-none" id="crop-viewport">
            <img id="crop-source-img" src="" alt="Crop Source"
                 class="pointer-events-none select-none origin-center"
                 style="max-width: none !important; max-height: none !important; position: absolute; transform-origin: center center; will-change: transform;">
            <!-- Circular Mask Cutout -->
            <div class="absolute inset-0 pointer-events-none ring-[140px] ring-black/75 rounded-full"></div>
            <!-- Circle Ring Guide -->
            <div class="absolute inset-0 pointer-events-none border-2 border-white/90 rounded-full shadow-[0_0_0_1px_rgba(0,0,0,0.3)]"></div>
        </div>

        <!-- Zoom Slider Bar -->
        <div class="flex items-center justify-between gap-3 px-2 pt-1 text-slate-600">
            <span class="text-xs font-bold text-slate-400">Zoom</span>
            <div class="flex-1 flex items-center gap-2">
                <span class="text-xs">−</span>
                <input type="range" id="crop-zoom-range" min="1" max="3" step="0.05" value="1" oninput="applyAvatarZoom(this.value)"
                       class="w-full accent-[#4154e8] h-1.5 bg-slate-200 rounded-lg cursor-pointer">
                <span class="text-xs">+</span>
            </div>
            <span id="crop-zoom-label" class="text-xs font-bold text-slate-700 w-10 text-right">1.0x</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
            <button type="button" onclick="closeAvatarCropModal()" class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-bold text-slate-600 transition">
                Batal
            </button>
            <button type="button" onclick="confirmAvatarCrop()" class="rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-5 py-2 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 transition active:scale-95">
                Terapkan Foto
            </button>
        </div>
    </div>
</div>

<script>
// ─── Rich Bio Toolbar Helper ─────────────────────────────────────────────────
function formatBio(type) {
    const textarea = document.getElementById('bio-textarea');
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    let replacement = '';
    switch (type) {
        case 'bold':
            replacement = `**${selectedText || 'teks tebal'}**`;
            break;
        case 'italic':
            replacement = `*${selectedText || 'teks miring'}*`;
            break;
        case 'underline':
            replacement = `_${selectedText || 'teks garis bawah'}_`;
            break;
        case 'heading':
            replacement = `\n### ${selectedText || 'Judul Bagian'}\n`;
            break;
        case 'bullet':
            replacement = `\n- ${selectedText || 'Poin daftar'}`;
            break;
        case 'number':
            replacement = `\n1. ${selectedText || 'Item pertama'}`;
            break;
        case 'quote':
            replacement = `\n> ${selectedText || 'Kutipan motivasi'}\n`;
            break;
        case 'link':
            replacement = `[${selectedText || 'Tautan web'}](https://)`;
            break;
        case 'clear':
            replacement = selectedText.replace(/[*_#>`-]/g, '').trim();
            break;
    }

    textarea.setRangeText(replacement, start, end, 'end');
    textarea.focus();
    document.getElementById('bio-counter').innerText = textarea.value.length + ' / 500';
}

// ─── Circular Avatar Cropper (Clamped & Cover Fit) ──────────────────────────
const vpSize = 280;
let cropCurrentZoom = 1;
let cropPanX = 0;
let cropPanY = 0;
let baseW = 280;
let baseH = 280;
let isDraggingCrop = false;
let cropStartX = 0;
let cropStartY = 0;
let rawCropImageObj = null;

function initAvatarCrop(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const modal = document.getElementById('avatar-crop-modal');
        const img = document.getElementById('crop-source-img');

        rawCropImageObj = new Image();
        rawCropImageObj.onload = function() {
            // HITUNG COVER FIT: dimensi terkecil minimal harus sama dengan 280px (vpSize)!
            // Dengan cara ini, foto TIDAK PERNAH memiliki celah kosong di lingkaran crop!
            const scaleCover = Math.max(vpSize / rawCropImageObj.width, vpSize / rawCropImageObj.height);
            baseW = Math.round(rawCropImageObj.width * scaleCover);
            baseH = Math.round(rawCropImageObj.height * scaleCover);

            img.style.width = baseW + 'px';
            img.style.height = baseH + 'px';

            cropCurrentZoom = 1;
            cropPanX = 0;
            cropPanY = 0;

            const slider = document.getElementById('crop-zoom-range');
            if (slider) slider.value = 1;
            const label = document.getElementById('crop-zoom-label');
            if (label) label.innerText = '1.0x';

            updateCropTransform();
            modal.classList.remove('hidden');
        };
        rawCropImageObj.src = e.target.result;
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Clamp posisi pan X & Y agar foto tidak pernah keluar dari lingkaran crop
function clampPan() {
    const curW = baseW * cropCurrentZoom;
    const curH = baseH * cropCurrentZoom;

    // Maksimum geser dihitung dari selisih ukuran foto terhadap viewport
    const maxPanX = Math.max(0, (curW - vpSize) / 2);
    const maxPanY = Math.max(0, (curH - vpSize) / 2);

    cropPanX = Math.max(-maxPanX, Math.min(maxPanX, cropPanX));
    cropPanY = Math.max(-maxPanY, Math.min(maxPanY, cropPanY));
}

function applyAvatarZoom(val) {
    cropCurrentZoom = Math.max(1, Math.min(3, parseFloat(val) || 1));
    document.getElementById('crop-zoom-label').innerText = cropCurrentZoom.toFixed(1) + 'x';
    updateCropTransform();
}

function updateCropTransform() {
    clampPan();
    const img = document.getElementById('crop-source-img');
    if (img) {
        img.style.transform = `translate(${cropPanX}px, ${cropPanY}px) scale(${cropCurrentZoom})`;
    }
}

// Mouse & Touch Drag Event Handlers
const cropViewport = document.getElementById('crop-viewport');
if (cropViewport) {
    // Mouse drag
    cropViewport.addEventListener('mousedown', function(e) {
        isDraggingCrop = true;
        cropStartX = e.clientX - cropPanX;
        cropStartY = e.clientY - cropPanY;
        e.preventDefault();
    });

    window.addEventListener('mousemove', function(e) {
        if (!isDraggingCrop) return;
        cropPanX = e.clientX - cropStartX;
        cropPanY = e.clientY - cropStartY;
        updateCropTransform();
    });

    window.addEventListener('mouseup', function() {
        isDraggingCrop = false;
    });

    // Touch drag (mobile / tablet touchscreen)
    cropViewport.addEventListener('touchstart', function(e) {
        if (e.touches && e.touches[0]) {
            isDraggingCrop = true;
            cropStartX = e.touches[0].clientX - cropPanX;
            cropStartY = e.touches[0].clientY - cropPanY;
        }
    }, { passive: true });

    window.addEventListener('touchmove', function(e) {
        if (!isDraggingCrop || !e.touches || !e.touches[0]) return;
        cropPanX = e.touches[0].clientX - cropStartX;
        cropPanY = e.touches[0].clientY - cropStartY;
        updateCropTransform();
    }, { passive: true });

    window.addEventListener('touchend', function() {
        isDraggingCrop = false;
    });

    // Scroll wheel zoom
    cropViewport.addEventListener('wheel', function(e) {
        e.preventDefault();
        const step = 0.1;
        if (e.deltaY < 0) {
            cropCurrentZoom = Math.min(3, cropCurrentZoom + step);
        } else {
            cropCurrentZoom = Math.max(1, cropCurrentZoom - step);
        }
        const slider = document.getElementById('crop-zoom-range');
        if (slider) slider.value = cropCurrentZoom;
        const label = document.getElementById('crop-zoom-label');
        if (label) label.innerText = cropCurrentZoom.toFixed(1) + 'x';
        updateCropTransform();
    }, { passive: false });
}

function closeAvatarCropModal() {
    const modal = document.getElementById('avatar-crop-modal');
    if (modal) modal.classList.add('hidden');
    const fileInput = document.getElementById('avatar_file');
    if (fileInput) fileInput.value = '';
}

function confirmAvatarCrop() {
    if (!rawCropImageObj) return;

    const canvas = document.createElement('canvas');
    canvas.width = 400;
    canvas.height = 400;
    const ctx = canvas.getContext('2d');

    // Pastikan koordinat canvas 100% persis dengan viewport 280x280
    const scaleToCanvas = 400 / vpSize;
    const curW = baseW * cropCurrentZoom;
    const curH = baseH * cropCurrentZoom;

    // Posisi sudut kiri atas gambar di viewport:
    const imgLeft = (vpSize / 2) + cropPanX - (curW / 2);
    const imgTop = (vpSize / 2) + cropPanY - (curH / 2);

    // Skala ke kanvas 400x400:
    const drawX = imgLeft * scaleToCanvas;
    const drawY = imgTop * scaleToCanvas;
    const drawW = curW * scaleToCanvas;
    const drawH = curH * scaleToCanvas;

    ctx.drawImage(rawCropImageObj, drawX, drawY, drawW, drawH);

    const croppedBase64 = canvas.toDataURL('image/png', 0.95);

    document.getElementById('avatar_cropped').value = croppedBase64;

    const previewImg = document.getElementById('avatar-preview');
    const fallback = document.getElementById('avatar-preview-fallback');
    if (previewImg) {
        previewImg.src = croppedBase64;
        previewImg.classList.remove('hidden');
    }
    if (fallback) {
        fallback.classList.add('hidden');
    }

    closeAvatarCropModal();

    if (window.showSweetAlert) {
        showSweetAlert('Foto Terpilih!', 'Foto profil siap disimpan. Klik Simpan Perubahan untuk memperbarui akun.', 'success');
    }
}
</script>
@endsection
