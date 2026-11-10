@extends('layouts.app')

@section('title', 'Pesan · BARENG')

@section('content')
@php
    $rawAuthName = trim($authUser->name ?? 'User');
    $authInitials = strtoupper(substr($rawAuthName, 0, 2));
@endphp

{{-- Full-size Instagram-style layout: full height and full width --}}
<div class="h-screen w-full flex overflow-hidden bg-white text-slate-900">

    <!-- ═══════════════════════════════════════════════════════════
         LEFT SIDEBAR (Instagram Style, Clean & Sesuai Permintaan)
    ═══════════════════════════════════════════════════════════ -->
    <aside class="flex w-full shrink-0 flex-col border-r border-slate-200 bg-white sm:w-[350px]">

        <!-- TOP BAR: Username Saja (Tanpa Chevron & Tanpa Icon Pen/Search Dobel) -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <span class="text-base font-extrabold tracking-tight text-slate-900 select-none">
                {{ $authUser->username ?? $authUser->name ?? 'Pesan' }}
            </span>
        </div>

        <!-- SEARCH INPUT (Satu-satunya Input Search, Tanpa Icon Dobel di Atas) -->
        <div class="px-5 py-2.5">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                </span>
                <input type="text" id="chat-search-input"
                       placeholder="Search"
                       oninput="filterChatContacts(this.value)"
                       class="w-full rounded-2xl bg-[#efefef] py-2 pl-10 pr-8 text-xs font-medium text-slate-800 placeholder:text-slate-400 outline-none focus:bg-slate-200/70 transition">
                <button type="button" onclick="clearChatSearch()" id="chat-search-clear"
                        class="hidden absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                    &times;
                </button>
            </div>
        </div>

        <!-- NOTES TRAY (Your note & Friends notes, padding lega agar tidak terpotong) -->
        <div class="px-5 pt-5 pb-3 border-b border-slate-100">
            <div class="flex gap-4 overflow-x-auto pb-1 no-scrollbar">
                <!-- Your Note (Klik untuk buat/ubah note) -->
                <div class="flex flex-col items-center shrink-0 w-16 text-center cursor-pointer group"
                     onclick="openChatNoteModal()" title="Bagikan catatan status">
                    <div class="relative mb-1 flex flex-col items-center">
                        {{-- Bubble Catatan (di atas avatar dengan ekor, tidak terpotong) --}}
                        <div id="chat-my-note-bubble"
                             class="relative max-w-[76px] truncate rounded-xl border border-slate-200 bg-white px-2 py-0.5 text-[9px] font-semibold text-slate-700 shadow-sm group-hover:border-[#0095f6] group-hover:scale-105 transition select-none">
                            {{ $authUser->note ?: 'Your note' }}
                            <span class="absolute -bottom-[5px] left-1/2 -translate-x-1/2 h-[6px] w-[6px] rotate-45 bg-white border-r border-b border-slate-200"></span>
                        </div>

                        <div class="relative mt-1">
                            @if ($authUser->avatar)
                                <img src="{{ $authUser->avatar }}" class="h-13 w-13 rounded-full object-cover ring-2 ring-slate-200 group-hover:ring-[#0095f6] transition">
                            @else
                                <div class="flex h-13 w-13 items-center justify-center rounded-full bg-[#0095f6] text-white text-xs font-black ring-2 ring-slate-200">
                                    {{ $authInitials }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-[#0095f6] text-white text-[10px] font-bold shadow ring-2 ring-white">
                                +
                            </span>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium truncate w-full mt-1">Your note</span>
                </div>

                <!-- Friends Notes -->
                @forelse ($notes as $fNote)
                    <div class="flex flex-col items-center shrink-0 w-16 text-center cursor-pointer group"
                         onclick="window.location.href='{{ route('chat', ['user' => $fNote->username ?? $fNote->id]) }}'"
                         title="Obrolan dengan {{ $fNote->name }}">
                        <div class="relative mb-1 flex flex-col items-center">
                            <div class="relative max-w-[76px] truncate rounded-xl border border-slate-200 bg-white px-2 py-0.5 text-[9px] font-semibold text-slate-600 shadow-sm group-hover:border-[#0095f6] transition select-none">
                                {{ Str::limit($fNote->note, 12) }}
                                <span class="absolute -bottom-[5px] left-1/2 -translate-x-1/2 h-[6px] w-[6px] rotate-45 bg-white border-r border-b border-slate-200"></span>
                            </div>
                            <div class="relative mt-1">
                                @if ($fNote->avatar)
                                    <img src="{{ $fNote->avatar }}" class="h-13 w-13 rounded-full object-cover ring-2 ring-slate-200 group-hover:ring-[#0095f6] transition">
                                @else
                                    <div class="flex h-13 w-13 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-xs font-bold ring-2 ring-slate-200 group-hover:ring-[#0095f6] transition">
                                        {{ strtoupper(substr($fNote->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-700 font-medium truncate w-full mt-1">{{ explode(' ', $fNote->name)[0] }}</span>
                    </div>
                @empty
                @endforelse
            </div>
        </div>

        <!-- MESSAGES SUBHEADER (Pesan / Permintaan) -->
        <div class="flex items-center justify-between px-6 pt-3 pb-2 text-xs font-bold text-slate-400">
            <span class="text-slate-900 font-extrabold text-sm">Pesan</span>
            <span class="text-xs font-semibold text-slate-400 cursor-pointer hover:text-slate-700 transition">Permintaan</span>
        </div>

        <!-- CONTACTS THREADS LIST -->
        <div id="contacts-container" class="flex-1 overflow-y-auto divide-y divide-slate-50">
            @forelse ($contacts as $c)
                @php $isActive = $activeUser && $activeUser->id === $c['id']; @endphp
                <a href="{{ route('chat', ['user' => $c['username'] ?? $c['id']]) }}"
                   id="contact-item-{{ $c['id'] }}"
                   class="contact-item flex items-center justify-between px-5 py-3 transition {{ $isActive ? 'bg-slate-100' : 'hover:bg-slate-50' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative shrink-0">
                            @if ($c['avatar'])
                                <img src="{{ $c['avatar'] }}" alt="{{ $c['name'] }}" class="h-12 w-12 rounded-full object-cover">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#0095f6] text-white text-sm font-black">
                                    {{ $c['initials'] }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        </div>

                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $c['name'] }}</h4>
                            <p id="contact-preview-{{ $c['id'] }}"
                               class="text-[11px] {{ $c['unread'] ? 'text-slate-900 font-bold' : 'text-slate-500' }} truncate mt-0.5 max-w-[170px]">
                                {{ $c['is_outgoing'] ? 'Anda: ' : '' }}{{ $c['last_message'] ?: 'Mulai obrolan' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 pl-2">
                        <span id="contact-time-{{ $c['id'] }}" class="text-[10px] text-slate-400">{{ $c['last_time'] }}</span>
                        @if ($c['unread'])
                            <span class="h-2 w-2 rounded-full bg-[#0095f6] mt-1"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
                    Belum ada riwayat pesan.
                </div>
            @endforelse
        </div>
    </aside>

    <!-- ═══════════════════════════════════════════════════════════
         RIGHT PANEL: CHAT ROOM (Instagram Web Style)
    ═══════════════════════════════════════════════════════════ -->
    <main class="hidden sm:flex flex-1 flex-col bg-white">
        @if ($activeUser)
            @php
                $rawActiveName = trim($activeUser->name ?? 'User');
                $activeInitials = strtoupper(substr($rawActiveName, 0, 2));
            @endphp

            <!-- HEADER RUANG OBROLAN -->
            <header class="flex items-center justify-between border-b border-slate-200 px-6 py-3.5 bg-white">
                <a href="{{ route('profile.show', $activeUser->username ?? $activeUser->id) }}" class="flex items-center gap-3 group">
                    <div class="relative shrink-0">
                        @if ($activeUser->avatar)
                            <img src="{{ $activeUser->avatar }}" class="h-11 w-11 rounded-full object-cover">
                        @else
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-xs font-black">
                                {{ $activeInitials }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-[#0095f6] transition leading-tight">
                            {{ $activeUser->name }}
                        </h3>
                        <p class="text-[11px] text-slate-400 font-medium">
                            {{ $activeUser->username ? '@' . $activeUser->username : '' }} · Aktif sekarang
                        </p>
                    </div>
                </a>

                <!-- ICON TELEPON, VIDEO, DAN INFO AKUN (Mirip Instagram) -->
                <div class="flex items-center gap-3 text-slate-700">
                    <button type="button" onclick="alert('Panggilan suara gotong royong segera hadir!')" class="p-2 rounded-full hover:bg-slate-100 transition" title="Telepon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="alert('Video call gotong royong segera hadir!')" class="p-2 rounded-full hover:bg-slate-100 transition" title="Video Call">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    <a href="{{ route('profile.show', $activeUser->username ?? $activeUser->id) }}" class="p-2 rounded-full hover:bg-slate-100 transition" title="Info Akun">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- MESSAGES CONTAINER -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                <!-- Target User Hero Card (Instagram style) -->
                <div class="flex flex-col items-center py-6 text-center">
                    @if ($activeUser->avatar)
                        <img src="{{ $activeUser->avatar }}" class="h-20 w-20 rounded-full object-cover shadow-md mb-2">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-2xl font-black mb-2">
                            {{ $activeInitials }}
                        </div>
                    @endif
                    <h4 class="text-base font-extrabold text-slate-900">{{ $activeUser->name }}</h4>
                    <p class="text-xs text-slate-400">{{ $activeUser->username ? '@' . $activeUser->username : '' }} · BARENG Community</p>
                    <a href="{{ route('profile.show', $activeUser->username ?? $activeUser->id) }}"
                       class="mt-3 rounded-xl bg-slate-100 px-4 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                        Lihat profil
                    </a>
                </div>

                <!-- Message Bubbles -->
                @foreach ($messages as $msg)
                    @php
                        $isMe = $msg->sender_id === $authUser->id;
                        $isRead = (bool) ($msg->is_read ?? false);
                    @endphp
                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}" data-message-id="{{ $msg->id }}">
                        @if ($msg->media_url)
                            <div class="mb-1.5 max-w-xs sm:max-w-sm rounded-2xl overflow-hidden shadow-md">
                                <img src="{{ $msg->media_url }}" alt="Media" class="w-full h-auto object-cover rounded-2xl">
                            </div>
                        @endif

                        @if (!empty($msg->message))
                            <div class="max-w-xs sm:max-w-md rounded-3xl px-4 py-2.5 text-xs sm:text-sm font-medium leading-relaxed shadow-sm {{ $isMe ? 'bg-[#0095f6] text-white rounded-br-sm' : 'bg-[#efefef] text-slate-900 rounded-bl-sm' }}">
                                {{ $msg->message }}
                            </div>
                        @endif

                        {{-- Waktu & Tanda Ceklis Dua (Abu-abu = Terkirim, Biru = Sudah Dibaca) --}}
                        <div class="flex items-center gap-1 mt-1 px-1">
                            <span class="text-[9px] text-slate-400">{{ $msg->created_at->format('H:i') }}</span>
                            @if ($isMe)
                                @if ($isRead)
                                    {{-- DUA CEKLIS BIRU (SUDAH DIBACA) --}}
                                    <span class="inline-flex items-center text-[#0095f6]" title="Sudah dibaca">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 15" fill="none">
                                            <path d="M15.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0l-3.54-3.54a.75.75 0 011.06-1.06l3.01 3.01 7.177-7.177a.75.75 0 011.06 1.061z" fill="currentColor"/>
                                            <path d="M11.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0L.97 9.75a.75.75 0 111.06-1.06l.713.712 7.207-7.207a.75.75 0 011.06 1.061z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                @else
                                    {{-- DUA CEKLIS ABU-ABU (TERKIRIM & AKTIF) --}}
                                    <span class="inline-flex items-center text-slate-400" title="Terkirim & aktif">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 15" fill="none">
                                            <path d="M15.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0l-3.54-3.54a.75.75 0 011.06-1.06l3.01 3.01 7.177-7.177a.75.75 0 011.06 1.061z" fill="currentColor"/>
                                            <path d="M11.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0L.97 9.75a.75.75 0 111.06-1.06l.713.712 7.207-7.207a.75.75 0 011.06 1.061z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- INPUT FOOTER (Sleek Instagram style) -->
            <footer class="p-4 border-t border-slate-200 bg-white">
                <form id="chat-form" onsubmit="submitChatMessage(event)"
                      class="relative flex items-center gap-3 rounded-full border border-slate-300 bg-white px-4 py-2 focus-within:border-[#0095f6] transition">

                    <!-- Attach photo / media -->
                    <button type="button" onclick="promptMediaUrl()" class="text-slate-500 hover:text-slate-800 transition" title="Kirim gambar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </button>

                    <input type="text" id="chat-message-input" placeholder="Message..." required autocomplete="off"
                           class="flex-1 bg-transparent text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 outline-none">

                    <!-- Send Button -->
                    <button type="submit" class="font-extrabold text-xs text-[#0095f6] hover:text-[#0077c5] transition px-2 py-1 select-none">
                        Kirim
                    </button>
                </form>
            </footer>
        @else
            <!-- No active conversation selected -->
            <div class="flex flex-1 flex-col items-center justify-center p-8 text-center text-slate-400">
                <div class="flex h-24 w-24 items-center justify-center rounded-full border-2 border-slate-800 text-slate-800 mb-4">
                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-900">Pesan Anda</h3>
                <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed">
                    Kirim pesan privat, ajak patungan sesi bareng, atau koordinasi dengan tetangga sekitar.
                </p>
            </div>
        @endif
    </main>
</div>

<!-- Modal: Edit Status Note (Bisa dipakai di Chat & Tersinkron ke Profil) -->
<div id="chat-note-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl border border-slate-100 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Catatan Status Anda</h3>
            <button type="button" onclick="closeChatNoteModal()" class="p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="chat-note-form" onsubmit="submitChatNote(event)" class="mt-4 space-y-4">
            <div>
                <input type="text" id="chat-note-input" value="{{ $authUser->note }}" maxlength="60"
                       placeholder="Bagikan status singkat…"
                       oninput="updateChatNoteCharCount(this.value)"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 outline-none focus:border-[#0095f6] focus:ring-2 focus:ring-[#0095f6]/20 transition">
                <div class="flex justify-between items-center mt-1 px-1">
                    <p class="text-[10px] text-slate-400">Tampil di balon status Chat & Profil Anda.</p>
                    <span id="chat-note-count" class="text-[10px] text-slate-400 font-bold">{{ strlen($authUser->note ?? '') }}/60</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                @if ($authUser->note)
                    <button type="button" onclick="submitChatNote(null, true)" class="rounded-xl px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                        Hapus Catatan
                    </button>
                @else
                    <div></div>
                @endif
                <div class="flex gap-2">
                    <button type="button" onclick="closeChatNoteModal()" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" id="chat-note-save-btn" class="rounded-xl bg-[#0095f6] px-5 py-2 text-xs font-extrabold text-white shadow-md hover:bg-[#0077c5] transition">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let pendingMediaUrl = '';

// SVG CONSTANTS UNTUK CEKLIS DUA
const DOUBLE_TICK_GRAY = `
    <span class="inline-flex items-center text-slate-400" title="Terkirim & aktif">
        <svg class="h-3.5 w-3.5" viewBox="0 0 16 15" fill="none">
            <path d="M15.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0l-3.54-3.54a.75.75 0 011.06-1.06l3.01 3.01 7.177-7.177a.75.75 0 011.06 1.061z" fill="currentColor"/>
            <path d="M11.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0L.97 9.75a.75.75 0 111.06-1.06l.713.712 7.207-7.207a.75.75 0 011.06 1.061z" fill="currentColor"/>
        </svg>
    </span>`;

const DOUBLE_TICK_BLUE = `
    <span class="inline-flex items-center text-[#0095f6]" title="Sudah dibaca">
        <svg class="h-3.5 w-3.5" viewBox="0 0 16 15" fill="none">
            <path d="M15.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0l-3.54-3.54a.75.75 0 011.06-1.06l3.01 3.01 7.177-7.177a.75.75 0 011.06 1.061z" fill="currentColor"/>
            <path d="M11.01 3.316l-7.707 7.706a.75.75 0 01-1.06 0L.97 9.75a.75.75 0 111.06-1.06l.713.712 7.207-7.207a.75.75 0 011.06 1.061z" fill="currentColor"/>
        </svg>
    </span>`;

function promptMediaUrl() {
    const url = prompt('Masukkan URL gambar yang ingin dikirim:', '');
    if (url) {
        pendingMediaUrl = url;
        submitChatMessage(null, url);
    }
}

// KIRIM PESAN DENGAN OPTIMISTIC UI (0 ms lag)
function submitChatMessage(e, mediaUrl = '') {
    if (e) e.preventDefault();

    const input = document.getElementById('chat-message-input');
    const msgText = input ? input.value.trim() : '';
    if (!msgText && !mediaUrl) return;

    @if($activeUser)
        const receiverId = {{ $activeUser->id }};
    @else
        return;
    @endif

    const tempId = 'temp-' + Date.now();
    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

    // 1. Langsung render bubble keluar seketika
    appendOutgoingBubble(msgText, mediaUrl, timeStr, tempId);

    // 2. Kosongkan input seketika
    if (input) {
        input.value = '';
        input.focus();
    }
    pendingMediaUrl = '';

    // 3. Update preview kontak sidebar kiri
    updateSidebarPreview(receiverId, msgText || '[Gambar]', timeStr);

    // 4. Auto scroll ke bawah
    scrollMessagesToBottom();

    // 5. Kirim via AJAX di background
    fetch('{{ route("chat.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            receiver_id: receiverId,
            message: msgText,
            media_url: mediaUrl
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            confirmOutgoingBubble(tempId, data.message.id, data.message.created_at);
        } else {
            markOutgoingBubbleError(tempId);
        }
    })
    .catch(err => {
        console.error(err);
        markOutgoingBubbleError(tempId);
    });
}

function appendOutgoingBubble(text, media, time, tempId) {
    const container = document.getElementById('messages-container');
    if (!container) return;

    const div = document.createElement('div');
    div.id = tempId;
    div.className = 'flex flex-col items-end animate-fade-in';

    let mediaHtml = '';
    if (media) {
        mediaHtml = `
            <div class="mb-1.5 max-w-xs sm:max-w-sm rounded-2xl overflow-hidden shadow-md">
                <img src="${escapeHtml(media)}" class="w-full h-auto object-cover rounded-2xl">
            </div>
        `;
    }

    let textHtml = '';
    if (text) {
        textHtml = `
            <div class="max-w-xs sm:max-w-md rounded-3xl px-4 py-2.5 text-xs sm:text-sm font-medium leading-relaxed shadow-sm bg-[#0095f6] text-white rounded-br-sm">
                ${escapeHtml(text)}
            </div>
        `;
    }

    div.innerHTML = `
        ${mediaHtml}
        ${textHtml}
        <div class="flex items-center gap-1 mt-1 px-1">
            <span class="bubble-time text-[9px] text-slate-400">${time}</span>
            <span class="bubble-tick">${DOUBLE_TICK_GRAY}</span>
        </div>
    `;

    container.appendChild(div);
}

function confirmOutgoingBubble(tempId, realId, serverTime) {
    const el = document.getElementById(tempId);
    if (!el) return;
    el.id = 'msg-' + realId;
    el.setAttribute('data-message-id', realId);

    const timeEl = el.querySelector('.bubble-time');
    const tickEl = el.querySelector('.bubble-tick');
    if (timeEl && serverTime) timeEl.textContent = serverTime;
    if (tickEl) tickEl.innerHTML = DOUBLE_TICK_GRAY; // Ceklis dua abu-abu (terkirim)
}

function markOutgoingBubbleError(tempId) {
    const el = document.getElementById(tempId);
    if (!el) return;
    const tickEl = el.querySelector('.bubble-tick');
    if (tickEl) {
        tickEl.innerHTML = '<span class="text-rose-500 text-[10px] font-bold">! Gagal</span>';
    }
}

function updateSidebarPreview(userId, text, time) {
    const prevEl = document.getElementById('contact-preview-' + userId);
    if (prevEl) {
        prevEl.textContent = 'Anda: ' + text;
        prevEl.className = 'text-[11px] text-slate-500 truncate mt-0.5 max-w-[170px]';
    }
    const timeEl = document.getElementById('contact-time-' + userId);
    if (timeEl) {
        timeEl.textContent = time;
    }
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function scrollMessagesToBottom() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function clearChatSearch() {
    const input = document.getElementById('chat-search-input');
    if (input) {
        input.value = '';
        filterChatContacts('');
    }
}

function filterChatContacts(keyword) {
    const q = keyword.toLowerCase().trim();
    const clearBtn = document.getElementById('chat-search-clear');
    if (clearBtn) {
        clearBtn.classList.toggle('hidden', !q);
    }

    document.querySelectorAll('.contact-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

// ─── NOTE MODAL & INSTANT AJAX ──────────────────────────────────────
function openChatNoteModal() {
    const modal = document.getElementById('chat-note-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        const input = document.getElementById('chat-note-input');
        if (input) input.focus();
    }
}

function closeChatNoteModal() {
    const modal = document.getElementById('chat-note-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function updateChatNoteCharCount(val) {
    const countEl = document.getElementById('chat-note-count');
    if (countEl) countEl.textContent = val.length + '/60';
}

function submitChatNote(e, clear = false) {
    if (e) e.preventDefault();

    const input = document.getElementById('chat-note-input');
    const noteVal = clear ? '' : (input ? input.value.trim() : '');
    const saveBtn = document.getElementById('chat-note-save-btn');
    if (saveBtn) saveBtn.disabled = true;

    fetch('{{ route("profile.note") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ note: noteVal })
    })
    .then(r => r.json())
    .then(data => {
        if (saveBtn) saveBtn.disabled = false;
        if (data.success) {
            const bubbleEl = document.getElementById('chat-my-note-bubble');
            if (bubbleEl) {
                bubbleEl.innerHTML = (data.note ? escapeHtml(data.note) : 'Your note') +
                    '<span class="absolute -bottom-[5px] left-1/2 -translate-x-1/2 h-[6px] w-[6px] rotate-45 bg-white border-r border-b border-slate-200"></span>';
            }
            if (input) input.value = data.note || '';
            updateChatNoteCharCount(data.note || '');
            closeChatNoteModal();
        }
    })
    .catch(err => {
        if (saveBtn) saveBtn.disabled = false;
        alert('Terjadi kesalahan saat menyimpan catatan.');
    });
}

// ─── SINKRONISASI REAL-TIME (Ceklis Dua Berubah Biru Saat Dibaca & Pesan Baru) ──
@if ($activeUser)
function syncActiveChat() {
    const receiverId = {{ $activeUser->id }};
    fetch('{{ route("chat.sync") }}?receiver_id=' + receiverId, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && Array.isArray(data.messages)) {
            let hasNewMessage = false;

            data.messages.forEach(msg => {
                // Cari apakah bubble pesan sudah ada di DOM
                const existingBubble = document.querySelector(`[data-message-id="${msg.id}"]`);

                if (existingBubble) {
                    // Update tanda ceklis jika pesan keluar milik kita
                    if (msg.is_outgoing) {
                        const tickContainer = existingBubble.querySelector('.bubble-tick') || existingBubble.querySelector('[title="Terkirim & aktif"]') || existingBubble.querySelector('[title="Sudah dibaca"]');
                        if (tickContainer) {
                            if (msg.is_read) {
                                tickContainer.outerHTML = DOUBLE_TICK_BLUE; // Berubah jadi ceklis dua biru!
                            } else {
                                tickContainer.outerHTML = DOUBLE_TICK_GRAY; // Ceklis dua abu-abu
                            }
                        }
                    }
                } else {
                    // Pesan baru masuk dari lawan bicara yang belum ada di layar!
                    hasNewMessage = true;
                    appendIncomingBubble(msg.text, msg.media_url, msg.created_at, msg.id);
                }
            });

            if (hasNewMessage) {
                scrollMessagesToBottom();
            }
        }
    })
    .catch(() => {});
}

function appendIncomingBubble(text, media, time, id) {
    const container = document.getElementById('messages-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'flex flex-col items-start animate-fade-in';
    div.setAttribute('data-message-id', id);

    let mediaHtml = '';
    if (media) {
        mediaHtml = `
            <div class="mb-1.5 max-w-xs sm:max-w-sm rounded-2xl overflow-hidden shadow-md">
                <img src="${escapeHtml(media)}" class="w-full h-auto object-cover rounded-2xl">
            </div>`;
    }

    let textHtml = '';
    if (text) {
        textHtml = `
            <div class="max-w-xs sm:max-w-md rounded-3xl px-4 py-2.5 text-xs sm:text-sm font-medium leading-relaxed shadow-sm bg-[#efefef] text-slate-900 rounded-bl-sm">
                ${escapeHtml(text)}
            </div>`;
    }

    div.innerHTML = `
        ${mediaHtml}
        ${textHtml}
        <div class="flex items-center gap-1 mt-1 px-1">
            <span class="text-[9px] text-slate-400">${time}</span>
        </div>`;

    container.appendChild(div);
}

// Jalankan polling setiap 3.5 detik untuk sinkronisasi ceklis biru dan pesan baru
setInterval(syncActiveChat, 3500);
@endif

// Auto scroll saat pertama kali dibuka
window.addEventListener('DOMContentLoaded', () => {
    scrollMessagesToBottom();
});
</script>
@endsection
