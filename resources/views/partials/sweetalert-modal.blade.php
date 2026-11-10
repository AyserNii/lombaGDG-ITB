{{-- ========================================================================= --}}
{{-- TOP-RIGHT MODERN TOAST NOTIFICATION (ANIMASI CEKLIS CENTANG)                  --}}
{{-- Tidak memblokir klik layar (pointer-events-none pada container)               --}}
{{-- ========================================================================= --}}
<div id="toast-container" class="fixed top-5 right-5 z-[9999] pointer-events-none flex flex-col gap-3 max-w-sm sm:max-w-md w-full px-4 sm:px-0">
    <div id="toast-card"
         style="display: none;"
         class="pointer-events-auto relative flex items-start gap-3.5 rounded-2xl bg-white p-4 shadow-[0_12px_40px_rgba(0,0,0,0.12)] border border-slate-100 transform translate-x-8 opacity-0 scale-95 transition-all duration-300 overflow-hidden">

        <!-- Animated Circle Checkmark Icon -->
        <div id="toast-icon-wrap" class="relative shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-500">
            <svg id="toast-svg-success" class="w-6 h-6 text-emerald-500" viewBox="0 0 52 52">
                <circle class="toast-checkmark-circle" cx="26" cy="26" r="23" fill="none" stroke="currentColor" stroke-width="4"/>
                <path class="toast-checkmark-check" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M14 27l8 8 16-16"/>
            </svg>

            <!-- Error Icon (Fallback) -->
            <svg id="toast-svg-error" class="hidden w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0 pt-0.5">
            <h4 id="toast-title" class="text-xs font-black text-slate-900 tracking-tight leading-tight">
                Berhasil!
            </h4>
            <p id="toast-message" class="text-[11px] text-slate-500 font-medium leading-relaxed mt-0.5">
                Perubahan data berhasil disimpan ke sistem.
            </p>
        </div>

        <!-- Close Button -->
        <button type="button" onclick="hideToast()" class="shrink-0 p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer" title="Tutup">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
            <div id="toast-progress-bar" class="h-full bg-emerald-500 transition-all linear" style="width: 100%;"></div>
        </div>
    </div>
</div>

<style>
/* ─── Animasi Menggambar Garis Ceklis (Stroke Draw) ─── */
.toast-checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-miterlimit: 10;
    animation: toast-draw 0.45s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}
.toast-checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: toast-draw 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.25s forwards;
}
@keyframes toast-draw {
    100% { stroke-dashoffset: 0; }
}
</style>

<script>
let toastTimer = null;
let toastProgressInterval = null;

function showSuccessToast(title, message, durationMs = 3500) {
    showToast(title || 'Berhasil!', message || 'Operasi berhasil dilakukan.', 'success', durationMs);
}

function showErrorToast(title, message, durationMs = 4000) {
    showToast(title || 'Gagal!', message || 'Terjadi kesalahan sistem.', 'error', durationMs);
}

// Aliases for compatibility
window.showSuccessAlert = showSuccessToast;
window.showErrorAlert = showErrorToast;
window.showSuccessToast = showSuccessToast;
window.showErrorToast = showErrorToast;
window.closeSweetAlert = hideToast;

function showToast(title, message, type = 'success', durationMs = 3500) {
    const card = document.getElementById('toast-card');
    const titleEl = document.getElementById('toast-title');
    const msgEl = document.getElementById('toast-message');
    const svgSuccess = document.getElementById('toast-svg-success');
    const svgError = document.getElementById('toast-svg-error');
    const iconWrap = document.getElementById('toast-icon-wrap');
    const progressBar = document.getElementById('toast-progress-bar');

    if (!card) return;

    clearTimeout(toastTimer);
    clearInterval(toastProgressInterval);

    titleEl.innerText = title;
    msgEl.innerText = message;

    if (type === 'success') {
        iconWrap.className = 'relative shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-500';
        svgSuccess.classList.remove('hidden');
        svgError.classList.add('hidden');
        progressBar.className = 'h-full bg-emerald-500 transition-all linear';
        // Re-trigger animation
        svgSuccess.innerHTML = `
            <circle class="toast-checkmark-circle" cx="26" cy="26" r="23" fill="none" stroke="currentColor" stroke-width="4"/>
            <path class="toast-checkmark-check" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M14 27l8 8 16-16"/>
        `;
    } else {
        iconWrap.className = 'relative shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-rose-50 text-rose-500';
        svgSuccess.classList.add('hidden');
        svgError.classList.remove('hidden');
        progressBar.className = 'h-full bg-rose-500 transition-all linear';
    }

    progressBar.style.width = '100%';
    card.style.display = 'flex';

    requestAnimationFrame(() => {
        card.classList.remove('translate-x-8', 'opacity-0', 'scale-95');
        card.classList.add('translate-x-0', 'opacity-100', 'scale-100');
    });

    const startTime = Date.now();
    toastProgressInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        const remainingPct = Math.max(0, 100 - (elapsed / durationMs) * 100);
        progressBar.style.width = remainingPct + '%';
        if (elapsed >= durationMs) {
            clearInterval(toastProgressInterval);
        }
    }, 40);

    toastTimer = setTimeout(() => {
        hideToast();
    }, durationMs);
}

function hideToast() {
    clearTimeout(toastTimer);
    clearInterval(toastProgressInterval);
    const card = document.getElementById('toast-card');
    if (!card) return;

    card.classList.add('translate-x-8', 'opacity-0', 'scale-95');
    card.classList.remove('translate-x-0', 'opacity-100', 'scale-100');

    setTimeout(() => {
        card.style.display = 'none';
    }, 280);
}

// Auto-trigger on session message
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showSuccessToast('Berhasil!', "{{ session('success') }}");
    @endif
    @if(session('error'))
        showErrorToast('Gagal!', "{{ session('error') }}");
    @endif
});
</script>
