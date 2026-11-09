<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · BARENG</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#edf1f7] text-[#1e293b] antialiased font-sans">
    <div class="flex min-h-screen w-full bg-[#edf1f7]">
        <aside class="sticky top-0 hidden h-screen w-[240px] shrink-0 flex-col bg-[#4154e8] text-white z-30 lg:flex overflow-y-auto">
            <div class="flex items-center px-5 pt-5 pb-5">
                <a href="{{ route('dashboard') }}" class="block">
                    {{-- Logo SVG inline — ikon pin saja tanpa teks, koordinat diperbaiki --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="16 6 68 88" width="48" height="48" role="img" aria-label="Logo BARENG">
                        <path d="M50 6C30 6 16 21 16 40c0 22 34 54 34 54s34-32 34-54C84 21 70 6 50 6Z" fill="rgba(255,255,255,0.22)"/>
                        <g fill="none" stroke="#fff" stroke-width="3.5">
                            <circle cx="42" cy="36" r="9"/>
                            <circle cx="58" cy="36" r="9"/>
                            <circle cx="50" cy="50" r="9"/>
                        </g>
                    </svg>
                </a>
            </div>

            <nav class="flex flex-1 flex-col gap-1.5 pt-1">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'url' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'home'],
                        ['route' => 'makan', 'url' => route('makan'), 'label' => 'Bareng Makan', 'icon' => 'food'],
                        ['route' => 'belanja', 'url' => route('belanja'), 'label' => 'Bareng Belanja', 'icon' => 'bag'],
                        ['route' => 'kirim', 'url' => route('kirim'), 'label' => 'Bareng Kirim', 'icon' => 'truck'],
                        ['route' => 'sesi', 'url' => route('sesi'), 'label' => 'Sesi Aktif', 'icon' => 'schedule'],
                        ['route' => 'community', 'url' => route('community'), 'label' => 'Komunitas', 'icon' => 'forum'],
                        ['route' => 'settings', 'url' => route('settings'), 'label' => 'Pengaturan', 'icon' => 'settings'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $isActive = request()->routeIs($item['route']); @endphp
                    <div class="{{ $isActive ? 'relative pl-4' : 'px-4' }}">
                        <a href="{{ $item['url'] }}"
                           class="{{ $isActive
                                ? 'relative z-10 flex items-center gap-3 rounded-l-full bg-[#edf1f7] py-3.5 pl-4 pr-3 text-sm font-extrabold text-[#1e293b] shadow-[-6px_4px_16px_rgba(0,0,0,0.06)]'
                                : 'group flex items-center gap-3 rounded-2xl py-3 px-3 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white' }}">
                            <span class="{{ $isActive ? 'flex h-7 w-7 items-center justify-center text-[#4154e8]' : 'flex h-7 w-7 items-center justify-center text-white/80 group-hover:text-white' }}">
                                @include('partials.icons', ['name' => $item['icon'], 'class' => 'h-5 w-5'])
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </div>
                @endforeach
            </nav>

            <div class="p-4 mt-auto">
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white">
                        <span class="flex h-6 w-6 items-center justify-center">
                            @include('partials.icons', ['name' => 'logout', 'class' => 'h-5 w-5'])
                        </span>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col min-h-screen bg-[#edf1f7]">
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 bg-[#edf1f7]/95 px-6 py-4 backdrop-blur-md">
                <div class="relative w-full max-w-md">
                    <input type="search" placeholder="Cari sesi, merchant, atau tetangga..."
                           class="w-full rounded-2xl border border-transparent bg-white py-2.5 pl-4 pr-10 text-xs md:text-sm text-[#1e293b] outline-none shadow-[0_2px_12px_rgba(0,0,0,0.03)] placeholder:text-[#a0aec0] focus:ring-2 focus:ring-[#4154e8]/30">
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-[#718096]">
                        @include('partials.icons', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                </div>

                <div class="flex items-center gap-3 md:gap-4">
                    @php
                        $authUser = Auth::user() ? Auth::user()->toArray() : session('user', [
                            'name' => 'Grace Stanley',
                            'role' => 'Customer · Cluster Beji',
                            'location' => 'Beji, Depok'
                        ]);
                        $rawName = trim($authUser['name'] ?? 'User');
                        $nameParts = preg_split('/\s+/', $rawName);
                        $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));
                    @endphp

                    <div class="hidden sm:flex items-center gap-1.5 rounded-full bg-white px-3.5 py-2 shadow-sm text-xs font-bold text-[#1e293b]">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $authUser['location'] ?? 'Beji, Depok' }}</span>
                        @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5 text-[#718096]'])
                    </div>

                    <a href="{{ route('sesi') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#1c243a] text-white shadow-sm transition hover:opacity-90" title="Chat Sesi">
                        @include('partials.icons', ['name' => 'mail', 'class' => 'h-4 w-4'])
                    </a>

                    <button type="button" onclick="alert('Tidak ada notifikasi baru saat ini.')" class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-[#1c243a] text-white shadow-sm transition hover:opacity-90" title="Notifikasi Sesi">
                        @include('partials.icons', ['name' => 'bell', 'class' => 'h-4 w-4'])
                        <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-[#4154e8] ring-2 ring-[#1c243a]"></span>
                    </button>

                    <a href="{{ route('settings') }}" class="flex items-center gap-3 cursor-pointer">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-xs font-black shadow-sm ring-2 ring-white select-none">
                            {{ $userInitials }}
                        </div>
                        <div class="hidden md:block text-left">
                            <span class="block text-xs font-bold text-[#1e293b] leading-tight">{{ $authUser['name'] }}</span>
                            <span class="block text-[10px] text-[#7e8ba0]">{{ $authUser['role'] ?? 'Customer' }}</span>
                        </div>
                    </a>
                </div>
            </header>

            <main id="main-content" class="flex-1 px-6 pb-12 pt-2">
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-4 bottom-3 z-40 flex items-center justify-around rounded-3xl bg-[#4154e8] p-2 text-white shadow-2xl lg:hidden">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center rounded-2xl px-2.5 py-1.5 text-[10px] font-bold {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'home', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Dashboard</span>
        </a>
        <a href="{{ route('makan') }}" class="flex flex-col items-center rounded-2xl px-2.5 py-1.5 text-[10px] font-bold {{ request()->routeIs('makan') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'food', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Makan</span>
        </a>
        <a href="{{ route('belanja') }}" class="flex flex-col items-center rounded-2xl px-2.5 py-1.5 text-[10px] font-bold {{ request()->routeIs('belanja') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'bag', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Belanja</span>
        </a>
        <a href="{{ route('sesi') }}" class="flex flex-col items-center rounded-2xl px-2.5 py-1.5 text-[10px] font-bold {{ request()->routeIs('sesi') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'schedule', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Sesi</span>
        </a>
        <a href="{{ route('community') }}" class="flex flex-col items-center rounded-2xl px-2.5 py-1.5 text-[10px] font-bold {{ request()->routeIs('community') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'forum', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Komunitas</span>
        </a>
    </nav>
</body>
</html>
