@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="mx-auto max-w-xl pb-16">
    <!-- Instagram Style Notifications Panel (Foto 4) -->
    <div class="rounded-[32px] bg-[#12141a] text-white p-6 shadow-2xl border border-slate-800">
        <!-- Header: Title & Close -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <h1 class="text-xl font-extrabold tracking-tight text-white">Notifications</h1>
            <a href="{{ url()->previous() ?: route('dashboard') }}" class="p-1.5 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <!-- Filter Pills (Foto 4: All, People you follow, Comments, Follows) -->
        <div class="flex items-center gap-2 overflow-x-auto py-4 border-b border-slate-800/80 no-scrollbar">
            <a href="{{ route('notifications', ['filter' => 'all']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ $filter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'bg-[#202533] text-slate-300 hover:bg-[#2b3245]' }}">
                All
            </a>
            <a href="{{ route('notifications', ['filter' => 'following']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-bold transition whitespace-nowrap {{ $filter === 'following' ? 'bg-white text-slate-900 shadow-sm' : 'bg-[#202533] text-slate-300 hover:bg-[#2b3245]' }}">
                People you follow
            </a>
            <a href="{{ route('notifications', ['filter' => 'comments']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ $filter === 'comments' ? 'bg-white text-slate-900 shadow-sm' : 'bg-[#202533] text-slate-300 hover:bg-[#2b3245]' }}">
                Comments
            </a>
            <a href="{{ route('notifications', ['filter' => 'follows']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ $filter === 'follows' ? 'bg-white text-slate-900 shadow-sm' : 'bg-[#202533] text-slate-300 hover:bg-[#2b3245]' }}">
                Follows
            </a>
        </div>

        <!-- Notification Groups -->
        <div class="divide-y divide-slate-800/60 pt-2 space-y-6">

            <!-- THIS WEEK -->
            @if ($grouped['this_week']->isNotEmpty())
                <div class="pt-4 space-y-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">This week</h3>
                    @foreach ($grouped['this_week'] as $notif)
                        @include('customer.notifications.item', ['notif' => $notif])
                    @endforeach
                </div>
            @endif

            <!-- THIS MONTH -->
            @if ($grouped['this_month']->isNotEmpty())
                <div class="pt-4 space-y-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">This month</h3>
                    @foreach ($grouped['this_month'] as $notif)
                        @include('customer.notifications.item', ['notif' => $notif])
                    @endforeach
                </div>
            @endif

            <!-- EARLIER -->
            @if ($grouped['earlier']->isNotEmpty())
                <div class="pt-4 space-y-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Earlier</h3>
                    @foreach ($grouped['earlier'] as $notif)
                        @include('customer.notifications.item', ['notif' => $notif])
                    @endforeach
                </div>
            @endif

            @if ($allNotifs->isEmpty())
                <div class="py-12 text-center text-slate-400">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-800/80 mb-3 text-slate-400">
                        @include('partials.icons', ['name' => 'bell', 'class' => 'h-7 w-7'])
                    </div>
                    <p class="text-sm font-bold text-slate-300">Belum ada notifikasi</p>
                    <p class="text-xs text-slate-500 mt-1">Aktivitas follower, sesi, dan gotong royong terdekat akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleNotifFollow(userId, btn) {
    fetch('/users/' + userId + '/toggle-follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.following) {
                btn.className = 'rounded-xl bg-[#262626] hover:bg-slate-700 px-4 py-1.5 text-xs font-bold text-white transition';
                btn.innerText = 'Following';
            } else {
                btn.className = 'rounded-xl bg-[#0095f6] hover:bg-[#1877f2] px-4 py-1.5 text-xs font-bold text-white transition';
                btn.innerText = 'Follow';
            }
        }
    });
}
</script>
@endsection

