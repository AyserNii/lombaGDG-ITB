<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · BARENG</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#edf1f7] text-[#1e293b] antialiased font-sans">
    @php
        $immersivePage = request()->routeIs('community', 'chat');
        $communityPage = request()->routeIs('community');
    @endphp
    <div class="flex min-h-screen w-full bg-[#edf1f7]">
        <!-- SIDEBAR DESKTOP -->
        <aside class="sticky top-0 hidden h-screen w-[240px] shrink-0 flex-col bg-[#4154e8] text-white z-30 lg:flex overflow-y-auto">
            <div class="flex items-center px-5 pt-5 pb-5">
                <a href="{{ route('dashboard') }}" class="block">
                    {{-- Logo SVG inline — ikon pin saja tanpa teks --}}
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

            <nav class="flex flex-1 flex-col gap-1 pt-1">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'url' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'home'],
                        ['route' => 'makan', 'url' => route('makan'), 'label' => 'Bareng Makan', 'icon' => 'food'],
                        ['route' => 'belanja', 'url' => route('belanja'), 'label' => 'Bareng Belanja', 'icon' => 'bag'],
                        ['route' => 'kirim', 'url' => route('kirim'), 'label' => 'Bareng Kirim', 'icon' => 'truck'],
                        ['route' => 'sesi', 'url' => route('sesi'), 'label' => 'Sesi Aktif', 'icon' => 'schedule'],
                        ['route' => 'community', 'url' => route('community'), 'label' => 'Komunitas', 'icon' => 'forum'],
                        ['route' => 'chat', 'url' => route('chat'), 'label' => 'Chat DM', 'icon' => 'dm'],
                        ['is_modal' => true, 'action' => 'openCreatePostModal()', 'label' => 'Create', 'icon' => 'plus-square'],
                        ['route' => 'profile', 'url' => route('profile'), 'label' => 'Profil Saya', 'icon' => 'user'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @if (isset($item['is_modal']) && $item['is_modal'])
                        <div class="px-4">
                            <button type="button" onclick="{{ $item['action'] }}"
                               class="w-full group flex items-center gap-3 rounded-2xl py-2.5 px-3 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">
                                <span class="flex h-6 w-6 items-center justify-center text-white/90 group-hover:text-white">
                                    @include('partials.icons', ['name' => $item['icon'], 'class' => 'h-5 w-5'])
                                </span>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        </div>
                    @else
                        @php $isActive = request()->routeIs($item['route']) || ($item['route'] === 'profile' && request()->routeIs('profile.*')); @endphp
                        <div class="{{ $isActive ? 'relative pl-4' : 'px-4' }}">
                            <a href="{{ $item['url'] }}"
                               class="{{ $isActive
                                    ? 'relative z-10 flex items-center gap-3 rounded-l-full bg-[#edf1f7] py-3 pl-4 pr-3 text-sm font-extrabold text-[#1e293b] shadow-[-6px_4px_16px_rgba(0,0,0,0.06)]'
                                    : 'group flex items-center gap-3 rounded-2xl py-2.5 px-3 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white' }}">
                                <span class="{{ $isActive ? 'flex h-6 w-6 items-center justify-center text-[#4154e8]' : 'flex h-6 w-6 items-center justify-center text-white/80 group-hover:text-white' }}">
                                    @include('partials.icons', ['name' => $item['icon'], 'class' => 'h-5 w-5'])
                                </span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="p-4 mt-auto">
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white">
                        <span class="flex h-5 w-5 items-center justify-center">
                            @include('partials.icons', ['name' => 'logout', 'class' => 'h-5 w-5'])
                        </span>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col min-h-screen {{ $immersivePage ? 'bg-white' : 'bg-[#edf1f7]' }}">
            <!-- HEADER -->
            @unless ($immersivePage)
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 bg-[#edf1f7]/95 px-6 py-4 backdrop-blur-md">
                <div class="relative w-full max-w-md">
                    <input type="search" placeholder="Cari sesi, postingan, atau username warga..."
                           class="w-full rounded-2xl border border-transparent bg-white py-2.5 pl-4 pr-10 text-xs md:text-sm text-[#1e293b] outline-none shadow-[0_2px_12px_rgba(0,0,0,0.03)] placeholder:text-[#a0aec0] focus:ring-2 focus:ring-[#4154e8]/30">
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-[#718096]">
                        @include('partials.icons', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                </div>

                <div class="flex items-center gap-3 md:gap-4">
                    @php
                        $userModel = Auth::user();
                        $authUser = $userModel ? $userModel->toArray() : session('user', [
                            'name' => 'Resya Anggara',
                            'username' => 'aysernii',
                            'role' => 'Customer · Cluster Beji',
                            'location' => 'Beji, Depok'
                        ]);
                        $rawName = trim($authUser['name'] ?? 'User');
                        $nameParts = preg_split('/\s+/', $rawName);
                        $userInitials = strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : ''));

                        // Unread messages and notifications counts
                        $unreadMsgCount = $userModel ? \App\Models\Message::where('receiver_id', $userModel->id)->where('is_read', false)->count() : 0;
                        $unreadNotifCount = $userModel ? \App\Models\Notification::where('user_id', $userModel->id)->where('is_read', false)->count() : 0;
                    @endphp

                    <div class="hidden sm:flex items-center gap-1.5 rounded-full bg-white px-3.5 py-2 shadow-sm text-xs font-bold text-[#1e293b]">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $authUser['location'] ?? 'Beji, Depok' }}</span>
                        @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5 text-[#718096]'])
                    </div>

                    <!-- Direct Messages / Chat Icon (Foto 2 Style with Red Badge) -->
                    <a href="{{ route('chat') }}" class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-[#1c243a] text-white shadow-sm transition hover:opacity-90" title="Direct Messages (Chat)">
                        @include('partials.icons', ['name' => 'dm', 'class' => 'h-5 w-5 text-white'])
                        @php
                            $msgBadgeCount = $unreadMsgCount > 0 ? $unreadMsgCount : 1;
                        @endphp
                        <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff3040] text-[10px] font-black text-white ring-2 ring-white shadow-md select-none">
                            {{ $msgBadgeCount > 9 ? '9+' : $msgBadgeCount }}
                        </span>
                    </a>

                    <!-- Notifications Bell → Dropdown (white theme) -->
                    <div class="relative" id="notif-wrapper">
                        <button type="button"
                                onclick="toggleNotifDropdown()"
                                class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-[#1c243a] text-white shadow-sm transition hover:opacity-90"
                                title="Notifikasi"
                                id="notif-bell-btn">
                            @include('partials.icons', ['name' => 'bell', 'class' => 'h-4 w-4'])
                            @if ($unreadNotifCount > 0)
                                <span id="notif-badge" class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white ring-2 ring-[#1c243a]">
                                    {{ $unreadNotifCount }}
                                </span>
                            @else
                                <span id="notif-badge" class="absolute top-2 right-2 h-2 w-2 rounded-full bg-[#4154e8] ring-2 ring-[#1c243a] hidden"></span>
                            @endif
                        </button>

                        <!-- Dropdown Panel (white, lebar 380px) -->
                        <div id="notif-dropdown"
                             class="absolute right-0 top-[calc(100%+10px)] z-50 hidden w-[360px] rounded-[20px] bg-white shadow-[0_8px_40px_rgba(0,0,0,0.12)] border border-slate-200/80 overflow-hidden animate-fade-in">

                            <!-- Header dropdown -->
                            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                                <h3 class="text-sm font-extrabold text-slate-900">Notifikasi</h3>
                                <a href="{{ route('notifications') }}" class="text-xs font-bold text-[#4154e8] hover:underline">Lihat semua</a>
                            </div>

                            <!-- Notification list (populated by JS) -->
                            <div id="notif-list" class="max-h-[420px] overflow-y-auto divide-y divide-slate-50">
                                <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                                    <div class="animate-spin h-5 w-5 border-2 border-[#4154e8] border-t-transparent rounded-full mb-2"></div>
                                    <span class="text-xs">Memuat notifikasi...</span>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="border-t border-slate-100 px-5 py-3 text-center">
                                <a href="{{ route('notifications') }}" class="text-xs font-bold text-slate-500 hover:text-[#4154e8] transition">
                                    Buka halaman notifikasi penuh &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- USER PROFILE PILL & DROPDOWN (Persis Foto 4 & Foto 5) -->
                    <div class="relative" id="user-menu-wrapper">
                        <!-- Pill Button (Foto 4) -->
                        <button type="button" onclick="toggleUserDropdown()"
                                class="flex items-center gap-2.5 rounded-full bg-white border border-slate-200/80 px-3 py-1.5 shadow-sm hover:bg-slate-50 transition cursor-pointer select-none">
                            <div class="relative shrink-0">
                                @if (!empty($authUser['avatar']))
                                    <img src="{{ $authUser['avatar'] }}" alt="{{ $authUser['name'] }}"
                                         class="h-7 w-7 rounded-full object-cover">
                                @else
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] text-[10px] font-black">
                                        {{ $userInitials }}
                                    </div>
                                @endif
                            </div>
                            <span class="text-xs font-bold text-slate-800 leading-none">
                                {{ $authUser['name'] }}
                            </span>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu (Persis Foto 5) -->
                        <div id="user-menu-dropdown"
                             class="absolute right-0 top-[calc(100%+8px)] z-50 hidden w-[240px] rounded-2xl bg-white shadow-xl border border-slate-100 p-2 overflow-hidden animate-fade-in text-left">
                            <!-- User Info Header (Foto 5) -->
                            <div class="px-3 py-2.5">
                                <p class="font-extrabold text-sm text-slate-900 leading-tight">{{ $authUser['name'] }}</p>
                                <p class="text-[11px] text-slate-400 font-medium truncate mt-0.5">{{ $authUser['email'] ?? 'user@bareng.id' }}</p>
                            </div>

                            <div class="border-t border-slate-100 my-1"></div>

                            <!-- Menu 1: Pengaturan Akun (Foto 5) -->
                            <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Pengaturan akun</span>
                            </a>

                            <!-- Menu 2: Profil Saya -->
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profil saya</span>
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <!-- Menu 3: Keluar (Foto 5: Red "Keluar") -->
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                    <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            @endunless

            <main id="main-content" class="flex-1 {{ $communityPage ? 'px-4 sm:px-6 pb-16 pt-4' : ($immersivePage ? 'p-0' : 'px-4 sm:px-6 pb-16 pt-2') }}">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION -->
    <nav class="fixed inset-x-4 bottom-3 z-40 flex items-center justify-around rounded-3xl bg-[#4154e8] p-2 text-white shadow-2xl lg:hidden">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center rounded-2xl px-2 py-1 text-[10px] font-bold {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'home', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Home</span>
        </a>
        <a href="{{ route('community') }}" class="flex flex-col items-center rounded-2xl px-2 py-1 text-[10px] font-bold {{ request()->routeIs('community') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'forum', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Warga</span>
        </a>
        <a href="{{ route('chat') }}" class="flex flex-col items-center rounded-2xl px-2 py-1 text-[10px] font-bold {{ request()->routeIs('chat') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'mail', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Chat</span>
        </a>
        <a href="{{ route('notifications') }}" class="flex flex-col items-center rounded-2xl px-2 py-1 text-[10px] font-bold {{ request()->routeIs('notifications') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'bell', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Notif</span>
        </a>
        <a href="{{ route('profile') }}" class="flex flex-col items-center rounded-2xl px-2 py-1 text-[10px] font-bold {{ request()->routeIs('profile*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'user', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Profil</span>
        </a>
    </nav>

<script>
// ─── Notification Dropdown Logic ───────────────────────────────────────────
let notifLoaded = false;
let notifOpen = false;

function toggleNotifDropdown() {
    const dropdown = document.getElementById('notif-dropdown');
    const badge = document.getElementById('notif-badge');

    notifOpen = !notifOpen;

    if (notifOpen) {
        dropdown.classList.remove('hidden');

        // Load notifications via AJAX on first open
        if (!notifLoaded) {
            fetch('{{ route("notifications.dropdown") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                notifLoaded = true;
                renderNotifDropdown(data.notifications);

                // Clear badge after opening
                if (badge) badge.classList.add('hidden');
            })
            .catch(() => {
                document.getElementById('notif-list').innerHTML =
                    '<div class="py-8 text-center text-xs text-slate-400">Gagal memuat notifikasi.</div>';
            });
        } else {
            // Already loaded — just clear badge
            if (badge) badge.classList.add('hidden');
        }
    } else {
        dropdown.classList.add('hidden');
    }
}

function renderNotifDropdown(notifs) {
    const list = document.getElementById('notif-list');

    if (!notifs || notifs.length === 0) {
        list.innerHTML = `
            <div class="flex flex-col items-center py-10 text-slate-400">
                <div class="mb-2 flex justify-center">@include('partials.icons', ['name' => 'bell', 'class' => 'h-8 w-8'])</div>
                <p class="text-xs font-bold text-slate-600">Belum ada notifikasi</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Aktivitas tetangga dan sesi bareng akan muncul di sini.</p>
            </div>`;
        return;
    }

    list.innerHTML = notifs.map(n => {
        const initials = n.actor_initials ?? '??';
        const name = n.actor_username ? '@' + n.actor_username : (n.actor_name ?? 'Sistem');
        const isFollow = n.type === 'follow';
        const isLike = n.type === 'like';

        // Pick accent color per type
        const avatarBg = isFollow ? 'from-[#3b49cf] to-[#5b72ff]' :
                         isLike   ? 'from-rose-500 to-pink-400'   :
                                    'from-slate-500 to-slate-400';
        const icon = isFollow
            ? '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m14-9a3 3 0 1 0-6 0 3 3 0 0 0 6 0zM12 7a4 4 0 1 0-8 0 4 4 0 0 0 8 0z"/></svg>'
            : isLike
                ? '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.6-7 10-7 10z"/></svg>'
                : '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0"/></svg>';

        // Right side: Follow button or post thumbnail
        let rightHtml = '';
        if (isFollow && n.actor_id) {
            rightHtml = `
                <button onclick="toggleFollowFromDropdown(${n.actor_id}, this)"
                        class="shrink-0 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-3 py-1.5 text-[11px] font-extrabold text-white transition">
                    Follow
                </button>`;
        } else if (n.post_image) {
            rightHtml = `
                <div class="shrink-0 h-11 w-11 overflow-hidden rounded-xl bg-slate-100 shadow-sm border border-slate-200">
                    <img src="${n.post_image}" class="h-full w-full object-cover">
                </div>`;
        }

        return `
            <div class="flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-slate-50 transition">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr ${avatarBg} text-white text-xs font-black select-none shadow-sm">
                            ${initials}
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[#4154e8] shadow">${icon}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-800 leading-snug truncate max-w-[180px]">
                            <span class="font-extrabold text-slate-900">${name}</span>
                            ${n.message.replace(n.actor_username ?? n.actor_name ?? '', '').trim()}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5 font-semibold">${n.time_ago}</p>
                    </div>
                </div>
                ${rightHtml}
            </div>`;
    }).join('');
}

function toggleFollowFromDropdown(userId, btn) {
    fetch('/users/' + userId + '/toggle-follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
                            document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.following) {
                btn.className = 'shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 px-3 py-1.5 text-[11px] font-extrabold text-slate-700 transition';
                btn.innerText = 'Following';
            } else {
                btn.className = 'shrink-0 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-3 py-1.5 text-[11px] font-extrabold text-white transition';
                btn.innerText = 'Follow';
            }
        }
    });
}

// User Dropdown Logic (Foto 4 & 5)
let userDropdownOpen = false;

function toggleUserDropdown() {
    const dropdown = document.getElementById('user-menu-dropdown');
    userDropdownOpen = !userDropdownOpen;
    if (userDropdownOpen) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const notifWrapper = document.getElementById('notif-wrapper');
    if (notifWrapper && !notifWrapper.contains(e.target) && notifOpen) {
        document.getElementById('notif-dropdown').classList.add('hidden');
        notifOpen = false;
    }

    const userWrapper = document.getElementById('user-menu-wrapper');
    if (userWrapper && !userWrapper.contains(e.target) && userDropdownOpen) {
        document.getElementById('user-menu-dropdown').classList.add('hidden');
        userDropdownOpen = false;
    }
});
</script>

@include('partials.create-post-modal')
@include('partials.sweetalert-modal')
</body>
</html>
