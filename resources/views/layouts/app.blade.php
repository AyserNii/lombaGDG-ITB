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
    <style>
        /* ═══ INSTAGRAM-STYLE HOVER EXPAND SIDEBAR DENGAN CEKUNGAN PUTIH ═══ */
        #main-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 72px; /* Collapsed: icon-only */
            background-color: #4154e8;
            z-index: 50;
            display: none;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 260ms cubic-bezier(0.4, 0, 0.2, 1);
            will-change: width;
        }
        @media (min-width: 1024px) {
            #main-sidebar {
                display: flex;
            }
            /* Spacer tetap 72px agar konten tidak geser saat sidebar hover */
            #sidebar-pusher {
                display: block;
                width: 72px;
                flex-shrink: 0;
            }
        }

        /* Saat HOVER: Melebar menimpa konten (overlay Instagram-style) */
        #main-sidebar:hover {
            width: 240px;
            box-shadow: 6px 0 28px rgba(0,0,0,0.22);
        }

        /* Label teks: tersembunyi saat collapsed, fade-in saat hover */
        #main-sidebar .sidebar-label {
            opacity: 0;
            pointer-events: none;
            transition: opacity 160ms ease 60ms;
            white-space: nowrap;
            overflow: hidden;
            display: inline-block;
        }
        #main-sidebar:hover .sidebar-label {
            opacity: 1;
            pointer-events: auto;
        }

        /* Icon slot: ukuran konsisten 44px di tengah kolom collapsed */
        .sidebar-icon-slot {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-unread-badge {
            position: absolute;
            top: 4px;
            left: 36px;
            z-index: 30;
            display: flex;
            min-width: 18px;
            height: 18px;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid #4154e8;
            border-radius: 9999px;
            background: #ff3040;
            color: #fff;
            font-size: 9px;
            font-weight: 900;
            line-height: 1;
            box-shadow: 0 2px 8px rgba(30, 41, 59, 0.2);
            pointer-events: none;
            transition: left 260ms cubic-bezier(0.4, 0, 0.2, 1), right 260ms cubic-bezier(0.4, 0, 0.2, 1), top 260ms cubic-bezier(0.4, 0, 0.2, 1), transform 260ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        #main-sidebar:hover .sidebar-unread-badge {
            top: 50%;
            right: 12px;
            left: auto;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            transform: translateY(-50%);
        }

        /* Active nav item with inverted curved corners (cekungan putih) */
        .sidebar-curved-active {
            position: relative;
            background-color: #edf1f7;
            color: #1e293b !important;
            font-weight: 800;
            border-top-left-radius: 28px;
            border-bottom-left-radius: 28px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            width: 100%;
            z-index: 10;
        }

        /* Smooth scrollbar for sidebar */
        #main-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #main-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen bg-[#edf1f7] text-[#1e293b] antialiased font-sans">
    @php
        $immersivePage = request()->routeIs('community', 'chat');
        $communityPage = request()->routeIs('community');

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
        $totalNotifCount = $userModel ? \App\Models\Notification::where('user_id', $userModel->id)->count() : 0;
    @endphp
    <div class="flex min-h-screen w-full bg-[#edf1f7]">

        <!-- SIDEBAR DESKTOP — Instagram-style Hover Expand with Curved Active Notch (Cekungan Putih) -->
        <aside id="main-sidebar" class="bg-[#4154e8] text-white">

            <!-- ── Logo ── -->
            <div class="flex h-16 items-center px-3.5 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center group overflow-hidden" title="BARENG">
                    <div class="sidebar-icon-slot rounded-2xl bg-white/15 backdrop-blur-sm shadow-inner group-hover:bg-white/25 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="16 6 68 88" width="26" height="26" role="img" aria-label="Logo BARENG">
                            <path d="M50 6C30 6 16 21 16 40c0 22 34 54 34 54s34-32 34-54C84 21 70 6 50 6Z" fill="rgba(255,255,255,0.22)"/>
                            <g fill="none" stroke="#fff" stroke-width="3.5">
                                <circle cx="42" cy="36" r="9"/>
                                <circle cx="58" cy="36" r="9"/>
                                <circle cx="50" cy="50" r="9"/>
                            </g>
                        </svg>
                    </div>
                    <span class="sidebar-label ml-3 text-lg font-black tracking-tight text-white">BARENG</span>
                </a>
            </div>

            <!-- ── Nav Items ── -->
            <nav class="flex flex-1 flex-col gap-1 pl-3.5 pr-0 py-2">
                @php
                    $navItems = [
                        ['route' => 'dashboard',  'url' => route('dashboard'),  'label' => 'Dashboard',      'icon' => 'home'],
                        ['route' => 'makan',      'url' => route('makan'),      'label' => 'Bareng Makan',   'icon' => 'food'],
                        ['route' => 'belanja',    'url' => route('belanja'),    'label' => 'Bareng Belanja', 'icon' => 'bag'],
                        ['route' => 'kirim',      'url' => route('kirim'),      'label' => 'Bareng Kirim',   'icon' => 'truck'],
                        ['route' => 'sesi',       'url' => route('sesi'),       'label' => 'Sesi Aktif',     'icon' => 'schedule'],
                        ['route' => 'community',  'url' => route('community'),  'label' => 'Komunitas',      'icon' => 'forum'],
                        ['route' => 'chat',       'url' => route('chat'),       'label' => 'Chat DM',        'icon' => 'dm'],
                        ['is_modal' => true, 'action' => 'openCreatePostModal()', 'label' => 'Buat Post',    'icon' => 'plus-square'],
                        ['route' => 'profile',    'url' => route('profile'),    'label' => 'Profil Saya',    'icon' => 'user', 'is_profile' => true],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @if (isset($item['is_modal']) && $item['is_modal'])
                        {{-- Modal trigger (Create Post) --}}
                        <div class="relative w-full pr-2">
                            <button type="button" onclick="{{ $item['action'] }}"
                                    class="group flex w-full items-center gap-3 rounded-2xl px-1 py-1.5 text-xs font-bold text-white/80 transition hover:bg-white/10 hover:text-white">
                                <span class="sidebar-icon-slot text-white/80 group-hover:text-white">
                                    @include('partials.icons', ['name' => $item['icon'], 'class' => 'h-[20px] w-[20px]'])
                                </span>
                                <span class="sidebar-label truncate">{{ $item['label'] }}</span>
                            </button>
                        </div>

                    @else
                        @php
                            $isActive = request()->routeIs($item['route'])
                                     || ($item['route'] === 'profile' && request()->routeIs('profile.*'));
                        @endphp

                        @if ($isActive)
                            {{-- ── ACTIVE NAV ITEM: TAB PUTIH DENGAN CEKUNGAN ATAS & BAWAH ── --}}
                            <div class="relative w-full">
                                <a href="{{ $item['url'] }}"
                                   class="sidebar-curved-active flex items-center justify-between gap-3 px-1 py-1.5 text-xs font-black text-[#1e293b]">
                                    
                                    <!-- Inverted Curve Top (Cekungan Atas) -->
                                    <span class="absolute -top-6 right-0 w-6 h-6 pointer-events-none z-20">
                                        <svg viewBox="0 0 24 24" width="24" height="24" class="fill-[#edf1f7] block">
                                            <path d="M24,0 L24,24 L0,24 A24,24 0 0,0 24,0 Z"/>
                                        </svg>
                                    </span>

                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="sidebar-icon-slot text-[#4154e8]">
                                            @if (!empty($item['is_profile']))
                                                @if (!empty($authUser['avatar']))
                                                    <span class="relative block h-6 w-6">
                                                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-[#4154e8] text-[9px] font-black text-white">{{ $userInitials }}</span>
                                                        <img src="{{ $authUser['avatar'] }}" alt="{{ $authUser['name'] }}" onerror="this.remove()"
                                                             class="relative h-6 w-6 rounded-full object-cover ring-2 ring-[#4154e8]">
                                                    </span>
                                                @else
                                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#4154e8] text-white text-[9px] font-black">{{ $userInitials }}</div>
                                                @endif
                                            @else
                                                @include('partials.icons', ['name' => $item['icon'], 'class' => 'h-[20px] w-[20px]'])
                                            @endif
                                        </span>
                                        <span class="sidebar-label font-extrabold truncate">{{ $item['label'] }}</span>
                                    </div>

                                    <!-- Inverted Curve Bottom (Cekungan Bawah — SWEEP FLAG 1) -->
                                    <span class="absolute -bottom-6 right-0 w-6 h-6 pointer-events-none z-20">
                                        <svg viewBox="0 0 24 24" width="24" height="24" class="fill-[#edf1f7] block">
                                            <path d="M24,24 L24,0 L0,0 A24,24 0 0,1 24,24 Z"/>
                                        </svg>
                                    </span>
                                </a>
                                @if(($item['route'] ?? '') === 'chat' && $unreadMsgCount > 0)
                                    <span class="sidebar-unread-badge" role="status" aria-label="{{ $unreadMsgCount }} pesan belum dibaca" title="{{ $unreadMsgCount }} pesan belum dibaca">
                                        {{ $unreadMsgCount > 9 ? '9+' : $unreadMsgCount }}
                                    </span>
                                @endif
                            </div>

                        @else
                            {{-- ── NORMAL ITEM ── --}}
                            <div class="relative w-full pr-2">
                                <a href="{{ $item['url'] }}"
                                   class="group flex items-center justify-between gap-3 rounded-2xl px-1 py-1.5 text-xs font-bold text-white/80 transition hover:bg-white/10 hover:text-white">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="sidebar-icon-slot text-white/80 group-hover:text-white">
                                            @if (!empty($item['is_profile']))
                                                @if (!empty($authUser['avatar']))
                                                    <span class="relative block h-6 w-6">
                                                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-white/20 text-[9px] font-black text-white">{{ $userInitials }}</span>
                                                        <img src="{{ $authUser['avatar'] }}" alt="{{ $authUser['name'] }}" onerror="this.remove()"
                                                             class="relative h-6 w-6 rounded-full object-cover ring-2 ring-white/30">
                                                    </span>
                                                @else
                                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-white text-[9px] font-black">{{ $userInitials }}</div>
                                                @endif
                                            @else
                                                @include('partials.icons', ['name' => $item['icon'], 'class' => 'h-[20px] w-[20px]'])
                                            @endif
                                        </span>
                                        <span class="sidebar-label truncate">{{ $item['label'] }}</span>
                                    </div>

                                </a>
                                @if(($item['route'] ?? '') === 'chat' && $unreadMsgCount > 0)
                                    <span class="sidebar-unread-badge" role="status" aria-label="{{ $unreadMsgCount }} pesan belum dibaca" title="{{ $unreadMsgCount }} pesan belum dibaca">
                                        {{ $unreadMsgCount > 9 ? '9+' : $unreadMsgCount }}
                                    </span>
                                @endif
                            </div>
                        @endif

                    @endif
                @endforeach
            </nav>

            <!-- ── Bottom: Logout ── -->
            <div class="px-3.5 py-4 mt-auto shrink-0 border-t border-white/10">
                <button type="button"
                        onclick="openLogoutModal()"
                        class="group flex w-full items-center gap-3 rounded-2xl px-1 py-1.5 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white cursor-pointer"
                        title="Keluar dari akun">
                    <span class="sidebar-icon-slot text-white/70 group-hover:text-white">
                        @include('partials.icons', ['name' => 'logout', 'class' => 'h-[20px] w-[20px]'])
                    </span>
                    <span class="sidebar-label">Log Out</span>
                </button>
            </div>

        </aside>

        <!-- Invisible spacer that reserves the 72px sidebar width in the flex flow -->
        <div id="sidebar-pusher" class="hidden lg:block"></div>

        <!-- MAIN CONTENT — takes remaining space, seamless with curved active tab -->
        <div class="flex min-w-0 flex-1 flex-col min-h-screen bg-[#edf1f7]">
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
                    <a href="{{ route('settings') }}" class="hidden sm:flex items-center gap-1.5 rounded-full bg-white px-3.5 py-2 shadow-sm text-xs font-bold text-[#1e293b] hover:text-[#4154e8]">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>{{ ($authUser['location'] ?? '') === 'Belum diatur' ? 'Atur lokasi' : ($authUser['location'] ?? 'Atur lokasi') }}</span>
                        @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5 text-[#718096]'])
                    </a>

                    <!-- Notifications Bell → Dropdown (white theme) -->
                    <div class="relative" id="notif-wrapper">
                        <button type="button"
                                onclick="event.stopPropagation(); toggleNotifDropdown(event)"
                                class="group relative flex h-11 w-11 items-center justify-center rounded-2xl border border-white bg-white text-[#4154e8] shadow-sm transition hover:bg-indigo-50 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#4154e8]/40"
                                title="Notifikasi"
                                id="notif-bell-btn"
                                aria-label="Buka notifikasi"
                                aria-haspopup="true"
                                aria-controls="notif-dropdown"
                                aria-expanded="false">
                            @include('partials.icons', ['name' => 'bell-ring', 'class' => 'h-5 w-5 pointer-events-none transition-transform group-hover:-rotate-6'])
                            @if ($unreadNotifCount > 0)
                                <span id="notif-badge" class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-black text-white ring-2 ring-[#edf1f7] pointer-events-none">
                                    {{ $unreadNotifCount }}
                                </span>
                            @else
                                <span id="notif-badge" class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-[#edf1f7] hidden pointer-events-none"></span>
                            @endif
                        </button>

                        <div id="notif-dropdown"
                             style="display: none;"
                             onclick="event.stopPropagation()"
                             class="absolute right-0 top-[calc(100%+12px)] z-[60] w-[calc(100vw-2rem)] overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_18px_60px_rgba(30,41,59,0.18)] animate-fade-in sm:w-[400px]">

                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-gradient-to-br from-indigo-50/90 via-white to-white px-4 py-4 sm:px-5">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-extrabold text-slate-900">Notifikasi</h3>
                                    <p id="notif-summary" class="mt-0.5 truncate text-[11px] font-medium text-slate-500">
                                        {{ $unreadNotifCount > 0 ? $unreadNotifCount.' notifikasi baru' : 'Kabar terbaru dari aktivitasmu' }}
                                    </p>
                                </div>
                                <button id="notif-delete-all-btn"
                                        type="button"
                                        onclick="deleteAllNotifications(this)"
                                        {{ $totalNotifCount === 0 ? 'hidden' : '' }}
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-2.5 py-2 text-[11px] font-extrabold text-rose-600 transition hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-300">
                                    @include('partials.icons', ['name' => 'trash', 'class' => 'h-3.5 w-3.5'])
                                    Hapus semua
                                </button>
                            </div>

                            <div id="notif-list" class="max-h-[min(60vh,440px)] overflow-y-auto overscroll-contain divide-y divide-slate-100/80">
                                <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                                    <div class="mb-2 h-6 w-6 animate-spin rounded-full border-2 border-[#4154e8] border-t-transparent"></div>
                                    <span class="text-xs font-medium">Memuat aktivitas terbaru...</span>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 bg-slate-50/70 px-4 py-3 text-center">
                                <a href="{{ route('notifications') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 transition hover:text-[#4154e8]">
                                    Buka pusat notifikasi
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- USER PROFILE PILL & DROPDOWN (Persis Foto 4 & Foto 5) -->
                    <div class="relative" id="user-menu-wrapper">
                        <!-- Pill Button (Foto 4) -->
                        <button type="button"
                                id="user-menu-btn"
                                onclick="event.stopPropagation(); toggleUserDropdown(event)"
                                class="flex items-center gap-2.5 rounded-full bg-white border border-slate-200/80 px-3 py-1.5 shadow-sm hover:bg-slate-50 transition cursor-pointer select-none">
                            <div class="relative shrink-0 pointer-events-none">
                                @if (!empty($authUser['avatar']))
                                    <span class="relative block h-7 w-7">
                                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-slate-100 text-[10px] font-black text-[#4154e8]">{{ $userInitials }}</span>
                                        <img src="{{ $authUser['avatar'] }}" alt="{{ $authUser['name'] }}" onerror="this.remove()"
                                             class="relative h-7 w-7 rounded-full object-cover">
                                    </span>
                                @else
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-[#4154e8] text-[10px] font-black">
                                        {{ $userInitials }}
                                    </div>
                                @endif
                            </div>
                            <span class="text-xs font-bold text-slate-800 leading-none pointer-events-none">
                                {{ $authUser['name'] }}
                            </span>
                            <svg class="h-3.5 w-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu (Persis Foto 5 tanpa Sertifikat) -->
                        <div id="user-menu-dropdown"
                             style="display: none;"
                             onclick="event.stopPropagation()"
                             class="absolute right-0 top-[calc(100%+8px)] z-[60] w-[240px] rounded-2xl bg-white shadow-2xl border border-slate-100 p-2 overflow-hidden animate-fade-in text-left">
                            <!-- User Info Header (Foto 5) -->
                            <div class="px-3 py-2.5">
                                <p class="font-extrabold text-sm text-slate-900 leading-tight">{{ $authUser['name'] }}</p>
                                <p class="text-[11px] text-slate-400 font-medium truncate mt-0.5">{{ $authUser['email'] ?? 'user@bareng.id' }}</p>
                            </div>

                            <div class="border-t border-slate-100 my-1"></div>

                            <!-- Menu 1: Pengaturan Akun (Foto 5) -->
                            <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-[#4154e8] transition">
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Pengaturan akun</span>
                            </a>

                            <!-- Menu 2: Profil Saya (Pengganti Sertifikat saya) -->
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-[#4154e8] transition">
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profil saya</span>
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <!-- Menu 3: Keluar -->
                            <button type="button"
                                    onclick="openLogoutModal()"
                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
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
            @include('partials.icons', ['name' => 'bell-ring', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Notif</span>
        </a>
        <a href="{{ route('profile') }}" class="flex flex-col items-center rounded-2xl px-2 py-1 text-[10px] font-bold {{ request()->routeIs('profile*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white' }}">
            @include('partials.icons', ['name' => 'user', 'class' => 'h-4 w-4 mb-0.5'])
            <span>Profil</span>
        </a>
    </nav>

<script>
// ─── Notification Dropdown Logic ───────────────────────────────────────────
let notifOpen = false;

function startButtonLoading(button, label = 'Memproses...') {
    if (button.disabled) return false;

    button.dataset.originalHtml = button.innerHTML;
    button.dataset.originalClass = button.className;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.classList.add('cursor-wait', 'opacity-70');
    button.innerHTML = `<span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-r-transparent"></span><span>${label}</span>`;

    return true;
}

function finishButtonLoading(button) {
    button.disabled = false;
    button.removeAttribute('aria-busy');
    button.innerHTML = button.dataset.originalHtml || button.innerHTML;
    button.className = button.dataset.originalClass || button.className;
    delete button.dataset.originalHtml;
    delete button.dataset.originalClass;
}

function restoreButtonAfterFailure(button) {
    finishButtonLoading(button);
}

function updateNotificationCount(count) {
    const badge = document.getElementById('notif-badge');
    const summary = document.getElementById('notif-summary');
    if (!badge) return;

    const unreadCount = Number(count) || 0;
    if (unreadCount > 0) {
        badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
        badge.className = 'absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-black text-white ring-2 ring-[#edf1f7] pointer-events-none';
    } else {
        badge.textContent = '';
        badge.className = 'absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-[#edf1f7] hidden pointer-events-none';
    }

    if (summary) {
        summary.textContent = unreadCount > 0
            ? `${unreadCount} notifikasi baru`
            : 'Kabar terbaru dari aktivitasmu';
    }
}

function toggleNotifDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const dropdown = document.getElementById('notif-dropdown');
    const button = document.getElementById('notif-bell-btn');
    if (!dropdown) return;

    // Close user dropdown if open
    const userDropdown = document.getElementById('user-menu-dropdown');
    if (userDropdown) {
        userDropdown.classList.add('hidden');
        userDropdown.style.display = 'none';
        userDropdownOpen = false;
        const notifButton = document.getElementById('notif-bell-btn');
        if (notifButton) notifButton.setAttribute('aria-expanded', 'false');
    }

    const isHidden = dropdown.style.display === 'none' || dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        dropdown.style.display = 'block';
        notifOpen = true;
        if (button) button.setAttribute('aria-expanded', 'true');

        document.getElementById('notif-list').innerHTML = `
            <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                <div class="mb-2 h-6 w-6 animate-spin rounded-full border-2 border-[#4154e8] border-t-transparent"></div>
                <span class="text-xs font-medium">Memuat aktivitas terbaru...</span>
            </div>`;

        fetch('{{ route("notifications.dropdown") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load notifications.');
            return response.json();
        })
        .then(data => {
            renderNotifDropdown(data.notifications);
            updateNotificationCount(data.unread_count);
        })
        .catch(() => {
            document.getElementById('notif-list').innerHTML =
                '<div class="px-5 py-10 text-center"><div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-500"><span class="text-xl font-bold">!</span></div><p class="text-xs font-bold text-slate-700">Notifikasi belum bisa dimuat</p><p class="mt-1 text-[11px] text-slate-400">Coba buka lagi sebentar lagi.</p></div>';
        });
    } else {
        dropdown.classList.add('hidden');
        dropdown.style.display = 'none';
        notifOpen = false;
        if (button) button.setAttribute('aria-expanded', 'false');
    }
}

function renderNotifDropdown(notifs) {
    const list = document.getElementById('notif-list');
    const deleteAllButton = document.getElementById('notif-delete-all-btn');
    if (deleteAllButton) deleteAllButton.hidden = !notifs || notifs.length === 0;

    if (!notifs || notifs.length === 0) {
        list.innerHTML = `
            <div class="flex flex-col items-center px-6 py-10 text-center">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-[20px] bg-indigo-50 text-[#4154e8]">@include('partials.icons', ['name' => 'bell-ring', 'class' => 'h-7 w-7'])</div>
                <p class="text-xs font-extrabold text-slate-700">Belum ada notifikasi</p>
                <p class="mt-1 max-w-[250px] text-[11px] leading-relaxed text-slate-400">Aktivitas, pesan, suka, dan komentar dari tetangga akan muncul di sini.</p>
            </div>`;
        return;
    }

    list.innerHTML = notifs.map(n => {
        const initials = n.actor_initials ?? '??';
        const name = n.actor_username ? '@' + n.actor_username : (n.actor_name ?? 'Sistem');
        const isFollow = n.type === 'follow';
        const isLike = n.type === 'like';
        const isComment = n.type === 'comment';
        const isMessage = n.type === 'message';

        const icon = isFollow
            ? '<svg class="h-3.5 w-3.5 text-[#4154e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m14-9a3 3 0 1 0-6 0 3 3 0 0 0 6 0zM12 7a4 4 0 1 0-8 0 4 4 0 0 0 8 0z"/></svg>'
            : isLike
                ? '<svg class="h-3.5 w-3.5 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'
                : isMessage
                    ? '<svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>'
                    : '<svg class="h-3.5 w-3.5 text-[#4154e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>';
        const iconBadgeTone = isLike
            ? 'bg-rose-100'
            : isMessage
                ? 'bg-emerald-100'
                : 'bg-indigo-100';

        const avatarHtml = n.actor_avatar
            ? `<span class="relative block h-10 w-10 shrink-0"><span class="absolute inset-0 flex items-center justify-center rounded-full bg-[#4154e8] text-xs font-black text-white">${escapeNotificationHtml(initials)}</span><img src="${escapeNotificationHtml(n.actor_avatar)}" alt="" onerror="this.remove()" class="relative h-10 w-10 rounded-full object-cover shadow-sm"></span>`
            : `<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#4154e8] text-white text-xs font-black select-none shadow-sm">${escapeNotificationHtml(initials)}</div>`;

        const actionText = isFollow
            ? 'mulai mengikuti kamu.'
            : isLike
                ? 'menyukai postingan kamu.'
                : isComment
                    ? 'mengomentari postingan kamu.'
                    : n.message;
        const postPreview = n.post_id && (isLike || isComment)
            ? `<button type="button" onclick="openPostDetailModal(${Number(n.post_id)})" class="mt-2 flex w-full items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 p-2 text-left hover:bg-indigo-50">${n.post_image ? `<img src="${escapeNotificationHtml(n.post_image)}" alt="" class="h-9 w-9 shrink-0 rounded-lg object-cover">` : ''}<span class="min-w-0"><span class="block truncate text-[10px] font-bold text-slate-500">Postingan: ${escapeNotificationHtml(n.post_caption || 'Foto komunitas')}</span>${isComment && n.comment_body ? `<span class="mt-0.5 block line-clamp-2 text-[10px] text-slate-700">Komentar: ${escapeNotificationHtml(n.comment_body)}</span>` : ''}</span></button>`
            : '';

        let rightHtml = '';
        if (isFollow && n.actor_id) {
            rightHtml = `
                <button onclick="toggleFollowFromDropdown(${n.actor_id}, this)"
                        class="shrink-0 rounded-xl ${n.is_following ? 'bg-slate-100 text-slate-800 hover:bg-slate-200' : 'bg-[#4154e8] text-white hover:bg-[#3243d4]'} px-3.5 py-1.5 text-[11px] font-extrabold transition">
                    ${n.is_following ? 'Following' : (n.follows_you ? 'Follow back' : 'Follow')}
                </button>`;
        } else if (isMessage && n.chat_url) {
            rightHtml = `
                <a href="${escapeNotificationHtml(n.chat_url)}" class="shrink-0 rounded-xl bg-[#4154e8] px-3 py-2 text-[11px] font-extrabold text-white hover:bg-[#3243d4]">Balas</a>`;
        }

        return `
            <div class="flex items-start gap-3 px-4 py-4 transition ${n.is_read ? 'bg-white' : 'bg-indigo-50/55'} hover:bg-indigo-50/80 sm:px-5">
                <div class="relative mt-0.5 shrink-0">
                    ${avatarHtml}
                    <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full ${iconBadgeTone} ring-2 ring-white">${icon}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs leading-relaxed text-slate-700">
                        <a href="${escapeNotificationHtml(n.actor_url || '#')}" class="font-extrabold text-slate-900 transition hover:text-[#4154e8]">${escapeNotificationHtml(name)}</a>
                        <span>${escapeNotificationHtml(actionText)}</span>
                    </p>
                    <p class="mt-1 text-[10px] font-semibold text-slate-400">${escapeNotificationHtml(n.time_ago)}</p>
                    ${postPreview}
                    ${rightHtml ? `<div class="mt-2">${rightHtml}</div>` : ''}
                </div>
            </div>`;
    }).join('');
}

function escapeNotificationHtml(value) {
    const element = document.createElement('span');
    element.textContent = value ?? '';
    return element.innerHTML;
}

function toggleFollowFromDropdown(userId, btn) {
    if (!startButtonLoading(btn, 'Memproses...')) return;

    fetch('/users/' + userId + '/toggle-follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
                            document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Follow request failed.');
        return response.json();
    })
    .then(data => {
        if (!data.success) throw new Error(data.error || 'Follow request was not successful.');

        finishButtonLoading(btn);
        btn.className = data.following
            ? 'shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 px-3 py-1.5 text-[11px] font-extrabold text-slate-700 transition'
            : 'shrink-0 rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-3 py-1.5 text-[11px] font-extrabold text-white transition';
        btn.innerText = data.following ? 'Following' : (data.follows_you ? 'Follow back' : 'Follow');
        btn.dataset.followsYou = data.follows_you ? 'true' : 'false';
    })
    .catch(error => {
        restoreButtonAfterFailure(btn);
        if (window.showSweetAlert) {
            showSweetAlert('Aksi gagal', 'Status follow belum berhasil diperbarui. Coba lagi.', 'error');
        } else {
            alert('Status follow belum berhasil diperbarui. Coba lagi.');
        }
        console.error(error);
    });
}

function deleteAllNotifications(button) {
    if (button.disabled || !confirm('Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) return;
    if (!startButtonLoading(button, 'Menghapus...')) return;

    fetch('{{ route("notifications.destroy-all") }}', {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Delete notifications request failed.');
        return response.json();
    })
    .then(data => {
        if (!data.success) throw new Error('Notifications could not be deleted.');

        finishButtonLoading(button);
        renderNotifDropdown([]);
        updateNotificationCount(0);
        if (window.showSweetAlert) {
            showSweetAlert('Notifikasi dihapus', 'Semua notifikasi berhasil dihapus.', 'success');
        }
    })
    .catch(error => {
        restoreButtonAfterFailure(button);
        if (window.showSweetAlert) {
            showSweetAlert('Gagal menghapus', 'Notifikasi belum berhasil dihapus. Coba lagi.', 'error');
        } else {
            alert('Notifikasi belum berhasil dihapus. Coba lagi.');
        }
        console.error(error);
    });
}

// User Dropdown Logic (Foto 4 & 5)
let userDropdownOpen = false;

function toggleUserDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const dropdown = document.getElementById('user-menu-dropdown');
    if (!dropdown) return;

    // Close notif dropdown if open
    const notifDropdown = document.getElementById('notif-dropdown');
    if (notifDropdown) {
        notifDropdown.classList.add('hidden');
        notifDropdown.style.display = 'none';
        notifOpen = false;
        const notifButton = document.getElementById('notif-bell-btn');
        if (notifButton) notifButton.setAttribute('aria-expanded', 'false');
    }

    const isHidden = dropdown.style.display === 'none' || dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        dropdown.style.display = 'block';
        userDropdownOpen = true;
    } else {
        dropdown.classList.add('hidden');
        dropdown.style.display = 'none';
        userDropdownOpen = false;
    }
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const notifWrapper = document.getElementById('notif-wrapper');
    const notifDropdown = document.getElementById('notif-dropdown');
    if (notifWrapper && !notifWrapper.contains(e.target)) {
        if (notifDropdown) {
            notifDropdown.classList.add('hidden');
            notifDropdown.style.display = 'none';
        }
        notifOpen = false;
        const notifButton = document.getElementById('notif-bell-btn');
        if (notifButton) notifButton.setAttribute('aria-expanded', 'false');
    }

    const userWrapper = document.getElementById('user-menu-wrapper');
    const userDropdown = document.getElementById('user-menu-dropdown');
    if (userWrapper && !userWrapper.contains(e.target)) {
        if (userDropdown) {
            userDropdown.classList.add('hidden');
            userDropdown.style.display = 'none';
        }
        userDropdownOpen = false;
    }
});
</script>

@include('partials.create-post-modal')
@include('partials.post-detail-modal')
@include('partials.sweetalert-modal')
@include('partials.logout-modal')
</body>
</html>
