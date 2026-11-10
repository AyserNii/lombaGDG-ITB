@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="mx-auto max-w-2xl pb-16">
    <!-- Pure White Modern Notification Panel (Tema Putih BARENG) -->
    <div class="rounded-[28px] bg-white text-slate-800 p-6 sm:p-8 shadow-sm border border-slate-200/80">
        <!-- Header: Title & Back -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h1 class="text-xl font-black tracking-tight text-slate-900">Notifikasi</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Aktivitas tetangga, sesi bareng, dan interaksi postingan</p>
            </div>
            <a href="{{ url()->previous() ?: route('dashboard') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition" title="Tutup">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <!-- Filter Pills (All, People you follow, Comments, Follows) -->
        <div class="flex items-center gap-2 overflow-x-auto py-4 border-b border-slate-100 no-scrollbar">
            <a href="{{ route('notifications', ['filter' => 'all']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-extrabold transition {{ $filter === 'all' ? 'bg-[#4154e8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('notifications', ['filter' => 'following']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-extrabold transition whitespace-nowrap {{ $filter === 'following' ? 'bg-[#4154e8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Diikuti
            </a>
            <a href="{{ route('notifications', ['filter' => 'comments']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-extrabold transition {{ $filter === 'comments' ? 'bg-[#4154e8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Komentar
            </a>
            <a href="{{ route('notifications', ['filter' => 'follows']) }}"
               class="rounded-full px-4 py-1.5 text-xs font-extrabold transition {{ $filter === 'follows' ? 'bg-[#4154e8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pengikut Baru
            </a>
        </div>

        <!-- Notification Groups -->
        <div class="divide-y divide-slate-100 pt-2 space-y-6">

            <!-- THIS WEEK -->
            @if ($grouped['this_week']->isNotEmpty())
                <div class="pt-4 space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Minggu Ini</h3>
                    <div class="divide-y divide-slate-50">
                        @foreach ($grouped['this_week'] as $notif)
                            @include('customer.notifications.item', ['notif' => $notif])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- THIS MONTH -->
            @if ($grouped['this_month']->isNotEmpty())
                <div class="pt-4 space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Bulan Ini</h3>
                    <div class="divide-y divide-slate-50">
                        @foreach ($grouped['this_month'] as $notif)
                            @include('customer.notifications.item', ['notif' => $notif])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- EARLIER -->
            @if ($grouped['earlier']->isNotEmpty())
                <div class="pt-4 space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Sebelumnya</h3>
                    <div class="divide-y divide-slate-50">
                        @foreach ($grouped['earlier'] as $notif)
                            @include('customer.notifications.item', ['notif' => $notif])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($allNotifs->isEmpty())
                <div class="py-16 text-center text-slate-400">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 mb-3 text-slate-400">
                        @include('partials.icons', ['name' => 'bell', 'class' => 'h-7 w-7 text-slate-400'])
                    </div>
                    <p class="text-sm font-extrabold text-slate-700">Belum ada notifikasi</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Aktivitas follower baru, komentar postingan, dan respon sesi bareng akan muncul di sini.</p>
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
                btn.className = 'rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-1.5 text-xs font-extrabold text-slate-700 transition';
                btn.innerText = 'Following';
            } else {
                btn.className = 'rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-4 py-1.5 text-xs font-extrabold text-white transition';
                btn.innerText = 'Follow';
            }
        }
    });
}
</script>
@endsection
