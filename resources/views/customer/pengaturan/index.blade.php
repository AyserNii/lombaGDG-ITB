@extends('layouts.app')

@section('title', 'Edit Profil & Pengaturan Akun')

@section('content')
@php
    $rawName = trim($user['name'] ?? 'User');
    $nameParts = preg_split('/\s+/', $rawName);
    $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
    $radius = (int) ($user['matching_radius'] ?? 800);
    $currentGender = $user['gender'] ?? 'prefer_not_to_say';
@endphp

<div class="mx-auto max-w-3xl space-y-6 pb-20">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#4154e8]/10 px-3 py-1 text-xs font-bold text-[#4154e8]">
                    @include('partials.icons', ['name' => 'settings', 'class' => 'h-3.5 w-3.5'])
                    Pusat Pengaturan
                </span>
                <span class="text-xs font-semibold text-slate-400">·</span>
                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Akun Warga Aktif
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1e293b] tracking-tight">Edit Profil & Preferensi</h1>
            <p class="text-xs md:text-sm text-[#7e8ba0]">Kelola tampilan akun, bio Instagram, foto profil dengan crop, dan titik temu gotong royong.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('profile') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                Lihat Profil &rarr;
            </a>
            <button type="submit" form="edit-profile-form" class="flex items-center gap-2 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-6 py-2.5 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 transition active:scale-[0.98]">
                @include('partials.icons', ['name' => 'check', 'class' => 'h-4 w-4'])
                Simpan
            </button>
        </div>
    </div>

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

    <form id="edit-profile-form" method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Hidden input for base64 cropped avatar -->
        <input type="hidden" name="avatar_cropped" id="avatar_cropped">

        <!-- CARD 1: FOTO PROFIL & CHANGE PHOTO (DENGAN CROP ALTA INSTAGRAM/WHATSAPP) -->
        <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        @if (!empty($user['avatar']))
                            <img id="avatar-preview" src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}"
                                 class="h-20 w-20 rounded-full object-cover ring-4 ring-slate-100 shadow-sm">
                        @else
                            <div id="avatar-preview-fallback" class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] text-2xl font-black shadow-sm ring-4 ring-slate-100 select-none">
                                {{ $userInitials }}
                            </div>
                            <img id="avatar-preview" src="" alt="Avatar" class="h-20 w-20 rounded-full object-cover ring-4 ring-slate-100 shadow-sm hidden">
                        @endif
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-[#1e293b] leading-tight">
                            {{ $user['username'] ?? 'aysernii' }}
                        </h3>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">{{ $user['name'] }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pilih foto baru dan sesuaikan dengan crop lingkaran</p>
                    </div>
                </div>

                <div>
                    <!-- Hidden real file input -->
                    <input type="file" id="avatar_file" accept="image/*" class="hidden" onchange="initAvatarCrop(this)">
                    <button type="button" onclick="document.getElementById('avatar_file').click()"
                            class="rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-5 py-2.5 text-xs font-extrabold text-white shadow-sm transition active:scale-95 flex items-center gap-2">
                        @include('partials.icons', ['name' => 'camera', 'class' => 'h-4 w-4'])
                        Change photo
                    </button>
                </div>
            </div>
        </div>

        <!-- CARD 2: NAMA & USERNAME -->
        <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user['name']) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Username (Instagram Handle)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400 font-bold text-xs">@</span>
                        <input type="text" name="username" value="{{ old('username', $user['username'] ?? 'aysernii') }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-8 pr-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: WEBSITE -->
        <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 space-y-2">
            <label class="block text-xs font-bold text-slate-700">Website</label>
            <input type="url" name="website" value="{{ old('website', $user['website'] ?? '') }}"
                   placeholder="https://yourwebsite.com atau tautan medsos..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition">
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Tautan situs web akan tampil di profil publik kamu agar tetangga dan koneksi gotong royong bisa melihat tautanmu.
            </p>
        </div>

        <!-- CARD 4: BIO -->
        <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700">Bio</label>
                <span class="text-[10px] text-slate-400 font-semibold" id="bio-char-count">0 / 300</span>
            </div>
            <textarea name="bio" rows="3" maxlength="300" oninput="document.getElementById('bio-char-count').innerText = this.value.length + ' / 300'"
                      placeholder="Tuliskan bio singkat, hobi, atau sesi favorit gotong royongmu..."
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition resize-none">{{ old('bio', $user['bio'] ?? '') }}</textarea>
        </div>

        <!-- CARD 5: GENDER (PILIHAN DENGAN PREFER NOT TO SAY) -->
        <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Gender</label>
                <p class="text-[11px] text-slate-400">Pilihan ini bersifat pribadi dan kamu bebas memilih untuk memberitahu atau tidak (Prefer not to say).</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <label class="relative flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'male' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                    <div class="flex items-center gap-3">
                        @include('partials.icons', ['name' => 'user', 'class' => 'h-5 w-5 text-slate-600'])
                        <span class="text-xs font-bold">Pria (Male)</span>
                    </div>
                    <input type="radio" name="gender" value="male" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'male' ? 'checked' : '' }}>
                </label>

                <label class="relative flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'female' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                    <div class="flex items-center gap-3">
                        @include('partials.icons', ['name' => 'user', 'class' => 'h-5 w-5 text-slate-600'])
                        <span class="text-xs font-bold">Wanita (Female)</span>
                    </div>
                    <input type="radio" name="gender" value="female" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'female' ? 'checked' : '' }}>
                </label>

                <label class="relative flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'custom' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                    <div class="flex items-center gap-3">
                        @include('partials.icons', ['name' => 'sparkles', 'class' => 'h-5 w-5 text-slate-600'])
                        <span class="text-xs font-bold">Custom</span>
                    </div>
                    <input type="radio" name="gender" value="custom" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'custom' ? 'checked' : '' }}>
                </label>

                <!-- Prefer not to say -->
                <label class="relative flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition hover:bg-slate-50 {{ $currentGender === 'prefer_not_to_say' ? 'border-[#4154e8] bg-blue-50/40 text-[#4154e8]' : 'border-slate-200 text-slate-700' }}">
                    <div class="flex items-center gap-3">
                        @include('partials.icons', ['name' => 'lock', 'class' => 'h-5 w-5 text-slate-600'])
                        <div>
                            <span class="text-xs font-bold">Prefer not to say</span>
                            <span class="block text-[10px] text-slate-400">Tidak ingin memberitahu</span>
                        </div>
                    </div>
                    <input type="radio" name="gender" value="prefer_not_to_say" class="text-[#4154e8] focus:ring-[#4154e8]" {{ $currentGender === 'prefer_not_to_say' ? 'checked' : '' }}>
                </label>
            </div>
            <p class="text-[11px] text-slate-400 italic">Informasi gender tidak akan ditampilkan ke publik.</p>
        </div>

        <!-- CARD 6: PREFERENSI CLUSTER & TITIK TEMU -->
        <div class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)] border border-slate-100 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Preferensi Cluster & Pengantaran</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi Cluster</label>
                    <input type="text" name="location" value="{{ old('location', $user['location'] ?? 'Beji, Depok') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user['phone'] ?? '') }}"
                           placeholder="0812xxxxxxx"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Titik Temu / Drop Point Utama</label>
                <input type="text" name="pickup_point" value="{{ old('pickup_point', $user['pickup_point'] ?? 'Pos Satpam Cluster Beji Blok B-12') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] focus:bg-white focus:ring-2 focus:ring-[#4154e8]/20 transition">
            </div>
        </div>

        <!-- Submit Button (Solid Modern, No Gradient) -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('profile') }}" class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-5 py-2.5 text-xs font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-8 py-2.5 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/25 transition active:scale-[0.99]">
                @include('partials.icons', ['name' => 'check', 'class' => 'h-4 w-4'])
                Simpan Perubahan
            </button>
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
        <div class="relative mx-auto w-[280px] h-[280px] overflow-hidden rounded-2xl bg-black flex items-center justify-center cursor-grab active:cursor-grabbing select-none" id="crop-viewport">
            <!-- Source Image that scales and pans -->
            <img id="crop-source-img" src="" alt="Crop Source" class="pointer-events-none max-w-none origin-center transition-transform duration-75">

            <!-- Circular Mask Cutout -->
            <div class="absolute inset-0 pointer-events-none ring-[140px] ring-black/70 rounded-full"></div>
            <!-- Circle Ring Guide -->
            <div class="absolute inset-0 pointer-events-none border-2 border-white/80 rounded-full"></div>
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
let cropCurrentZoom = 1;
let cropPanX = 0;
let cropPanY = 0;
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
            // Fit initial size to viewport
            const vpSize = 280;
            const aspect = rawCropImageObj.width / rawCropImageObj.height;
            if (aspect > 1) {
                img.style.height = vpSize + 'px';
                img.style.width = (vpSize * aspect) + 'px';
            } else {
                img.style.width = vpSize + 'px';
                img.style.height = (vpSize / aspect) + 'px';
            }

            cropCurrentZoom = 1;
            cropPanX = 0;
            cropPanY = 0;
            document.getElementById('crop-zoom-range').value = 1;
            document.getElementById('crop-zoom-label').innerText = '1.0x';
            renderCropTransform();

            modal.classList.remove('hidden');
        };
        rawCropImageObj.src = e.target.result;
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function closeAvatarCropModal() {
    document.getElementById('avatar-crop-modal').classList.add('hidden');
    document.getElementById('avatar_file').value = '';
}

