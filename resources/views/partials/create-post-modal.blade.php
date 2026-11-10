{{-- ========================================================================= --}}
{{-- MODAL TAMBAH POSTINGAN BARU (MODERN STUDIO STYLE DENGAN CROP COVER & TAG #)    --}}
{{-- ========================================================================= --}}
<div id="create-post-modal"
     class="fixed inset-0 z-[65] items-center justify-center p-3 sm:p-5 bg-slate-950/80 backdrop-blur-md transition-opacity duration-200"
     style="display: none;"
     onclick="if (event.target === this) closeCreatePostModal();">

    <div id="create-post-card"
         class="relative flex flex-col w-full max-w-4xl bg-white rounded-[28px] shadow-2xl border border-slate-100 overflow-hidden transform scale-95 opacity-0 transition-all duration-200 max-h-[94vh]">

        <!-- MODAL HEADER (3-Column balanced layout: Left Back, Center Title, Right Submit+Close) -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 shrink-0 bg-white z-20">
            <!-- Left: Back Button -->
            <div class="w-28 flex items-center">
                <button type="button" id="cp-back-btn" onclick="resetCreatePostStep()"
                        class="hidden text-xs font-black text-slate-700 hover:text-[#4154e8] transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Ganti Foto</span>
                </button>
            </div>

            <!-- Center: Title -->
            <h3 class="text-sm font-black text-slate-900 text-center flex-1 tracking-tight">Buat postingan baru</h3>

            <!-- Right: Action Buttons -->
            <div class="w-28 flex items-center justify-end gap-2">
                <button type="button" id="cp-submit-btn" onclick="submitCreatePost()"
                        class="hidden rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-4 py-1.5 text-xs font-black text-white transition shadow-sm active:scale-95 cursor-pointer">
                    Bagikan
                </button>
                <button type="button" onclick="closeCreatePostModal()"
                        class="p-1.5 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer" title="Tutup">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- MODAL BODY -->
        <div class="flex-1 overflow-y-auto">

            <!-- STEP 1: INITIAL UPLOAD STATE -->
            <div id="cp-step-upload" class="flex flex-col items-center justify-center p-12 sm:p-20 text-center space-y-5">
                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 text-[#4154e8] ring-8 ring-blue-50/50 shadow-inner">
                    <svg class="h-10 w-10 text-[#4154e8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="4" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                </div>
                <div class="max-w-md">
                    <h4 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Pilih foto postingan gotong royong</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Bagikan momen sesi makan, titip belanja bersama, rute kirim hemat, atau aktivitas seru cluster warga.
                    </p>
                </div>

                <!-- Hidden Input -->
                <input type="file" id="cp-file-input" accept="image/*" multiple class="hidden" onchange="handlePostFilesSelect(this.files)">

                <!-- Initial Upload Button -->
                <button type="button" onclick="document.getElementById('cp-file-input').click()"
                        class="rounded-2xl bg-[#4154e8] hover:bg-[#3243d4] px-7 py-3 text-xs font-extrabold text-white shadow-lg shadow-[#4154e8]/25 transition active:scale-95 cursor-pointer">
                    Pilih dari Komputer
                </button>
            </div>

            <!-- STEP 2: EDIT, CROP & CAPTION STATE (2-Column Studio Layout) -->
            <div id="cp-step-edit" class="hidden flex flex-col lg:flex-row min-h-[500px] lg:h-[580px]">

                <!-- Left Column: Studio Canvas & Controls -->
                <div class="w-full lg:w-3/5 bg-[#0a0d14] flex flex-col justify-between select-none relative overflow-hidden h-[440px] sm:h-[500px] lg:h-full border-b lg:border-b-0 lg:border-r border-slate-800">
                    
                    <!-- Centered Canvas Stage (Photo Crop Box) -->
                    <div id="cp-stage" class="flex-1 flex items-center justify-center p-4 sm:p-6 overflow-hidden relative">
                        
                        <!-- Canvas Viewport Frame (Smooth Aspect Ratio & Drag) -->
                        <div id="cp-canvas-wrapper"
                             class="relative overflow-hidden rounded-2xl bg-black cursor-grab active:cursor-grabbing shadow-[0_12px_40px_rgba(0,0,0,0.6)] flex items-center justify-center transition-all duration-300 border border-white/10"
                             style="width: 100%; max-width: 440px; aspect-ratio: 1/1;">
                            
                            <!-- Preview Image (Uses object-cover to FILL the frame properly without black side bars) -->
                            <img id="cp-preview-img" src="" alt="Preview"
                                 class="w-full h-full object-cover pointer-events-none select-none origin-center"
                                 style="transform: scale(1) translate(0px, 0px);">

                            <!-- Rule of Thirds Grid Overlay -->
                            <div id="cp-grid-overlay" class="absolute inset-0 pointer-events-none grid grid-cols-3 grid-rows-3 opacity-0 transition-opacity duration-150 border border-white/30">
                                <div class="border-r border-b border-white/30"></div>
                                <div class="border-r border-b border-white/30"></div>
                                <div class="border-b border-white/30"></div>
                                <div class="border-r border-b border-white/30"></div>
                                <div class="border-r border-b border-white/30"></div>
                                <div class="border-b border-white/30"></div>
                                <div class="border-r border-white/30"></div>
                                <div class="border-r border-white/30"></div>
                                <div></div>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Studio Toolbar: Multi-Photo Carousel & Aspect/Zoom Controls -->
                    <div class="bg-[#0f1422]/95 backdrop-blur-md p-3.5 space-y-3 border-t border-slate-800/80 shrink-0 z-10">
                        
                        <!-- Multi-Photo Thumbnails Strip & "+ Tambah Foto" Button -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                            <div id="cp-thumbs-container" class="flex items-center gap-2">
                                <!-- Thumbnails injected dynamically -->
                            </div>

                            <!-- "Tambah Foto" Button -->
                            <button type="button" onclick="document.getElementById('cp-file-input').click()"
                                    class="shrink-0 flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 px-3.5 py-2 text-xs font-bold text-slate-200 border border-slate-700/80 transition cursor-pointer"
                                    title="Tambah foto lain untuk postingan ini">
                                <svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Foto</span>
                            </button>
                        </div>

                        <!-- Crop & Scale Adjustment Bar -->
                        <div class="flex items-center justify-between gap-3 text-white text-xs">
                            <!-- Aspect Ratio Buttons (1:1 Square, 4:5 Portrait, 16:9 Landscape) -->
                            <div class="flex items-center gap-1 bg-slate-800/90 p-1 rounded-xl border border-slate-700/50">
                                <button type="button" onclick="setPostAspectRatio('1:1')" id="cp-ar-1"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-[#4154e8] text-white transition cursor-pointer">1:1</button>
                                <button type="button" onclick="setPostAspectRatio('4:5')" id="cp-ar-45"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-black text-slate-400 hover:text-white transition cursor-pointer">4:5</button>
                                <button type="button" onclick="setPostAspectRatio('16:9')" id="cp-ar-16"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-black text-slate-400 hover:text-white transition cursor-pointer">16:9</button>
                            </div>

                            <!-- Zoom Slider (SVG Icon Magnifier — Tanpa Emoticon) -->
                            <div class="flex items-center gap-2 bg-slate-800/90 px-3.5 py-1.5 rounded-xl border border-slate-700/50">
                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="11" cy="11" r="7"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                <input type="range" id="cp-zoom-slider" min="1" max="2.5" step="0.05" value="1"
                                       oninput="updatePostZoom(this.value)"
                                       class="w-20 sm:w-28 accent-[#4154e8] h-1.5 bg-slate-700 rounded-lg cursor-pointer">
                                <span id="cp-zoom-val" class="text-[11px] font-bold text-slate-300 w-8 text-right">1.0x</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Caption & Hashtag Form -->
                <div class="w-full lg:w-2/5 p-5 sm:p-6 flex flex-col justify-between space-y-5 bg-white overflow-y-auto lg:h-full">
                    <div class="space-y-4">
                        
                        <!-- Author Header -->
                        <div class="flex items-center gap-3">
                            @php
                                $cUser = Auth::user();
                                $cName = $cUser ? $cUser->name : 'User';
                                $cInit = strtoupper(substr($cName, 0, 2));
                            @endphp
                            @if ($cUser && $cUser->avatar)
                                <img src="{{ $cUser->avatar }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100 shadow-sm">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] font-black text-xs ring-2 ring-slate-100">
                                    {{ $cInit }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-900 leading-tight truncate">{{ $cUser->username ?? $cName }}</p>
                                <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">{{ $cUser->location ?? 'Cluster Beji, Depok' }}</p>
                            </div>
                        </div>

                        <!-- Caption Textarea -->
                        <div class="space-y-1">
                            <textarea id="cp-caption-input" rows="4" maxlength="500"
                                      placeholder="Tulis keterangan momen gotong royong, cerita sesi makan, titip belanja..."
                                      class="w-full text-xs text-slate-800 placeholder:text-slate-400 outline-none resize-none border-0 focus:ring-0 p-0 leading-relaxed"></textarea>
                            <div class="flex justify-between items-center text-[10px] text-slate-400 pt-2 border-t border-slate-100 font-semibold">
                                <span>Maks. 500 karakter</span>
                                <span id="cp-char-count">0 / 500</span>
                            </div>
                        </div>

                        <!-- HASHTAG (#) SECTION (Pengganti Kategori Statis) -->
                        <div class="space-y-2.5 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-black text-slate-800 tracking-tight flex items-center gap-1.5">
                                    <span class="text-[#4154e8]">#</span>
                                    <span>Hashtag Postingan</span>
                                </label>
                                <span class="text-[10px] text-slate-400 font-semibold">Bebas buat tag apapun</span>
                            </div>

                            <!-- Active Tag Chips Container -->
                            <div id="cp-tags-container" class="flex flex-wrap gap-1.5 min-h-[32px] p-2 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <!-- Tag badges dynamically inserted -->
                            </div>

                            <!-- Custom Tag Input Box -->
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-xs font-black pointer-events-none">#</span>
                                    <input type="text" id="cp-tag-input" placeholder="Ketik tag lalu tekan Enter (misal: fyp, kuliner)..."
                                           class="w-full rounded-xl bg-white border border-slate-200/80 pl-7 pr-3 py-2 text-xs text-slate-800 placeholder:text-slate-400 outline-none focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15 transition font-semibold"
                                           onkeydown="handleTagInputKey(event)">
                                </div>
                                <button type="button" onclick="addCustomTagFromInput()"
                                        class="shrink-0 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-3.5 py-2 text-xs font-extrabold text-white transition active:scale-95 shadow-sm cursor-pointer">
                                    + Tag
                                </button>
                            </div>

                            <!-- Quick Suggestion Hashtag Pills -->
                            <div class="space-y-1 pt-1">
                                <span class="text-[10px] text-slate-400 font-bold block">Tag Populer Cepat:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" onclick="quickAddTag('fyp')" class="rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-[#4154e8] px-2 py-1 text-[10px] font-bold text-slate-600 transition cursor-pointer">#fyp</button>
                                    <button type="button" onclick="quickAddTag('barengmakan')" class="rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-[#4154e8] px-2 py-1 text-[10px] font-bold text-slate-600 transition cursor-pointer">#barengmakan</button>
                                    <button type="button" onclick="quickAddTag('titipbelanja')" class="rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-[#4154e8] px-2 py-1 text-[10px] font-bold text-slate-600 transition cursor-pointer">#titipbelanja</button>
                                    <button type="button" onclick="quickAddTag('barengkirim')" class="rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-[#4154e8] px-2 py-1 text-[10px] font-bold text-slate-600 transition cursor-pointer">#barengkirim</button>
                                    <button type="button" onclick="quickAddTag('gotongroyong')" class="rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-[#4154e8] px-2 py-1 text-[10px] font-bold text-slate-600 transition cursor-pointer">#gotongroyong</button>
                                    <button type="button" onclick="quickAddTag('warga')" class="rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-[#4154e8] px-2 py-1 text-[10px] font-bold text-slate-600 transition cursor-pointer">#warga</button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Submit Button for Mobile View -->
                    <div class="pt-2 lg:hidden">
                        <button type="button" onclick="submitCreatePost()"
                                class="w-full rounded-2xl bg-[#4154e8] hover:bg-[#3243d4] py-3 text-xs font-black text-white transition shadow-md shadow-[#4154e8]/20 cursor-pointer">
                            Bagikan Postingan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// State for Create Post Modal
let cpSelectedPhotos = []; // array of { file, dataUrl, zoom: 1, panX: 0, panY: 0 }
let cpActiveIndex = 0;
let cpCurrentAspectRatio = '1:1';
let cpTags = ['fyp', 'gotongroyong']; // Default start tags

function openCreatePostModal() {
    const modal = document.getElementById('create-post-modal');
    const card = document.getElementById('create-post-card');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    renderTagChips();
    initPanGesture();
    requestAnimationFrame(() => {
        if (card) {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }
    });
}

function closeCreatePostModal() {
    const modal = document.getElementById('create-post-modal');
    const card = document.getElementById('create-post-card');
    if (card) {
        card.classList.add('scale-95', 'opacity-0');
        card.classList.remove('scale-100', 'opacity-100');
    }
    setTimeout(() => {
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        document.body.style.overflow = '';
        resetCreatePostStep();
    }, 200);
}

function handlePostFilesSelect(files) {
    if (!files || files.length === 0) return;

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const rawImg = new Image();
            rawImg.onload = function() {
                cpSelectedPhotos.push({
                    file: file,
                    dataUrl: e.target.result,
                    naturalWidth: rawImg.naturalWidth,
                    naturalHeight: rawImg.naturalHeight,
                    zoom: 1,
                    panX: 0,
                    panY: 0
                });
                if (cpSelectedPhotos.length === 1) {
                    cpActiveIndex = 0;
                    showEditPostStep();
                }
                renderPostThumbs();
            };
            rawImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function showEditPostStep() {
    document.getElementById('cp-step-upload').classList.add('hidden');
    document.getElementById('cp-step-edit').classList.remove('hidden');
    document.getElementById('cp-back-btn').classList.remove('hidden');
    document.getElementById('cp-submit-btn').classList.remove('hidden');

    setPostAspectRatio(cpCurrentAspectRatio);
    displayActivePhoto();
    renderTagChips();
}

function resetCreatePostStep() {
    cpSelectedPhotos = [];
    cpActiveIndex = 0;
    const fileInput = document.getElementById('cp-file-input');
    if (fileInput) fileInput.value = '';
    document.getElementById('cp-step-upload').classList.remove('hidden');
    document.getElementById('cp-step-edit').classList.add('hidden');
    document.getElementById('cp-back-btn').classList.add('hidden');
    document.getElementById('cp-submit-btn').classList.add('hidden');
    const cap = document.getElementById('cp-caption-input');
    if (cap) cap.value = '';
    const charEl = document.getElementById('cp-char-count');
    if (charEl) charEl.innerText = '0 / 500';
}

function getViewportDimensions() {
    const wrapper = document.getElementById('cp-canvas-wrapper');
    if (!wrapper) return { vpW: 420, vpH: 420 };
    return {
        vpW: wrapper.clientWidth || 420,
        vpH: wrapper.clientHeight || 420
    };
}

// Clamp posisi pan X & Y agar foto TIDAK PERNAH keluar dari batas bingkai
function clampPan(photo) {
    if (!photo) return;
    const { vpW, vpH } = getViewportDimensions();
    const nw = photo.naturalWidth || 800;
    const nh = photo.naturalHeight || 800;

    // Hitung Cover Fit: dimensi foto minimal harus sama/menutup penuh viewport
    const scaleCover = Math.max(vpW / nw, vpH / nh);
    const baseW = nw * scaleCover;
    const baseH = nh * scaleCover;

    const zoom = Math.max(1, Math.min(3, photo.zoom || 1));
    const curW = baseW * zoom;
    const curH = baseH * zoom;

    // Maksimum geser dihitung dari selisih ukuran foto terhadap ukuran viewport
    const maxPanX = Math.max(0, (curW - vpW) / 2);
    const maxPanY = Math.max(0, (curH - vpH) / 2);

    photo.panX = Math.max(-maxPanX, Math.min(maxPanX, photo.panX || 0));
    photo.panY = Math.max(-maxPanY, Math.min(maxPanY, photo.panY || 0));
    photo.baseW = baseW;
    photo.baseH = baseH;
}

function displayActivePhoto() {
    if (!cpSelectedPhotos[cpActiveIndex]) return;
    const current = cpSelectedPhotos[cpActiveIndex];
    const img = document.getElementById('cp-preview-img');
    img.src = current.dataUrl;

    const slider = document.getElementById('cp-zoom-slider');
    if (slider) slider.value = current.zoom || 1;
    const valLabel = document.getElementById('cp-zoom-val');
    if (valLabel) valLabel.innerText = (current.zoom || 1).toFixed(1) + 'x';

    applyPhotoTransform();
}

function renderPostThumbs() {
    const container = document.getElementById('cp-thumbs-container');
    container.innerHTML = cpSelectedPhotos.map((item, idx) => `
        <div class="relative shrink-0 h-11 w-11 rounded-xl overflow-hidden border-2 cursor-pointer transition ${idx === cpActiveIndex ? 'border-[#4154e8] scale-105 shadow-md' : 'border-slate-700 opacity-70 hover:opacity-100'}"
             onclick="switchActivePhoto(${idx})">
            <img src="${item.dataUrl}" class="h-full w-full object-cover">
            ${cpSelectedPhotos.length > 1 ? `
                <button type="button" onclick="event.stopPropagation(); removePhoto(${idx})"
                        class="absolute top-0.5 right-0.5 h-3.5 w-3.5 flex items-center justify-center rounded-full bg-black/80 text-white text-[8px] hover:bg-rose-600 transition"
                        title="Hapus foto ini">
                    ✕
                </button>
            ` : ''}
        </div>
    `).join('');
}

function switchActivePhoto(idx) {
    cpActiveIndex = idx;
    displayActivePhoto();
    renderPostThumbs();
}

function removePhoto(idx) {
    cpSelectedPhotos.splice(idx, 1);
    if (cpSelectedPhotos.length === 0) {
        resetCreatePostStep();
        return;
    }
    if (cpActiveIndex >= cpSelectedPhotos.length) {
        cpActiveIndex = cpSelectedPhotos.length - 1;
    }
    displayActivePhoto();
    renderPostThumbs();
}

function updatePostZoom(val) {
    if (!cpSelectedPhotos[cpActiveIndex]) return;
    cpSelectedPhotos[cpActiveIndex].zoom = Math.max(1, Math.min(3, parseFloat(val) || 1));
    document.getElementById('cp-zoom-val').innerText = cpSelectedPhotos[cpActiveIndex].zoom.toFixed(1) + 'x';
    applyPhotoTransform();
}

function applyPhotoTransform() {
    const current = cpSelectedPhotos[cpActiveIndex];
    if (!current) return;
    const img = document.getElementById('cp-preview-img');
    if (!img) return;

    clampPan(current);

    const zoom = current.zoom || 1;
    img.style.width = Math.round(current.baseW) + 'px';
    img.style.height = Math.round(current.baseH) + 'px';
    img.style.maxWidth = 'none';
    img.style.maxHeight = 'none';
    img.style.transformOrigin = 'center center';
    img.style.transform = `translate(${current.panX}px, ${current.panY}px) scale(${zoom})`;
}

// ─── ASPECT RATIO SWITCHER (Clean Centered Stage & Cover Crop) ───────────────────
function setPostAspectRatio(ar) {
    cpCurrentAspectRatio = ar;
    const wrapper = document.getElementById('cp-canvas-wrapper');

    // Reset button styles
    ['cp-ar-1', 'cp-ar-45', 'cp-ar-16'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.className = 'px-2.5 py-1 rounded-lg text-[10px] font-black text-slate-400 hover:text-white transition cursor-pointer';
        }
    });

    if (ar === '1:1') {
        wrapper.style.width = '100%';
        wrapper.style.maxWidth = '420px';
        wrapper.style.height = 'auto';
        wrapper.style.maxHeight = '420px';
        wrapper.style.aspectRatio = '1 / 1';
        document.getElementById('cp-ar-1').className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-[#4154e8] text-white transition cursor-pointer';
    } else if (ar === '4:5') {
        wrapper.style.width = 'auto';
        wrapper.style.maxWidth = '352px';
        wrapper.style.height = '100%';
        wrapper.style.maxHeight = '440px';
        wrapper.style.aspectRatio = '4 / 5';
        document.getElementById('cp-ar-45').className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-[#4154e8] text-white transition cursor-pointer';
    } else if (ar === '16:9') {
        wrapper.style.width = '100%';
        wrapper.style.maxWidth = '480px';
        wrapper.style.height = 'auto';
        wrapper.style.maxHeight = '270px';
        wrapper.style.aspectRatio = '16 / 9';
        document.getElementById('cp-ar-16').className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-[#4154e8] text-white transition cursor-pointer';
    }

    // Recalibrate cover scale & clamp pan with new aspect ratio dimensions
    setTimeout(() => {
        applyPhotoTransform();
    }, 40);
}

// ─── PAN / DRAG GESTURE FOR PHOTO ADJUSTMENT (DENGAN BATASAN MENGIKAT) ─────────────
let cpIsDragging = false;
let cpStartX = 0;
let cpStartY = 0;

function initPanGesture() {
    const wrapper = document.getElementById('cp-canvas-wrapper');
    const grid = document.getElementById('cp-grid-overlay');
    if (!wrapper || wrapper.dataset.panInitialized) return;
    wrapper.dataset.panInitialized = 'true';

    // Mouse events
    wrapper.addEventListener('mousedown', (e) => {
        if (!cpSelectedPhotos[cpActiveIndex]) return;
        cpIsDragging = true;
        cpStartX = e.clientX;
        cpStartY = e.clientY;
        if (grid) grid.style.opacity = '0.5';
        e.preventDefault();
    });

    window.addEventListener('mousemove', (e) => {
        if (!cpIsDragging || !cpSelectedPhotos[cpActiveIndex]) return;
        const dx = e.clientX - cpStartX;
        const dy = e.clientY - cpStartY;
        cpStartX = e.clientX;
        cpStartY = e.clientY;

        const current = cpSelectedPhotos[cpActiveIndex];
        current.panX = (current.panX || 0) + dx;
        current.panY = (current.panY || 0) + dy;
        applyPhotoTransform();
    });

    window.addEventListener('mouseup', () => {
        if (cpIsDragging) {
            cpIsDragging = false;
            if (grid) grid.style.opacity = '0';
        }
    });

    // Touch events for touchscreen/mobile
    wrapper.addEventListener('touchstart', (e) => {
        if (e.touches.length === 1 && cpSelectedPhotos[cpActiveIndex]) {
            cpIsDragging = true;
            cpStartX = e.touches[0].clientX;
            cpStartY = e.touches[0].clientY;
            if (grid) grid.style.opacity = '0.5';
        }
    }, { passive: true });

    window.addEventListener('touchmove', (e) => {
        if (!cpIsDragging || !cpSelectedPhotos[cpActiveIndex] || e.touches.length !== 1) return;
        const dx = e.touches[0].clientX - cpStartX;
        const dy = e.touches[0].clientY - cpStartY;
        cpStartX = e.touches[0].clientX;
        cpStartY = e.touches[0].clientY;

        const current = cpSelectedPhotos[cpActiveIndex];
        current.panX = (current.panX || 0) + dx;
        current.panY = (current.panY || 0) + dy;
        applyPhotoTransform();
    }, { passive: true });

    window.addEventListener('touchend', () => {
        if (cpIsDragging) {
            cpIsDragging = false;
            if (grid) grid.style.opacity = '0';
        }
    });
}

// ─── HASHTAG (#) LOGIC ──────────────────────────────────────────────────────────
function renderTagChips() {
    const container = document.getElementById('cp-tags-container');
    if (!container) return;

    if (cpTags.length === 0) {
        container.innerHTML = `<span class="text-[11px] text-slate-400 font-medium py-0.5 px-1">Belum ada tag. Ketik tag di bawah untuk menambahkan.</span>`;
        return;
    }

    container.innerHTML = cpTags.map(tag => `
        <span class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50/90 border border-blue-200/70 px-2.5 py-1 text-xs font-bold text-[#4154e8] transition animate-scale-up">
            <span>#${tag}</span>
            <button type="button" onclick="removeTag('${tag}')" class="text-slate-400 hover:text-rose-500 transition cursor-pointer text-xs leading-none">
                &times;
            </button>
        </span>
    `).join('');
}

function handleTagInputKey(e) {
    if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
        e.preventDefault();
        addCustomTagFromInput();
    }
}

function addCustomTagFromInput() {
    const input = document.getElementById('cp-tag-input');
    if (!input) return;
    const raw = input.value.trim().replace(/^#+/, '').replace(/\s+/g, '');
    if (!raw) return;

    if (!cpTags.includes(raw.toLowerCase())) {
        cpTags.push(raw.toLowerCase());
        renderTagChips();
    }
    input.value = '';
}

function quickAddTag(tag) {
    const clean = tag.toLowerCase().replace(/^#+/, '');
    if (!cpTags.includes(clean)) {
        cpTags.push(clean);
        renderTagChips();
    }
}

function removeTag(tag) {
    cpTags = cpTags.filter(t => t !== tag);
    renderTagChips();
}

// Character counter
document.getElementById('cp-caption-input')?.addEventListener('input', function() {
    const charEl = document.getElementById('cp-char-count');
    if (charEl) charEl.innerText = this.value.length + ' / 500';
});

function getCroppedPhotoData(photo, targetAspectRatio) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
            let outW = 1080, outH = 1080;
            if (targetAspectRatio === '4:5') { outW = 1080; outH = 1350; }
            else if (targetAspectRatio === '16:9') { outW = 1280; outH = 720; }

            const canvas = document.createElement('canvas');
            canvas.width = outW;
            canvas.height = outH;
            const ctx = canvas.getContext('2d');

            const nw = photo.naturalWidth || img.naturalWidth || 800;
            const nh = photo.naturalHeight || img.naturalHeight || 800;
            const scaleCover = Math.max(outW / nw, outH / nh);
            const baseW = nw * scaleCover;
            const baseH = nh * scaleCover;

            const zoom = photo.zoom || 1;
            const curW = baseW * zoom;
            const curH = baseH * zoom;

            const wrapper = document.getElementById('cp-canvas-wrapper');
            const dispVpW = (wrapper && wrapper.clientWidth) ? wrapper.clientWidth : 420;
            const ratio = outW / dispVpW;

            const panX = (photo.panX || 0) * ratio;
            const panY = (photo.panY || 0) * ratio;

            const drawX = (outW - curW) / 2 + panX;
            const drawY = (outH - curH) / 2 + panY;

            ctx.drawImage(img, drawX, drawY, curW, curH);
            resolve(canvas.toDataURL('image/jpeg', 0.92));
        };
        img.onerror = () => resolve(photo.dataUrl);
        img.src = photo.dataUrl;
    });
}

// Submit Post
async function submitCreatePost() {
    if (cpSelectedPhotos.length === 0) {
        if (window.showErrorToast) {
            showErrorToast('Foto Diperlukan', 'Harap pilih minimal satu foto untuk dibagikan.');
        }
        return;
    }

    const caption = document.getElementById('cp-caption-input').value;
    const submitBtn = document.getElementById('cp-submit-btn');
    submitBtn.innerText = 'Mengunggah...';
    submitBtn.disabled = true;

    try {
        const croppedPhotos = [];
        for (const p of cpSelectedPhotos) {
            const cropped = await getCroppedPhotoData(p, cpCurrentAspectRatio);
            croppedPhotos.push(cropped);
        }

        // Send via AJAX with custom tags
        fetch('{{ route("posts.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                caption: caption,
                type: cpTags.length > 0 ? ('#' + cpTags[0]) : 'GOTONG ROYONG',
                tags: cpTags,
                photos: croppedPhotos
            })
        })
        .then(r => r.json())
        .then(data => {
            submitBtn.innerText = 'Bagikan';
            submitBtn.disabled = false;
            closeCreatePostModal();

            if (data.success) {
                // Modal notif animasi ceklis modern (SweetAlert style toast)
                if (window.showSuccessToast) {
                    showSuccessToast('Postingan Berhasil Dibagikan!', 'Postingan baru kamu sudah berhasil tampil di linimasa komunitas.');
                }
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            } else {
                if (window.showErrorToast) {
                    showErrorToast('Gagal Membagikan', data.message || 'Terjadi kesalahan sistem.');
                }
            }
        })
        .catch(err => {
            console.error(err);
            submitBtn.innerText = 'Bagikan';
            submitBtn.disabled = false;
            if (window.showErrorToast) {
                showErrorToast('Gagal', 'Terjadi kesalahan koneksi.');
            }
        });
    } catch (e) {
        console.error(e);
        submitBtn.innerText = 'Bagikan';
        submitBtn.disabled = false;
    }
}
</script>
