<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · BARENG</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-[#eaf2ff] font-sans text-[#1e293b] antialiased overflow-x-hidden overflow-y-auto">
    <!-- Decorative Ambient Blobs (Fixed so they never trigger scrollbars) -->
    <div class="fixed -top-24 -left-16 h-72 w-72 rounded-full bg-[#cfe0ff] blur-3xl opacity-70 pointer-events-none"></div>
    <div class="fixed -right-10 top-10 h-56 w-56 rounded-full bg-[#b9d0ff] blur-3xl opacity-70 pointer-events-none"></div>
    <div class="fixed bottom-0 left-1/3 h-64 w-64 rounded-full bg-[#d9e6ff] blur-3xl opacity-70 pointer-events-none"></div>
    <div class="fixed -bottom-16 -right-10 h-80 w-80 rounded-full bg-[#c3d8ff] blur-3xl opacity-70 pointer-events-none"></div>

    <!-- Main Container: Centered vertically with my-auto on card to prevent top clipping -->
    <main class="relative z-10 flex min-h-screen w-full flex-col items-center justify-center p-3 sm:p-5 lg:p-7">
        <section class="relative w-full max-w-5xl my-auto overflow-hidden rounded-[28px] sm:rounded-[34px] border-2 border-[#7ea6ef] bg-white/90 shadow-[0_24px_70px_rgba(79,107,255,0.16)] backdrop-blur">
            <div class="absolute -top-10 right-24 h-28 w-28 rounded-full bg-[#dbe8ff] pointer-events-none"></div>
            <div class="absolute top-16 right-[42%] h-16 w-16 rounded-full bg-[#e7f0ff] pointer-events-none"></div>

            <div class="grid items-center gap-6 lg:gap-10 px-6 py-6 sm:px-8 sm:py-7 lg:grid-cols-2 lg:px-11 lg:py-8">
                <!-- Left Column: Form -->
                <div class="w-full max-w-md mx-auto">
                    <!-- Logo Web -->
                    <div class="mb-3 sm:mb-4">
                        <a href="/" class="inline-block hover:opacity-90 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="16 6 68 88" width="36" height="36" role="img" aria-label="Logo BARENG">
                                <path d="M50 6C30 6 16 21 16 40c0 22 34 54 34 54s34-32 34-54C84 21 70 6 50 6Z" fill="#4154e8"/>
                                <g fill="none" stroke="#fff" stroke-width="3.5">
                                    <circle cx="42" cy="36" r="9"/>
                                    <circle cx="58" cy="36" r="9"/>
                                    <circle cx="50" cy="50" r="9"/>
                                </g>
                            </svg>
                        </a>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#1d355d] leading-tight">Selamat datang!</h1>
                    <p class="mt-1 text-xs text-slate-500 font-medium">Masuk dengan email dan password untuk mulai berkolaborasi.</p>

                    @if (session('success'))
                        <div class="mt-3 rounded-xl bg-emerald-50 border border-emerald-200 px-3.5 py-2 text-xs font-bold text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="mt-3 rounded-xl bg-blue-50 border border-blue-200 px-3.5 py-2 text-xs font-bold text-blue-700">
                            {{ session('info') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-3 rounded-xl bg-red-50 border border-red-200 px-3.5 py-2 text-xs font-bold text-red-600">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('auth.authenticate') }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label for="email" class="mb-1 block text-xs font-bold text-[#1d355d]">Alamat Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   placeholder="nama@email.com"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="password" class="text-xs font-bold text-[#1d355d]">Password</label>
                            </div>
                            <div class="relative">
                                <input id="password" type="password" name="password" required autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-11 text-xs sm:text-sm outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/20">
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

                        <div class="flex items-center justify-between pt-0.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#4154e8] focus:ring-[#4154e8]">
                                <span class="text-xs font-semibold text-slate-600">Ingat saya</span>
                            </label>
                        </div>

                        <button type="submit"
                                class="mt-2 w-full rounded-xl bg-[#4154e8] py-2.5 sm:py-3 text-xs sm:text-sm font-extrabold tracking-wide text-white shadow-md shadow-[#4154e8]/25 transition hover:bg-[#2d3ec4] active:scale-[0.99] cursor-pointer">
                            MASUK SEKARANG
                        </button>
                    </form>

                    <!-- Separator Atau -->
                    <div class="relative my-3.5 flex items-center justify-center">
                        <div class="border-t border-slate-200 w-full"></div>
                        <span class="bg-white/95 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0">atau</span>
                        <div class="border-t border-slate-200 w-full"></div>
                    </div>

                    <!-- Tombol Masuk Lewat Google -->
                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-2.5 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition active:scale-[0.99] group cursor-pointer">
                        <svg class="h-4 sm:h-5 w-4 sm:w-5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span class="group-hover:text-slate-900 transition">Masuk lewat Google</span>
                    </a>

                    <!-- Footer Link -->
                    <div class="mt-4 border-t border-slate-200/80 pt-3 text-center">
                        <p class="text-xs text-slate-500 font-medium">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="font-bold text-[#4154e8] hover:underline ml-1">
                                Daftar Akun Baru &rarr;
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Right Column: Illustration & Highlights -->
                <div class="relative isolate hidden items-center justify-center md:flex flex-col py-2">
                    <div class="absolute h-64 w-64 rounded-full bg-[#d7e7ff] lg:h-80 lg:w-80 pointer-events-none"></div>
                    <div class="absolute top-4 right-8 h-14 w-14 rounded-full bg-[#cfe0ff] pointer-events-none"></div>
                    <div class="absolute bottom-6 left-6 h-10 w-10 rounded-full bg-[#bdd4ff] pointer-events-none"></div>

                    <div class="relative z-10 w-full max-w-[360px] text-center">
                        <iframe src="https://lottie.host/embed/860e6d51-6c89-42a4-8f0f-4072783dc56c/e1RHGYLrUM.lottie"
                                class="mx-auto h-52 w-52 sm:h-56 sm:w-56 lg:h-64 lg:w-64 border-0 pointer-events-none"
                                style="background:transparent;"
                                loading="lazy"
                                title="Animasi BARENG"></iframe>

                        <div class="space-y-1 mt-1">
                            <h2 class="text-lg sm:text-xl font-extrabold text-[#1d355d]">One Platform. Shared Needs.</h2>
                            <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                                Gabungkan pesanan makan, belanja sembako, dan logistik kirim bareng tetangga terdekatmu.
                            </p>
                        </div>

                        <div class="mt-4 flex justify-center gap-3">
                            <div class="rounded-xl bg-white/95 px-3.5 py-1.5 text-center shadow-sm border border-slate-100">
                                <p class="text-xs sm:text-sm font-extrabold text-[#4154e8]">3 Modul</p>
                                <p class="text-[9px] text-slate-400 font-semibold">Makan · Belanja · Kirim</p>
                            </div>
                            <div class="rounded-xl bg-white/95 px-3.5 py-1.5 text-center shadow-sm border border-slate-100">
                                <p class="text-xs sm:text-sm font-extrabold text-emerald-600">Gotong Royong</p>
                                <p class="text-[9px] text-slate-400 font-semibold">Hemat Biaya & Emisi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function togglePasswordVisibility(button) {
            const container = button.closest('.relative') || button.parentElement;
            const pw = container ? container.querySelector('input') : document.getElementById('password');
            if (!pw) return;

            const isPassword = pw.type === 'password';
            pw.type = isPassword ? 'text' : 'password';

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
