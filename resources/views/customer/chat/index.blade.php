@extends('layouts.app')

@section('title', 'Direct Messages · Komunitas')

@section('content')
@php
    $rawAuthName = trim($authUser->name ?? 'User');
    $authInitials = strtoupper(substr($rawAuthName, 0, 2));
@endphp

<div class="h-screen w-full">
    <div class="flex h-full w-full overflow-hidden bg-white text-slate-900">

        <!-- LEFT SIDEBAR: Threads List & Notes -->
        <aside class="flex w-full shrink-0 flex-col border-r border-slate-200 bg-white sm:w-[350px]">
            <!-- Top Bar: Username & New Chat -->
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <div class="flex items-center gap-1.5 cursor-pointer hover:opacity-80 transition">
                    <span class="text-base font-extrabold tracking-tight">{{ $authUser->username ?? 'aysernii' }}</span>
                    @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-4 w-4 text-slate-400'])
                </div>
                <button type="button" onclick="focusChatSearch()" class="p-2 rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" title="Pesan Baru">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
            </div>

            <!-- Search input -->
            <div class="px-5 py-2">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                        @include('partials.icons', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="text" id="chat-search-input" placeholder="Search" oninput="filterChatContacts(this.value)"
                           class="w-full rounded-2xl bg-slate-100 py-2 pl-9 pr-4 text-xs font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-1 focus:ring-[#4154e8]">
                </div>
            </div>

            <!-- Notes Tray (Foto 3: Share thoughts / Friends notes) -->
            <div class="px-5 py-3 border-b border-slate-100">
                <div class="flex gap-4 overflow-x-auto pb-1 no-scrollbar">
                    <!-- Your Note -->
                    <div class="flex flex-col items-center shrink-0 w-16 text-center cursor-pointer group" onclick="window.location.href='{{ route('profile') }}'">
                        <div class="relative mb-1">
                            <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-xl border border-slate-100 bg-white px-2 py-0.5 text-[9px] font-medium text-slate-600 whitespace-nowrap shadow-sm group-hover:scale-105 transition">
                                {{ $authUser->note ? Str::limit($authUser->note, 10) : 'Share note' }}
                            </span>
                            @if ($authUser->avatar)
                                <img src="{{ $authUser->avatar }}" class="h-13 w-13 rounded-full object-cover ring-2 ring-slate-200 mt-2">
                            @else
                                <div class="flex h-13 w-13 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-xs font-black ring-2 ring-slate-200 mt-2">
                                    {{ $authInitials }}
                                </div>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium truncate w-full mt-1">Your note</span>
                    </div>

                    <!-- Friends Notes -->
                    @foreach ($notes as $fNote)
                        <div class="flex flex-col items-center shrink-0 w-16 text-center cursor-pointer group"
                             onclick="window.location.href='{{ route('chat', ['user' => $fNote->username ?? $fNote->id]) }}'">
                            <div class="relative mb-1">
                                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-xl border border-slate-100 bg-white px-2 py-0.5 text-[9px] font-medium text-slate-600 whitespace-nowrap shadow-sm group-hover:scale-105 transition">
                                    {{ Str::limit($fNote->note, 12) }}
                                </span>
                                @if ($fNote->avatar)
                                    <img src="{{ $fNote->avatar }}" class="h-13 w-13 rounded-full object-cover ring-2 ring-[#4154e8]/50 mt-2">
                                @else
                                    <div class="flex h-13 w-13 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-xs font-bold ring-2 ring-[#4154e8]/50 mt-2">
                                        {{ strtoupper(substr($fNote->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-600 font-medium truncate w-full mt-1">{{ explode(' ', $fNote->name)[0] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Messages Header & Requests -->
            <div class="flex items-center justify-between px-5 pt-3 pb-2 text-xs font-bold text-slate-400">
                <span class="text-slate-900 font-extrabold">Pesan</span>
                <span class="cursor-pointer hover:text-slate-900 transition">Permintaan</span>
            </div>

            <!-- Contacts list -->
            <div id="contacts-container" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @foreach ($contacts as $c)
                    @php $isActive = $activeUser && $activeUser->id === $c['id']; @endphp
                    <a href="{{ route('chat', ['user' => $c['username'] ?? $c['id']]) }}"
                       class="contact-item flex items-center justify-between px-5 py-3 transition hover:bg-slate-50 {{ $isActive ? 'bg-blue-50' : '' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative shrink-0">
                                @if ($c['avatar'])
                                    <img src="{{ $c['avatar'] }}" alt="{{ $c['name'] }}" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-tr from-[#3b49cf] to-[#5b72ff] text-white text-sm font-black">
                                        {{ $c['initials'] }}
                                    </div>
                                @endif
                                <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $c['name'] }}</h4>
                                <p class="text-[11px] {{ $c['unread'] ? 'text-slate-900 font-bold' : 'text-slate-500' }} truncate mt-0.5 max-w-[170px]">
                                    {{ $c['is_outgoing'] ? 'You: ' : '' }}{{ $c['last_message'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end shrink-0 pl-2">
                            <span class="text-[10px] text-slate-500">{{ $c['last_time'] }}</span>
                            @if ($c['unread'])
                                <span class="h-2 w-2 rounded-full bg-[#0095f6] mt-1"></span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </aside>

        <!-- RIGHT PANEL: Chat Conversation Room (Foto 3) -->
        <main class="hidden sm:flex flex-1 flex-col bg-white">
            @if ($activeUser)
                @php
                    $rawActiveName = trim($activeUser->name ?? 'User');
                    $activeInitials = strtoupper(substr($rawActiveName, 0, 2));
                @endphp
                <!-- Header: User details & call icons -->
                <header class="flex items-center justify-between border-b border-slate-200 px-6 py-3.5 bg-white">
                    <a href="{{ route('profile.show', $activeUser->username ?? $activeUser->id) }}" class="flex items-center gap-3 group">
                        <div class="relative">
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
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-[#4154e8] transition">{{ $activeUser->name }}</h3>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">{{ $activeUser->username ? '@' . $activeUser->username : 'Active today' }}</p>
                        </div>
                    </a>

                    <!-- Action Icons (Foto 3: Call, Video, Info) -->
                    <div class="flex items-center gap-3 text-slate-500">
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

                <!-- Messages Stream (Foto 3) -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Target User Hero Card -->
                    <div class="flex flex-col items-center py-6 text-center">
                        @if ($activeUser->avatar)
                            <img src="{{ $activeUser->avatar }}" class="h-20 w-20 rounded-full object-cover ring-4 ring-slate-800 shadow-md mb-2">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-2xl font-black mb-2">
                                {{ $activeInitials }}
                            </div>
                        @endif
                        <h4 class="text-base font-extrabold text-slate-900">{{ $activeUser->name }}</h4>
                        <p class="text-xs text-slate-400">{{ $activeUser->username ? '@' . $activeUser->username : '' }} · BARENG Community</p>
                        <a href="{{ route('profile.show', $activeUser->username ?? $activeUser->id) }}"
                           class="mt-3 rounded-xl bg-slate-100 px-4 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                            View profile
                        </a>
                    </div>

                    <!-- Message Bubbles -->
                    @foreach ($messages as $msg)
                        @php $isMe = $msg->sender_id === $authUser->id; @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            @if ($msg->media_url)
                                <div class="mb-1.5 max-w-xs sm:max-w-sm rounded-2xl overflow-hidden shadow-md">
                                    <img src="{{ $msg->media_url }}" alt="Media" class="w-full h-auto object-cover rounded-2xl">
                                </div>
                            @endif

                            @if (!empty($msg->message))
                                <div class="max-w-xs sm:max-w-md rounded-3xl px-4 py-2.5 text-xs sm:text-sm font-medium leading-relaxed shadow-sm {{ $isMe ? 'bg-[#4154e8] text-white rounded-br-sm' : 'bg-slate-100 text-slate-800 rounded-bl-sm' }}">
                                    {{ $msg->message }}
                                </div>
                            @endif

                            <span class="text-[9px] text-slate-500 mt-1 px-1">
                                {{ $msg->created_at->format('H:i') }}
                                @if ($isMe) · Seen @endif
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- Input Footer (Foto 3) -->
                <footer class="p-4 border-t border-slate-200 bg-white">
                    <form id="chat-form" onsubmit="submitChatMessage(event)" class="relative flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5">
                        <input type="text" id="chat-message-input" placeholder="Message..." required autocomplete="off"
                               class="flex-1 bg-transparent text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 outline-none">

                        <!-- Mic voice note icon -->
                        <button type="button" onclick="alert('Voice note siap terintegrasi!')" class="text-slate-400 hover:text-slate-700 transition" title="Voice message">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </button>

                        <!-- Attach photo / media -->
                        <button type="button" onclick="promptMediaUrl()" class="text-slate-400 hover:text-slate-700 transition" title="Kirim gambar">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>

                        <!-- Send Button -->
                        <button type="submit" class="rounded-full bg-[#4154e8] px-3.5 py-1 text-xs font-bold text-white transition hover:bg-[#3243d4] active:scale-95 shadow-sm">
                            Kirim
                        </button>
                    </form>
                </footer>
            @else
                <!-- No active conversation selected -->
                <div class="flex flex-1 flex-col items-center justify-center p-8 text-center text-slate-400">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-slate-200 text-slate-400 mb-4">
                        @include('partials.icons', ['name' => 'mail', 'class' => 'h-10 w-10'])
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Pesan Anda</h3>
                    <p class="text-xs text-slate-400 max-w-sm mt-1">Kirim pesan privat, koordinasi sesi bareng, atau sapa tetangga cluster dari daftar sebelah kiri.</p>
                </div>
            @endif
        </main>
    </div>
</div>

<script>
let pendingMediaUrl = '';

function promptMediaUrl() {
    const url = prompt('Masukkan URL gambar yang ingin dikirim:', '');
    if (url) {
        pendingMediaUrl = url;
        submitChatMessage(null, url);
    }
}

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
            appendOutgoingMessage(data.message.text, data.message.media_url, data.message.created_at);
            if (input) input.value = '';
            pendingMediaUrl = '';
            scrollMessagesToBottom();
        }
    })
    .catch(err => {
        console.error(err);
        // fallback submit standard form
        if (e) e.target.submit();
    });
}

function appendOutgoingMessage(text, media, time) {
    const container = document.getElementById('messages-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'flex flex-col items-end animate-fade-in';

    let mediaHtml = '';
    if (media) {
        mediaHtml = `
            <div class="mb-1.5 max-w-xs sm:max-w-sm rounded-2xl overflow-hidden shadow-md">
                <img src="${media}" class="w-full h-auto object-cover rounded-2xl">
            </div>
        `;
    }

    let textHtml = '';
    if (text) {
        textHtml = `
            <div class="max-w-xs sm:max-w-md rounded-3xl px-4 py-2.5 text-xs sm:text-sm font-medium leading-relaxed shadow-sm bg-[#4154e8] text-white rounded-br-sm">
                ${text}
            </div>
        `;
    }

    div.innerHTML = `
        ${mediaHtml}
        ${textHtml}
        <span class="text-[9px] text-slate-500 mt-1 px-1">${time} · Sent</span>
    `;

    container.appendChild(div);
}

function scrollMessagesToBottom() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function focusChatSearch() {
    const input = document.getElementById('chat-search-input');
    if (input) input.focus();
}

function filterChatContacts(keyword) {
    const q = keyword.toLowerCase();
    document.querySelectorAll('.contact-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

// Auto scroll on load
window.addEventListener('DOMContentLoaded', () => {
    scrollMessagesToBottom();
});
</script>
@endsection
