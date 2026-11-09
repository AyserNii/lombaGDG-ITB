@extends('layouts.app')

@section('title', 'Dashboard BARENG')

@section('content')
@php
    $authUser = $user ?? session('user', [
        'name' => 'Grace Stanley',
        'role' => 'Customer · Cluster Beji',
        'avatar' => null,
        'location' => 'Beji, Depok',
        'savings' => 'Rp 95.4k',
        'sessions_count' => 12,
        'co2_saved' => '28.4 kg',
        'badge' => 'Top Saver',
    ]);
    $firstName = explode(' ', $authUser['name'])[0];
@endphp
<div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
    <div class="space-y-5 xl:col-span-8">

        <section class="relative overflow-hidden rounded-[26px] bg-white p-6 md:p-7 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
            <div class="grid items-center gap-4 md:grid-cols-12">
                <div class="md:col-span-7">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef3ff] px-3 py-1 text-[11px] font-bold text-[#4154e8] mb-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#4154e8]"></span>
                        Smart Matching Engine
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-[#1e293b]">Halo {{ $firstName }}!</h1>
                    <p class="mt-2 text-xs md:text-sm font-medium leading-relaxed text-[#7e8ba0]">
                        Kamu punya {{ $authUser['sessions_count'] ?? 3 }} sesi BARENG aktif hari ini. Gabung bareng tetangga sekitar {{ $authUser['location'] ?? 'Beji' }} biar ongkir makin hemat dan bebas sampah berlebih!
                    </p>
                    <div class="mt-3.5 flex items-center gap-4">
                        <a href="{{ route('sesi') }}" class="text-xs font-bold text-[#4154e8] underline underline-offset-4 hover:text-[#3243d4]">
                            review sesi aktif
                        </a>
                        <span class="text-xs text-[#cbd5e1]">·</span>
                        <a href="{{ route('makan') }}" class="text-xs font-bold text-[#1e293b] hover:text-[#4154e8]">
                            Mulai pesan baru &rarr;
                        </a>
                    </div>
                </div>

                <div class="flex justify-center items-center md:col-span-5">
                    <div class="relative h-44 w-full max-w-[260px] flex items-center justify-center">
                        <iframe src="https://lottie.host/embed/bd897874-a421-4d40-9d49-04c4334edfc1/q0KF3q9KJR.lottie"
                                class="h-full w-full border-0 pointer-events-none"
                                title="Robot BARENG Animation"
                                loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            <section class="flex flex-col justify-between rounded-[26px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
                <div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-[#1e293b]">Dampak Hematmu</h2>
                            <p class="text-[11px] font-semibold text-[#7e8ba0]">Efisiensi biaya & logistik</p>
                        </div>
                        <button type="button" class="flex items-center gap-1.5 rounded-full bg-[#f0f3fa] px-3 py-1 text-xs font-semibold text-[#1e293b] hover:bg-[#e4e9f5]">
                            <span>Desember</span>
                            @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-3 w-3 text-[#7e8ba0]'])
                        </button>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-[#7e8ba0]">Total penghematan bulan ini:</p>
                            <div class="mt-0.5 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold tracking-tight text-[#1e293b]">{{ $authUser['savings'] ?? 'Rp 95.4k' }}</span>
                                <span class="text-[11px] font-semibold text-[#4154e8]">dari {{ $authUser['sessions_count'] ?? 12 }} sesi<br class="hidden sm:inline"> BARENG</span>
                            </div>
                        </div>
                        <a href="{{ route('sesi') }}" class="rounded-full border border-gray-200 px-3.5 py-1 text-xs font-bold text-[#1e293b] transition hover:border-[#4154e8] hover:text-[#4154e8]">
                            Semua sesi
                        </a>
                    </div>
                </div>

                <div class="mt-6 flex h-44 items-end justify-between gap-2 pt-4">
                    @php
                        $bars = [
                            ['label' => 'Makan', 'val' => '85.3k', 'h_total' => '88%', 'h_top' => '35%'],
                            ['label' => 'Belanja', 'val' => '64.7k', 'h_total' => '68%', 'h_top' => '32%'],
                            ['label' => 'Kirim', 'val' => '94.2k', 'h_total' => '96%', 'h_top' => '40%'],
                            ['label' => 'Ongkir', 'val' => '45.6k', 'h_total' => '50%', 'h_top' => '30%'],
                            ['label' => 'Kemasan', 'val' => '43.5k', 'h_total' => '48%', 'h_top' => '30%'],
                            ['label' => 'Emisi CO₂', 'val' => '74.4kg', 'h_total' => '78%', 'h_top' => '36%'],
                        ];
                    @endphp

                    @foreach ($bars as $bar)
                        <div class="flex flex-1 flex-col items-center gap-1.5 h-full justify-end">
                            <span class="text-[10px] font-semibold text-[#7e8ba0]">{{ $bar['val'] }}</span>
                            <div class="w-full max-w-[28px] overflow-hidden rounded-t-md flex flex-col justify-end" style="height: {{ $bar['h_total'] }};">
                                <div class="w-full bg-[#7595ff] rounded-t-md" style="height: {{ $bar['h_top'] }};"></div>
                                <div class="w-full bg-[#4154e8] flex-1"></div>
                            </div>
                            <p class="h-6 text-center text-[10px] font-semibold leading-tight text-[#7e8ba0]">
                                {{ $bar['label'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="flex flex-col justify-between rounded-[26px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-base font-extrabold text-[#1e293b]">Aktivitas Sesi</h2>
                        <p class="text-[11px] font-semibold text-[#7e8ba0]">Smart matching & partisipasi</p>
                    </div>
                    <button type="button" class="flex items-center gap-1.5 rounded-full bg-[#f0f3fa] px-3 py-1 text-xs font-semibold text-[#1e293b] hover:bg-[#e4e9f5]">
                        <span>Desember</span>
                        @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-3 w-3 text-[#7e8ba0]'])
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-3.5 pt-1 pb-1">
                    @php
                        $visits = [
                            ['percent' => 92, 'label' => 'Bareng Makan', 'sub' => 'Nasi & geprek Beji'],
                            ['percent' => 83, 'label' => 'Bareng Belanja', 'sub' => 'Indomaret cluster'],
                            ['percent' => 78, 'label' => 'Bareng Kirim', 'sub' => 'Rute Depok Lama'],
                            ['percent' => 97, 'label' => 'Match Lokasi', 'sub' => 'Radius 800m'],
                            ['percent' => 96, 'label' => 'Kapasitas Grup', 'sub' => 'Rata-rata 4/5 orang'],
                            ['percent' => 89, 'label' => 'Efisiensi Biaya', 'sub' => 'Hemat hingga 60%'],
                        ];
                    @endphp

                    @foreach ($visits as $visit)
                        <div class="flex flex-col items-center text-center">
                            <div class="relative flex h-13 w-13 items-center justify-center">
                                <svg class="h-13 w-13 -rotate-90" viewBox="0 0 64 64">
                                    <circle cx="32" cy="32" r="26" stroke="#edf1f7" stroke-width="6" fill="transparent" />
                                    <circle cx="32" cy="32" r="26" stroke="#4154e8" stroke-width="6"
                                            stroke-dasharray="163.36"
                                            stroke-dashoffset="{{ 163.36 * (1 - $visit['percent'] / 100) }}"
                                            stroke-linecap="round" fill="transparent" />
                                </svg>
                                <span class="absolute text-xs font-black text-[#1e293b]">{{ $visit['percent'] }}%</span>
                            </div>
                            <p class="mt-1 text-[11px] font-bold leading-tight text-[#1e293b] max-w-[110px]">
                                {{ $visit['label'] }}
                            </p>
                            <p class="text-[9px] text-[#7e8ba0] mt-0.5 max-w-[110px] leading-tight line-clamp-1">
                                {{ $visit['sub'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="rounded-[26px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-[#1e293b]">Tetangga Sering Bareng</h2>
                    <p class="text-[11px] font-semibold text-[#7e8ba0]">Komunitas gotong royong terdekat</p>
                </div>
                <a href="{{ route('community') }}" class="text-xs font-bold text-[#4154e8] hover:underline">Lihat semua</a>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-2xl bg-[#f8fafc] px-4 py-3 transition hover:bg-[#f1f4f9]">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white text-xs font-black shadow-sm select-none">
                            MJ
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#1e293b]">Mary Johnson</h3>
                            <p class="text-xs font-medium text-[#7e8ba0]">Mentor Sesi Makan · Cluster Beji Indah</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#1e293b] hover:bg-white hover:shadow-sm" title="Chat Sesi">
                            @include('partials.icons', ['name' => 'mail', 'class' => 'h-4 w-4 text-[#1e293b]'])
                        </button>
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#1e293b] hover:bg-white hover:shadow-sm" title="Telepon">
                            @include('partials.icons', ['name' => 'phone', 'class' => 'h-4 w-4 text-[#1e293b]'])
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-2xl bg-[#f8fafc] px-4 py-3 transition hover:bg-[#f1f4f9]">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-black shadow-sm select-none">
                            JB
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#1e293b]">James Brown</h3>
                            <p class="text-xs font-medium text-[#7e8ba0]">Creator Belanja Mingguan · Margonda Raya</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#1e293b] hover:bg-white hover:shadow-sm" title="Chat Sesi">
                            @include('partials.icons', ['name' => 'mail', 'class' => 'h-4 w-4 text-[#1e293b]'])
                        </button>
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#1e293b] hover:bg-white hover:shadow-sm" title="Telepon">
                            @include('partials.icons', ['name' => 'phone', 'class' => 'h-4 w-4 text-[#1e293b]'])
                        </button>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <div class="space-y-5 xl:col-span-4">

        <section class="rounded-[26px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-[#1e293b]">Jadwal Sesi</h2>
                    <p class="text-xs font-semibold text-[#7e8ba0]">6 sesi aktif hari ini</p>
                </div>
                <button type="button" class="flex items-center gap-1.5 rounded-full bg-[#f0f3fa] px-3.5 py-1 text-xs font-semibold text-[#1e293b] hover:bg-[#e4e9f5]">
                    <span>Hari ini</span>
                    @include('partials.icons', ['name' => 'chevron-down', 'class' => 'h-3 w-3 text-[#7e8ba0]'])
                </button>
            </div>

            <div class="relative mt-6">
                <div class="relative space-y-4">

                    <div class="grid grid-cols-[48px_1fr] items-start gap-3">
                        <span class="text-[11px] font-semibold text-[#7e8ba0] pt-2">10:00</span>
                        <div class="relative rounded-2xl bg-[#4154e8] p-3 text-white shadow-md shadow-[#4154e8]/20">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/20">
                                    @include('partials.icons', ['name' => 'food', 'class' => 'h-4 w-4 text-white'])
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold leading-tight">BARENG MAKAN</h4>
                                    <p class="text-[10px] text-white/80 mt-0.5">Warung Nasi Beji · 4/5 orang</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative pl-12">
                        <div class="border-b border-dashed border-[#dce3ef]"></div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-start gap-3">
                        <span class="text-[11px] font-semibold text-[#7e8ba0] pt-2">11:00</span>
                        <div class="relative rounded-2xl bg-[#f0f3fa] p-3 transition hover:bg-[#e7ecf7]">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#dfe6f5] text-[#4154e8]">
                                    @include('partials.icons', ['name' => 'bag', 'class' => 'h-3 w-3'])
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-[#1e293b] leading-tight">BARENG BELANJA</h4>
                                    <p class="text-[10px] text-[#7e8ba0] mt-0.5">Segar Mart Margonda · 3 item</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-center gap-3">
                        <span class="text-[10px] font-semibold text-[#a3b0c5]">11:30</span>
                        <div class="h-[1px] bg-[#edf1f7]"></div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-start gap-3">
                        <span class="text-[11px] font-semibold text-[#7e8ba0] pt-2">12:00</span>
                        <div class="relative rounded-2xl bg-[#f0f3fa] p-3 transition hover:bg-[#e7ecf7]">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#dfe6f5] text-[#4154e8]">
                                    @include('partials.icons', ['name' => 'food', 'class' => 'h-3 w-3'])
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-[#1e293b] leading-tight">BARENG MAKAN</h4>
                                    <p class="text-[10px] text-[#7e8ba0] mt-0.5">Ayam Geprek 88 · Sesi siang</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-center gap-3">
                        <span class="text-[10px] font-semibold text-[#a3b0c5]">12:30</span>
                        <div class="h-[1px] bg-[#edf1f7]"></div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-center gap-3">
                        <span class="text-[10px] font-semibold text-[#a3b0c5]">13:00</span>
                        <div class="h-[1px] bg-[#edf1f7]"></div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-center gap-3">
                        <span class="text-[10px] font-semibold text-[#a3b0c5]">13:30</span>
                        <div class="h-[1px] bg-[#edf1f7]"></div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-start gap-3">
                        <span class="text-[11px] font-semibold text-[#7e8ba0] pt-2">14:00</span>
                        <div class="relative rounded-2xl bg-[#f0f3fa] p-3 transition hover:bg-[#e7ecf7]">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#dfe6f5] text-[#4154e8]">
                                    @include('partials.icons', ['name' => 'truck', 'class' => 'h-3 w-3'])
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-[#1e293b] leading-tight">BARENG KIRIM</h4>
                                    <p class="text-[10px] text-[#7e8ba0] mt-0.5">Rute UI Depok · 2 paket siap jalan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-[48px_1fr] items-center gap-3">
                        <span class="text-[10px] font-semibold text-[#a3b0c5]">14:30</span>
                        <div class="h-[1px] bg-[#edf1f7]"></div>
                    </div>

                </div>
            </div>
        </section>

        <section class="rounded-[26px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-[#1e293b]">Sesi Komunitas</h2>
                    <p class="text-[11px] font-semibold text-[#7e8ba0]">Agenda bareng tetangga</p>
                </div>
                <a href="{{ route('community') }}" class="text-xs font-bold text-[#4154e8] hover:underline">Lihat semua</a>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80"
                             alt="Makan Bareng Jumat"
                             class="h-12 w-12 rounded-full object-cover shrink-0 ring-2 ring-[#edf1f7]">
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#1e293b] leading-snug line-clamp-2">
                                Sesi Akbar Makan Bareng Jumat Berkah - Warung Beji
                            </h3>
                            <p class="mt-1 flex items-center gap-2 text-[10px] font-medium text-[#7e8ba0]">
                                <span>14 Desember 2026</span>
                                <span>·</span>
                                <span>12:00 WIB</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" class="shrink-0 p-1 text-[#a0aec0] hover:text-[#1e293b]">
                        @include('partials.icons', ['name' => 'dots-vertical', 'class' => 'h-4 w-4'])
                    </button>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=120&q=80"
                             alt="Belanja Sembako Grosir"
                             class="h-12 w-12 rounded-full object-cover shrink-0 ring-2 ring-[#edf1f7]">
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#1e293b] leading-snug">
                                Belanja Grosir Sembako Komunitas Margonda
                            </h3>
                            <p class="mt-1 flex items-center gap-2 text-[10px] font-medium text-[#7e8ba0]">
                                <span>21 Desember 2026</span>
                                <span>·</span>
                                <span>16:00 WIB</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" class="shrink-0 p-1 text-[#a0aec0] hover:text-[#1e293b]">
                        @include('partials.icons', ['name' => 'dots-vertical', 'class' => 'h-4 w-4'])
                    </button>
                </div>
            </div>
        </section>

    </div>

</div>
@endsection
