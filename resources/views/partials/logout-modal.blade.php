<!-- MODAL KONFIRMASI LOGOUT / KELUAR DARI AKUN (Styled according to user specification) -->
<div id="logout-modal"
     style="display: none;"
     class="fixed inset-0 z-[100] items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm animate-fade-in"
     onclick="if (event.target === this) closeLogoutModal();">

    <div class="relative w-full max-w-sm sm:max-w-md rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-slate-100 transform transition-all animate-scale-up text-left">
        <!-- 1. Header: Logo Web & Tombol Close -->
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/bareng-logo.svg') }}" alt="Logo BARENG" class="h-6 sm:h-7 w-auto object-contain">
            </div>
            <button type="button"
                    onclick="closeLogoutModal()"
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 transition cursor-pointer"
                    title="Tutup">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- 2. Profil User Card (Inspirasi Sesuai Foto Contoh) -->
        <div class="rounded-2xl bg-[#f8fafc] border border-slate-100/90 p-6 flex flex-col items-center justify-center text-center mb-6">
            <div class="relative">
                @if (!empty($authUser['avatar']))
                    <span class="relative block h-20 w-20">
                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-[#1b4332] text-2xl font-black text-white">{{ $userInitials ?? 'BA' }}</span>
                        <img src="{{ $authUser['avatar'] }}" alt="{{ $authUser['name'] }}" onerror="this.remove()"
                             class="relative h-20 w-20 rounded-full object-cover ring-4 ring-white shadow-md">
                    </span>
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-[#1b4332] text-white text-2xl font-black ring-4 ring-white shadow-md">
                        {{ $userInitials ?? 'BA' }}
                    </div>
                @endif
                <!-- Checkmark badge di pojok kanan bawah foto profil -->
                <div class="absolute -bottom-0.5 -right-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white ring-2 ring-white shadow-sm"
                     title="Akun Terverifikasi & Aktif">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <!-- Nama & Email User -->
            <h4 class="text-base font-extrabold text-slate-900 mt-3 leading-tight">{{ $authUser['name'] }}</h4>
            <p class="text-xs font-medium text-slate-500 mt-1 truncate max-w-[240px]">{{ $authUser['email'] ?? 'user@bareng.id' }}</p>
        </div>

        <!-- 3. Pertanyaan Konfirmasi -->
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Keluar dari akun?</h3>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Apakah Anda yakin ingin keluar dari web?<br>
                Sesi Anda akan diakhiri dan Anda dapat masuk kembali kapan saja untuk melanjutkan aktivitas gotong royong.
            </p>
        </div>

        <!-- 4. Tombol Aksi: Tetap Masuk vs Ya, Keluar -->
        <div class="flex items-center gap-3">
            <button type="button"
                    onclick="closeLogoutModal()"
                    class="flex-1 py-3 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm text-center transition cursor-pointer">
                Tetap Masuk
            </button>

            <form method="POST" action="{{ route('auth.logout') }}" class="flex-1 m-0">
                @csrf
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-[#dc2626] hover:bg-[#b91c1c] text-white font-bold text-sm flex items-center justify-center gap-2 shadow-sm shadow-rose-500/20 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Ya, Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openLogoutModal() {
    // Tutup dropdown user di navbar jika sedang terbuka
    const dropdown = document.getElementById('user-menu-dropdown') || document.getElementById('user-dropdown-menu');
    if (dropdown) {
        dropdown.classList.add('hidden');
        dropdown.style.display = 'none';
    }
    if (typeof userDropdownOpen !== 'undefined') {
        userDropdownOpen = false;
    }

    const modal = document.getElementById('logout-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }
}

function closeLogoutModal() {
    const modal = document.getElementById('logout-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('logout-modal');
        if (modal && modal.style.display !== 'none' && !modal.classList.contains('hidden')) {
            closeLogoutModal();
        }
    }
});
</script>