function applyAvatarZoom(val) {
    cropCurrentZoom = parseFloat(val);
    document.getElementById('crop-zoom-label').innerText = cropCurrentZoom.toFixed(1) + 'x';
    renderCropTransform();
}

function renderCropTransform() {
    const img = document.getElementById('crop-source-img');
    if (!img) return;
    img.style.transform = `translate(${cropPanX}px, ${cropPanY}px) scale(${cropCurrentZoom})`;
}

// Drag & Pan inside crop viewport
const cropVp = document.getElementById('crop-viewport');
cropVp.addEventListener('mousedown', function(e) {
    isDraggingCrop = true;
    cropStartX = e.clientX - cropPanX;
    cropStartY = e.clientY - cropPanY;
});

window.addEventListener('mousemove', function(e) {
    if (!isDraggingCrop) return;
    cropPanX = e.clientX - cropStartX;
    cropPanY = e.clientY - cropStartY;
    renderCropTransform();
});

window.addEventListener('mouseup', function() {
    isDraggingCrop = false;
});

// Touch support for mobile crop
cropVp.addEventListener('touchstart', function(e) {
    if (e.touches.length === 1) {
        isDraggingCrop = true;
        cropStartX = e.touches[0].clientX - cropPanX;
        cropStartY = e.touches[0].clientY - cropPanY;
    }
});

