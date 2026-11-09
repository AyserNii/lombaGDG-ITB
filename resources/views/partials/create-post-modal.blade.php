{{-- Modern Create New Post Modal (Instagram Style) --}}
<div id="create-post-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md transition-opacity duration-200">
    <div id="create-post-card" class="relative flex flex-col w-full max-w-2xl bg-white rounded-[28px] shadow-2xl border border-slate-100 overflow-hidden transform scale-95 opacity-0 transition-all duration-200 max-h-[90vh]">

        <!-- MODAL HEADER -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
            <button type="button" id="cp-back-btn" onclick="resetCreatePostStep()" class="hidden text-xs font-bold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
                &larr; Ganti Foto
            </button>
            <div id="cp-header-placeholder" class="w-10"></div>

            <h3 class="text-sm font-black text-slate-900 tracking-tight">Buat postingan baru</h3>

            <div class="flex items-center gap-2">
                <!-- Submit Button: Only active when photos chosen -->
                <button type="button" id="cp-submit-btn" onclick="submitCreatePost()"
                        class="hidden rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-4 py-1.5 text-xs font-extrabold text-white transition shadow-sm active:scale-95">
                    Bagikan
                </button>
                <button type="button" onclick="closeCreatePostModal()" class="p-1.5 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition" title="Tutup">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- MODAL BODY -->
        <div class="flex-1 overflow-y-auto">

            <!-- STEP 1: INITIAL UPLOAD STATE (Sebelum pilih foto) -->
            <div id="cp-step-upload" class="flex flex-col items-center justify-center p-12 sm:p-16 text-center space-y-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-[#4154e8] ring-8 ring-slate-50/50">
                    @include('partials.icons', ['name' => 'plus-square', 'class' => 'h-10 w-10'])
                </div>
                <div>
                    <h4 class="text-base font-extrabold text-slate-900">Pilih foto postingan gotong royong</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Bagikan momen sesi makan, titip belanja, rute kirim bersama, atau kegiatan cluster.</p>
                </div>

                <!-- Hidden Input -->
                <input type="file" id="cp-file-input" accept="image/*" multiple class="hidden" onchange="handlePostFilesSelect(this.files)">

                <!-- Initial Big Upload Button -->
                <button type="button" onclick="document.getElementById('cp-file-input').click()"
                        class="rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-6 py-3 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 transition active:scale-95">
                    Pilih dari Komputer
                </button>
            </div>

            <!-- STEP 2: EDIT, CROP & CAPTION STATE (Setelah foto dipilih) -->
            <div id="cp-step-edit" class="hidden flex flex-col md:flex-row">

                <!-- Left Column: Image Preview & Adjustments -->
                <div class="w-full md:w-3/5 bg-slate-900 flex flex-col justify-between select-none">
                    <!-- Main Image Frame with Zoom/Pan -->
                    <div id="cp-canvas-wrapper" class="relative w-full aspect-square overflow-hidden flex items-center justify-center bg-black cursor-grab active:cursor-grabbing">
                        <img id="cp-preview-img" src="" alt="Preview" class="max-h-full max-w-full object-contain pointer-events-none transition-transform duration-75 origin-center">
                        <!-- Grid Overlay when adjusting -->
                        <div id="cp-grid-overlay" class="absolute inset-0 pointer-events-none grid grid-cols-3 grid-rows-3 opacity-20 border border-white/40 hidden">
                            <div class="border-r border-b border-white/40"></div>
                            <div class="border-r border-b border-white/40"></div>
                            <div class="border-b border-white/40"></div>
                            <div class="border-r border-b border-white/40"></div>
                            <div class="border-r border-b border-white/40"></div>
                            <div class="border-b border-white/40"></div>
                            <div class="border-r border-white/40"></div>
                            <div class="border-r border-white/40"></div>
                            <div></div>
                        </div>
                    </div>

                    <!-- Bottom Controls: Multi-Photo Carousel & Adjustment Bar -->
                    <div class="bg-slate-900/95 p-3 space-y-2 border-t border-slate-800">
                        <!-- Multi-Photo Thumbnails Strip & "+ Tambah Foto" Button -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                            <div id="cp-thumbs-container" class="flex items-center gap-2">
                                <!-- Thumbnails injected dynamically -->
                            </div>

                            <!-- "Tambah Foto" Button (Button berubah menjadi Tambah Foto sesuai instruksi user) -->
                            <button type="button" onclick="document.getElementById('cp-file-input').click()"
                                    class="shrink-0 flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 px-3 py-2 text-[11px] font-bold text-slate-200 border border-slate-700 transition"
                                    title="Tambah foto lain untuk postingan ini">
                                <span>+</span>
                                <span>Tambah Foto</span>
                            </button>
                        </div>

                        <!-- Crop & Scale Adjustment Bar -->
                        <div class="flex items-center justify-between pt-1 text-white text-xs">
                            <!-- Aspect Ratio Buttons -->
                            <div class="flex items-center gap-1 bg-slate-800/80 p-1 rounded-xl">
                                <button type="button" onclick="setPostAspectRatio('1:1')" id="cp-ar-1" class="px-2 py-1 rounded-lg text-[10px] font-bold bg-[#4154e8] text-white">1:1</button>
                                <button type="button" onclick="setPostAspectRatio('4:5')" id="cp-ar-45" class="px-2 py-1 rounded-lg text-[10px] font-bold text-slate-400 hover:text-white">4:5</button>
                                <button type="button" onclick="setPostAspectRatio('16:9')" id="cp-ar-16" class="px-2 py-1 rounded-lg text-[10px] font-bold text-slate-400 hover:text-white">16:9</button>
                            </div>

                            <!-- Zoom Slider (Besarin / Kecilin foto) -->
                            <div class="flex items-center gap-2 bg-slate-800/80 px-3 py-1 rounded-xl">
                                <span class="text-[10px] text-slate-400">🔍</span>
                                <input type="range" id="cp-zoom-slider" min="1" max="2.5" step="0.05" value="1" oninput="updatePostZoom(this.value)"
                                       class="w-20 sm:w-28 accent-[#4154e8] h-1.5 bg-slate-700 rounded-lg cursor-pointer">
                                <span id="cp-zoom-val" class="text-[10px] font-semibold text-slate-300 w-8 text-right">1.0x</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Caption & Details Form -->
                <div class="w-full md:w-2/5 p-5 flex flex-col justify-between space-y-4 bg-white border-t md:border-t-0 md:border-l border-slate-100">
                    <div class="space-y-4">
                        <!-- User Header in Post Composer -->
                        <div class="flex items-center gap-3">
                            @php
                                $cUser = Auth::user();
                                $cName = $cUser ? $cUser->name : 'User';
                                $cInit = strtoupper(substr($cName, 0, 2));
                            @endphp
                            @if ($cUser && $cUser->avatar)
                                <img src="{{ $cUser->avatar }}" class="h-9 w-9 rounded-full object-cover">
                            @else
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] font-bold text-xs">
                                    {{ $cInit }}
                                </div>
                            @endif
                            <div>
                                <p class="text-xs font-bold text-slate-900 leading-tight">{{ $cUser->username ?? $cName }}</p>
                                <p class="text-[10px] text-slate-400">{{ $cUser->location ?? 'Cluster Beji' }}</p>
                            </div>
                        </div>

                        <!-- Caption Textarea -->
                        <div>
                            <textarea id="cp-caption-input" rows="4" maxlength="500" placeholder="Tulis keterangan momen gotong royong, sesi makan, titip belanja..."
                                      class="w-full text-xs text-slate-800 placeholder:text-slate-400 outline-none resize-none border-0 focus:ring-0 p-0 leading-relaxed"></textarea>
                            <div class="flex justify-between items-center text-[10px] text-slate-400 pt-2 border-t border-slate-100">
                                <span>Maks. 500 karakter</span>
                                <span id="cp-char-count">0 / 500</span>
                            </div>
                        </div>

                        <!-- Kategori Postingan Gotong Royong -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-700">Kategori Gotong Royong</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="selectPostCategory('BARENG MAKAN', this)" class="cp-cat-btn rounded-lg bg-[#4154e8] text-white px-2.5 py-1 text-[10px] font-bold transition">Bareng Makan</button>
                                <button type="button" onclick="selectPostCategory('BARENG BELANJA', this)" class="cp-cat-btn rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 px-2.5 py-1 text-[10px] font-bold transition">Bareng Belanja</button>
                                <button type="button" onclick="selectPostCategory('BARENG KIRIM', this)" class="cp-cat-btn rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 px-2.5 py-1 text-[10px] font-bold transition">Bareng Kirim</button>
                                <button type="button" onclick="selectPostCategory('GOTONG ROYONG', this)" class="cp-cat-btn rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 px-2.5 py-1 text-[10px] font-bold transition">Warga</button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button for Mobile View -->
                    <div class="pt-2 md:hidden">
                        <button type="button" onclick="submitCreatePost()"
                                class="w-full rounded-xl bg-[#4154e8] hover:bg-[#3243d4] py-2.5 text-xs font-extrabold text-white transition shadow-md shadow-[#4154e8]/20">
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
let cpSelectedCategory = 'BARENG MAKAN';

function openCreatePostModal() {
    const modal = document.getElementById('create-post-modal');
    const card = document.getElementById('create-post-card');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    });
}

