{{-- SweetAlert Modern Modal Component --}}
<div id="swal-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-200">
    <div id="swal-card" class="w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl border border-slate-100 text-center transform scale-95 opacity-0 transition-all duration-200">
        <!-- Success Icon with Animated Checkmark -->
        <div id="swal-icon-success" class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 ring-8 ring-emerald-50/50">
            <svg class="h-8 w-8 text-emerald-500 animate-scale-up" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div id="swal-icon-error" class="mx-auto mb-4 hidden h-16 w-16 items-center justify-center rounded-full bg-rose-50 ring-8 ring-rose-50/50">
            <svg class="h-8 w-8 text-rose-500 animate-scale-up" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <!-- Title & Message -->
        <h3 id="swal-title" class="text-lg font-black text-slate-900 tracking-tight">Berhasil!</h3>
        <p id="swal-message" class="mt-1.5 text-xs text-slate-500 leading-relaxed">Operasi telah berhasil diselesaikan.</p>

        <!-- Solid Modern Button -->
        <div class="mt-6">
            <button type="button" onclick="closeSweetAlert()"
                    class="w-full rounded-xl bg-[#4154e8] hover:bg-[#3243d4] py-3 text-xs font-extrabold text-white shadow-md shadow-[#4154e8]/20 transition active:scale-[0.98]">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
function showSuccessAlert(title, message) {
    const modal = document.getElementById('swal-modal');
    const card = document.getElementById('swal-card');
    const titleEl = document.getElementById('swal-title');
    const msgEl = document.getElementById('swal-message');
    const iconSuccess = document.getElementById('swal-icon-success');
    const iconError = document.getElementById('swal-icon-error');

    titleEl.innerText = title || 'Berhasil!';
    msgEl.innerText = message || 'Perubahan berhasil disimpan.';
    iconSuccess.classList.remove('hidden');
    iconSuccess.classList.add('flex');
    iconError.classList.add('hidden');

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    });
}

function showErrorAlert(title, message) {
    const modal = document.getElementById('swal-modal');
    const card = document.getElementById('swal-card');
    const titleEl = document.getElementById('swal-title');
    const msgEl = document.getElementById('swal-message');
    const iconSuccess = document.getElementById('swal-icon-success');
    const iconError = document.getElementById('swal-icon-error');

    titleEl.innerText = title || 'Terjadi Kesalahan';
    msgEl.innerText = message || 'Silakan coba beberapa saat lagi.';
    iconSuccess.classList.add('hidden');
    iconError.classList.remove('hidden');
    iconError.classList.add('flex');

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    });
}

function closeSweetAlert() {
    const modal = document.getElementById('swal-modal');
    const card = document.getElementById('swal-card');
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Auto-trigger if Laravel session success or error exists
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showSuccessAlert('Berhasil!', "{{ session('success') }}");
    @endif
    @if(session('error'))
        showErrorAlert('Gagal!', "{{ session('error') }}");
    @endif
});
</script>

