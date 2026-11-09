<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru · BARENG</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-hidden bg-[#eaf2ff] font-sans text-[#1e293b] antialiased">
    <div class="blob -top-24 -left-16 h-72 w-72 bg-[#cfe0ff]"></div>
    <div class="blob -right-10 top-10 h-56 w-56 bg-[#b9d0ff]"></div>
    <div class="blob bottom-0 left-1/3 h-64 w-64 bg-[#d9e6ff]"></div>
    <div class="blob -bottom-16 -right-10 h-80 w-80 bg-[#c3d8ff]"></div>

    <main class="relative z-10 flex min-h-screen items-center justify-center p-4 lg:p-8">
        <section class="relative w-full max-w-5xl overflow-hidden rounded-[36px] border-2 border-[#7ea6ef] bg-white/85 shadow-[0_30px_80px_rgba(79,107,255,0.18)] backdrop-blur">
            <div class="absolute -top-10 right-24 h-28 w-28 rounded-full bg-[#dbe8ff]"></div>
            <div class="absolute top-16 right-[42%] h-16 w-16 rounded-full bg-[#e7f0ff]"></div>

            <div class="grid items-center gap-8 px-6 py-8 lg:grid-cols-2 lg:px-12 lg:py-10">
                <div class="w-full max-w-md mx-auto">
                    <div class="mb-5">
                        <a href="/">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="16 6 68 88" width="40" height="40" role="img" aria-label="Logo BARENG">
                                <path d="M50 6C30 6 16 21 16 40c0 22 34 54 34 54s34-32 34-54C84 21 70 6 50 6Z" fill="#4154e8"/>
                                <g fill="none" stroke="#fff" stroke-width="3.5">
                                    <circle cx="42" cy="36" r="9"/>
                                    <circle cx="58" cy="36" r="9"/>
                                    <circle cx="50" cy="50" r="9"/>
                                </g>
                            </svg>
                        </a>
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-[#1d355d]">Buat Akun Baru</h1>
                    <p class="mt-1.5 text-xs text-slate-500">Mulai gabung dengan sesi gotong royong di sekitarmu.</p>

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs font-bold text-red-600">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('auth.store') }}" method="POST" class="mt-5 space-y-3.5">
                        @csrf
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-[#1d355d]">Nama Lengkap</span>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="Contoh: Budi Santoso"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="block">
                                <span class="mb-1 block text-xs font-bold text-[#1d355d]">Email</span>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       placeholder="nama@email.com"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                            </label>

                            <label class="block">
                                <span class="mb-1 block text-xs font-bold text-[#1d355d]">No. WhatsApp</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       placeholder="08123456789"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                            </label>
                        </div>

                        <div>
                            <label for="location" class="mb-1 block text-xs font-bold text-[#1d355d]">Wilayah / Cluster Domisili</label>
                            <input id="location" type="text" name="location" value="{{ old('location') }}"
                                   placeholder="Contoh: Cluster Beji Indah, Margonda, dll."
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="password" class="mb-1 block text-xs font-bold text-[#1d355d]">Password</label>
                                <div class="relative">
                                    <input id="password" type="password" name="password" required minlength="6" autocomplete="new-password"
                                           placeholder="Min. 6 karakter"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-11 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                                    <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#4154e8] transition focus:outline-none" aria-label="Tampilkan password" title="Tampilkan/Sembunyikan password">
                                        <svg class="h-4 w-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <svg class="h-4 w-4 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-1 block text-xs font-bold text-[#1d355d]">Konfirmasi Password</label>
                                <div class="relative">
                                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="6" autocomplete="new-password"
                                           placeholder="Ulangi password"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-11 text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                                    <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#4154e8] transition focus:outline-none" aria-label="Tampilkan password" title="Tampilkan/Sembunyikan password">
                                        <svg class="h-4 w-4 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <svg class="h-4 w-4 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="mt-4 w-full rounded-xl bg-[#4154e8] py-3 text-sm font-extrabold tracking-wide text-white shadow-lg shadow-[#4154e8]/30 transition hover:bg-[#2d3ec4] active:scale-[0.99]">
                            DAFTAR SEKARANG
                        </button>
                    </form>

                    <div class="mt-5 border-t border-slate-200/80 pt-4 text-center">
                        <p class="text-xs text-slate-500">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-bold text-[#4154e8] hover:underline ml-1">
                                Masuk di sini &rarr;
                            </a>
                        </p>
                    </div>
                </div>

                <div class="relative isolate hidden min-h-[460px] items-center justify-center md:flex flex-col">
                    <div class="absolute h-72 w-72 rounded-full bg-[#d7e7ff] lg:h-[400px] lg:w-[400px]"></div>
                    <div class="absolute top-8 right-10 h-16 w-16 rounded-full bg-[#cfe0ff]"></div>
                    <div class="absolute bottom-10 left-8 h-10 w-10 rounded-full bg-[#bdd4ff]"></div>

                    <div class="relative z-10 w-full max-w-[400px] text-center">
                        <iframe src="https://lottie.host/embed/860e6d51-6c89-42a4-8f0f-4072783dc56c/e1RHGYLrUM.lottie"
                                class="mx-auto h-72 w-72 lg:h-84 lg:w-84 border-0"
                                style="background:transparent;"
                                loading="lazy"
                                title="Animasi BARENG"></iframe>

                        <div class="space-y-1">
                            <h2 class="text-xl font-extrabold text-[#1d355d]">Gotong Royong Modern</h2>
                            <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                                Daftar dan rasakan kemudahan gabung sesi makan, belanja, dan kirim barang tanpa ongkir mahal.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function togglePasswordVisibility(button) {
            const container = button.closest('.relative') || button.parentElement;
            const input = container ? container.querySelector('input') : null;
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const eyeIcon = button.querySelector('.eye-icon');
            const eyeSlashIcon = button.querySelector('.eye-slash-icon');
            if (eyeIcon && eyeSlashIcon) {
                if (isPassword) {
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
            }
        }
    </script>
</body>
</html>