function closeCreatePostModal() {
    const modal = document.getElementById('create-post-modal');
    const card = document.getElementById('create-post-card');
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        resetCreatePostStep();
    }, 200);
}

function handlePostFilesSelect(files) {
    if (!files || files.length === 0) return;

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            cpSelectedPhotos.push({
                file: file,
                dataUrl: e.target.result,
                zoom: 1,
                panX: 0,
                panY: 0
            });
            // Update UI after reading
            if (cpSelectedPhotos.length === 1) {
                cpActiveIndex = 0;
                showEditPostStep();
            }
            renderPostThumbs();
        };
        reader.readAsDataURL(file);
    });
}

function showEditPostStep() {
    document.getElementById('cp-step-upload').classList.add('hidden');
    document.getElementById('cp-step-edit').classList.remove('hidden');
    document.getElementById('cp-back-btn').classList.remove('hidden');
    document.getElementById('cp-submit-btn').classList.remove('hidden');
    document.getElementById('cp-header-placeholder').classList.add('hidden');

    displayActivePhoto();
}

function resetCreatePostStep() {
    cpSelectedPhotos = [];
    cpActiveIndex = 0;
    document.getElementById('cp-file-input').value = '';
    document.getElementById('cp-step-upload').classList.remove('hidden');
    document.getElementById('cp-step-edit').classList.add('hidden');
    document.getElementById('cp-back-btn').classList.add('hidden');
    document.getElementById('cp-submit-btn').classList.add('hidden');
    document.getElementById('cp-header-placeholder').classList.remove('hidden');
    document.getElementById('cp-caption-input').value = '';
}