window.addEventListener('touchmove', function(e) {
    if (!isDraggingCrop || e.touches.length !== 1) return;
    cropPanX = e.touches[0].clientX - cropStartX;
    cropPanY = e.touches[0].clientY - cropStartY;
    renderCropTransform();
});

window.addEventListener('touchend', function() {
    isDraggingCrop = false;
});

function confirmAvatarCrop() {
    if (!rawCropImageObj) return;

    // Render cropped result to canvas (400x400)
    const canvas = document.createElement('canvas');
    canvas.width = 400;
    canvas.height = 400;
    const ctx = canvas.getContext('2d');

    const vpSize = 280;
    const scaleFactor = 400 / vpSize;

    const img = document.getElementById('crop-source-img');
    const imgW = parseFloat(img.style.width || vpSize);
    const imgH = parseFloat(img.style.height || vpSize);

    ctx.save();
    // Center canvas
    ctx.translate(200, 200);
    ctx.scale(cropCurrentZoom, cropCurrentZoom);
    ctx.translate(cropPanX * scaleFactor, cropPanY * scaleFactor);

    ctx.drawImage(
        rawCropImageObj,
        -(imgW * scaleFactor) / 2,
        -(imgH * scaleFactor) / 2,
        imgW * scaleFactor,
        imgH * scaleFactor
    );
    ctx.restore();

    const croppedBase64 = canvas.toDataURL('image/png', 0.95);

    // Set into hidden form input
    document.getElementById('avatar_cropped').value = croppedBase64;

    // Update preview immediately on page
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
    showSuccessAlert('Foto Dipotong', 'Foto profil telah disesuaikan. Jangan lupa klik "Simpan Perubahan" untuk menyimpan permanen.');
}
</script>
@endsection