function displayActivePhoto() {
    if (!cpSelectedPhotos[cpActiveIndex]) return;
    const current = cpSelectedPhotos[cpActiveIndex];
    const img = document.getElementById('cp-preview-img');
    img.src = current.dataUrl;

    // Reset zoom slider to current photo's zoom
    document.getElementById('cp-zoom-slider').value = current.zoom || 1;
    document.getElementById('cp-zoom-val').innerText = (current.zoom || 1) + 'x';
    applyPhotoTransform();
}

function renderPostThumbs() {
    const container = document.getElementById('cp-thumbs-container');
    container.innerHTML = cpSelectedPhotos.map((item, idx) => `
        <div class="relative shrink-0 h-11 w-11 rounded-lg overflow-hidden border-2 cursor-pointer transition ${idx === cpActiveIndex ? 'border-[#4154e8] scale-105' : 'border-slate-700 opacity-70 hover:opacity-100'}"
             onclick="switchActivePhoto(${idx})">
            <img src="${item.dataUrl}" class="h-full w-full object-cover">
            ${cpSelectedPhotos.length > 1 ? `
                <button type="button" onclick="event.stopPropagation(); removePhoto(${idx})"
                        class="absolute top-0.5 right-0.5 h-3.5 w-3.5 flex items-center justify-center rounded-full bg-black/70 text-white text-[9px] hover:bg-rose-600 transition">
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
    cpSelectedPhotos[cpActiveIndex].zoom = parseFloat(val);
    document.getElementById('cp-zoom-val').innerText = parseFloat(val).toFixed(1) + 'x';
    applyPhotoTransform();
}

function applyPhotoTransform() {
    const current = cpSelectedPhotos[cpActiveIndex];
    if (!current) return;
    const img = document.getElementById('cp-preview-img');
    img.style.transform = `scale(${current.zoom || 1})`;
}

function setPostAspectRatio(ar) {
    cpCurrentAspectRatio = ar;
    const wrapper = document.getElementById('cp-canvas-wrapper');

    // Reset button styles
    ['cp-ar-1', 'cp-ar-45', 'cp-ar-16'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.className = 'px-2 py-1 rounded-lg text-[10px] font-bold text-slate-400 hover:text-white';
        }
    });

    if (ar === '1:1') {
        wrapper.className = 'relative w-full aspect-square overflow-hidden flex items-center justify-center bg-black cursor-grab';
        document.getElementById('cp-ar-1').className = 'px-2 py-1 rounded-lg text-[10px] font-bold bg-[#4154e8] text-white';
    } else if (ar === '4:5') {
        wrapper.className = 'relative w-full aspect-[4/5] max-h-[380px] overflow-hidden flex items-center justify-center bg-black cursor-grab';
        document.getElementById('cp-ar-45').className = 'px-2 py-1 rounded-lg text-[10px] font-bold bg-[#4154e8] text-white';
    } else if (ar === '16:9') {
        wrapper.className = 'relative w-full aspect-video overflow-hidden flex items-center justify-center bg-black cursor-grab';
        document.getElementById('cp-ar-16').className = 'px-2 py-1 rounded-lg text-[10px] font-bold bg-[#4154e8] text-white';
    }
}

function selectPostCategory(cat, btn) {
    cpSelectedCategory = cat;
    document.querySelectorAll('.cp-cat-btn').forEach(b => {
        b.className = 'cp-cat-btn rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 px-2.5 py-1 text-[10px] font-bold transition';
    });
    btn.className = 'cp-cat-btn rounded-lg bg-[#4154e8] text-white px-2.5 py-1 text-[10px] font-bold transition';
}

// Character counter
document.getElementById('cp-caption-input')?.addEventListener('input', function() {
    document.getElementById('cp-char-count').innerText = this.value.length + ' / 500';
});

// Submit Post
function submitCreatePost() {
    if (cpSelectedPhotos.length === 0) {
        showErrorAlert('Foto Diperlukan', 'Harap pilih minimal satu foto untuk dibagikan.');
        return;
    }

    const caption = document.getElementById('cp-caption-input').value;
    const submitBtn = document.getElementById('cp-submit-btn');
    submitBtn.innerText = 'Mengunggah...';
    submitBtn.disabled = true;

    // Send via AJAX
    fetch('{{ route("posts.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            caption: caption,
            type: cpSelectedCategory,
            photos: cpSelectedPhotos.map(p => p.dataUrl)
        })
    })
    .then(r => r.json())
    .then(data => {
        submitBtn.innerText = 'Bagikan';
        submitBtn.disabled = false;
        closeCreatePostModal();

        if (data.success) {
            showSuccessAlert('Postingan Dibagikan!', 'Postingan baru kamu sudah berhasil tampil di komunitas & profil.');
            setTimeout(() => {
                window.location.reload();
            }, 1200);
        } else {
            showErrorAlert('Gagal Membagikan', data.message || 'Terjadi kesalahan.');
        }
    })
    .catch(err => {
        console.error(err);
        submitBtn.innerText = 'Bagikan';
        submitBtn.disabled = false;
        showErrorAlert('Gagal', 'Terjadi kesalahan koneksi.');
    });
}
</script>

