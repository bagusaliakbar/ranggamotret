@extends('layouts.admin')

@section('title', 'Dashboard Pelanggan — RanggaMotret.id')

@section('content')
@php
    $upcomingBooking = $recentBookings->whereIn('status', ['Terjadwal', 'Disetujui', 'Dibayar', 'Menunggu Pembayaran'])->first();
    $latestBooking = $recentBookings->first();
@endphp

<div class="space-y-8">
    <!-- Top Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <span>Dashboard</span>
                <span>/</span>
                <span class="text-slate-700">Portal Pelanggan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola jadwal pemotretan, kurasi galeri proofing foto, dan ruang belajar akademi fotografi Anda.</p>
        </div>
    </div>
    <!-- Visual 5-Step Booking Stepper Tracker for Latest Booking (Luxury Redesign) -->
    @if($latestBooking)
        @php
            $statusStep = match($latestBooking->status) {
                'Menunggu Konfirmasi' => 1,
                'Menunggu Pembayaran' => 2,
                'Disetujui', 'Dibayar', 'Terjadwal' => 3,
                'Pemotretan Selesai' => 4,
                'Selesai' => 5,
                default => 2,
            };

            $progressPercent = match($statusStep) {
                1 => 0,
                2 => 25,
                3 => 50,
                4 => 75,
                5 => 100,
                default => 25,
            };

            $statusConfig = match($latestBooking->status) {
                'Selesai' => [
                    'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                    'icon' => 'ph-check-circle',
                    'iconColor' => 'text-emerald-600',
                    'label' => 'Selesai'
                ],
                'Disetujui', 'Dibayar', 'Terjadwal' => [
                    'bg' => 'bg-slate-900 text-white border-slate-900',
                    'icon' => 'ph-calendar-check',
                    'iconColor' => 'text-emerald-400',
                    'label' => $latestBooking->status
                ],
                'Pemotretan Selesai' => [
                    'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
                    'icon' => 'ph-camera',
                    'iconColor' => 'text-blue-600',
                    'label' => 'Pemotretan Selesai'
                ],
                'Menunggu Pembayaran' => [
                    'bg' => 'bg-amber-50 text-amber-900 border-amber-300',
                    'icon' => 'ph-wallet',
                    'iconColor' => 'text-amber-600',
                    'label' => 'Menunggu Pembayaran'
                ],
                default => [
                    'bg' => 'bg-slate-100 text-slate-800 border-slate-200',
                    'icon' => 'ph-clock',
                    'iconColor' => 'text-slate-600',
                    'label' => $latestBooking->status
                ],
            };

            $steps = [
                1 => [
                    'title' => 'Reservasi Dibuat',
                    'desc' => 'Draft Terkirim',
                    'icon' => 'ph-file-text'
                ],
                2 => [
                    'title' => 'Pembayaran DP',
                    'desc' => 'Midtrans Snap',
                    'icon' => 'ph-credit-card'
                ],
                3 => [
                    'title' => 'Jadwal Terkunci',
                    'desc' => 'Slot Terkonfirmasi',
                    'icon' => 'ph-calendar-check'
                ],
                4 => [
                    'title' => 'Sesi Pemotretan',
                    'desc' => 'Produksi Visual',
                    'icon' => 'ph-camera'
                ],
                5 => [
                    'title' => 'Proofing & Retouch',
                    'desc' => 'Koleksi Foto Siap',
                    'icon' => 'ph-sparkle'
                ],
            ];
        @endphp

        <div class="clean-card p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-2xs relative overflow-hidden space-y-6">
            <!-- Decorative Subtle Watermark Background -->
            <div class="absolute -right-8 -bottom-8 text-slate-900/[0.03] text-9xl pointer-events-none select-none -rotate-12">
                <i class="ph-duotone ph-aperture"></i>
            </div>

            <!-- Header Section of Stepper -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 relative z-10">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-lg shadow-xs shrink-0">
                        <i class="ph-duotone ph-compass text-emerald-400"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest text-slate-400">Timeline Alur Reservasi</span>
                            <span class="font-mono text-[11px] font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200/80">• {{ $latestBooking->nomor_booking }}</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug mt-0.5">
                            {{ $latestBooking->layanan?->nama ?? 'Sesi Pemotretan' }}
                            <span class="text-xs font-semibold text-slate-400 hidden sm:inline ml-1">• Paket {{ $latestBooking->paket?->nama_paket ?? '-' }}</span>
                        </h3>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0">
                    <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border shadow-2xs inline-flex items-center gap-1.5 {{ $statusConfig['bg'] }}">
                        <i class="ph-duotone {{ $statusConfig['icon'] }} text-xs {{ $statusConfig['iconColor'] }}"></i>
                        <span>Status: {{ $statusConfig['label'] }}</span>
                    </span>
                    <a href="{{ route('customer.bookings.show', $latestBooking->id) }}" 
                       class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-slate-900 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-xs font-bold inline-flex items-center gap-1 transition-all shadow-2xs hover:scale-105 active:scale-95"
                       title="Lihat Rincian Reservasi">
                        <span>Rincian</span>
                        <i class="ph-bold ph-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Stepper Progress Bar & 5 Step Nodes -->
            <div class="relative py-2 z-10">
                <!-- Connecting Horizontal Track Line (Center to Center of Step 1 to Step 5) -->
                <div class="absolute top-5 sm:top-6 left-[10%] right-[10%] h-1 bg-slate-100 rounded-full z-0 overflow-hidden">
                    <div class="h-full bg-linear-to-r from-slate-900 {{ $statusStep >= 5 ? 'via-slate-800 to-emerald-500' : 'to-slate-800' }} rounded-full transition-all duration-700 ease-out" 
                         style="width: {{ $progressPercent }}%;"></div>
                </div>

                <!-- Stepper 5 Nodes Grid -->
                <div class="grid grid-cols-5 gap-1.5 sm:gap-3 text-center relative z-10">
                    @foreach($steps as $num => $step)
                        @php
                            $isCompleted = $statusStep > $num || ($statusStep === 5 && $num === 5);
                            $isCurrent = $statusStep === $num && $num < 5;
                            $isUpcoming = $statusStep < $num;
                        @endphp

                        <div class="space-y-2 group flex flex-col items-center">
                            <!-- Step Circle Node -->
                            @if($isCompleted)
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center {{ $num === 5 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25 ring-4 ring-emerald-50 border border-emerald-500' : 'bg-slate-900 text-white shadow-xs ring-4 ring-white border border-slate-900' }} transition-transform duration-200 group-hover:scale-105">
                                    @if($num === 5)
                                        <i class="ph-duotone ph-sparkle text-lg sm:text-xl text-white"></i>
                                    @else
                                        <i class="ph-bold ph-check text-sm sm:text-base text-emerald-400"></i>
                                    @endif
                                </div>
                            @elseif($isCurrent)
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center bg-slate-900 text-white shadow-lg shadow-slate-900/20 ring-4 ring-slate-100 border border-slate-900 relative transition-transform duration-200 group-hover:scale-105">
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    </span>
                                    <i class="ph-duotone {{ $step['icon'] }} text-base sm:text-lg text-emerald-400"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center bg-white text-slate-400 border border-slate-200 ring-4 ring-slate-50/50 transition-colors">
                                    <span class="text-xs sm:text-sm font-bold font-mono">{{ $num }}</span>
                                </div>
                            @endif

                            <!-- Step Label & Status Info -->
                            <div class="space-y-0.5">
                                <span class="text-[10px] sm:text-xs font-black block leading-tight {{ $isCompleted || $isCurrent ? ($num === 5 && $isCompleted ? 'text-emerald-700' : 'text-slate-900') : 'text-slate-400' }}">
                                    {{ $step['title'] }}
                                </span>
                                <span class="hidden sm:block text-[9px] sm:text-[10px] font-semibold text-slate-400 leading-tight">
                                    {{ $step['desc'] }}
                                </span>
                                @if($isCurrent)
                                    <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-black uppercase tracking-wider text-slate-800 bg-slate-100 border border-slate-200/90 px-1.5 py-0.5 rounded-full mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Saat Ini
                                    </span>
                                @elseif($num === 5 && $isCompleted)
                                    <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-full mt-0.5">
                                        <i class="ph-fill ph-check-circle text-[9px]"></i> Tuntas
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Dynamic Progress Insight & Direct Action Strip -->
            <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl {{ $statusStep === 5 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200/80 text-slate-800' }} flex items-center justify-center shrink-0 text-sm">
                        @if($statusStep === 1)
                            <i class="ph-duotone ph-hourglass-medium"></i>
                        @elseif($statusStep === 2)
                            <i class="ph-duotone ph-wallet"></i>
                        @elseif($statusStep === 3)
                            <i class="ph-duotone ph-calendar-check"></i>
                        @elseif($statusStep === 4)
                            <i class="ph-duotone ph-camera"></i>
                        @else
                            <i class="ph-duotone ph-sparkle"></i>
                        @endif
                    </div>
                    <div class="space-y-0.5">
                        <div class="font-extrabold text-slate-900 text-xs">
                            @if($statusStep === 1)
                                Menunggu Verifikasi Ketersediaan Slot
                            @elseif($statusStep === 2)
                                Menunggu Penyelesaian Pembayaran Deposit
                            @elseif($statusStep === 3)
                                Jadwal Pemotretan Terkonfirmasi Resmi
                            @elseif($statusStep === 4)
                                Sesi Pemotretan Berhasil Dilaksanakan
                            @else
                                Seluruh Rangkaian Sesi Selesai & Hasil Siap Diunduh
                            @endif
                        </div>
                        <p class="text-slate-500 font-medium text-[11px] leading-relaxed">
                            @if($statusStep === 1)
                                Admin studio sedang mengecek ketersediaan jadwal fotografer dan lokasi sesi yang Anda pilih.
                            @elseif($statusStep === 2)
                                Silakan tuntaskan pembayaran melalui Midtrans agar slot jadwal pemotretan Anda terkunci secara otomatis.
                            @elseif($statusStep === 3)
                                Persiapkan konsep dan busana terbaik Anda. Sesi dijadwalkan pada {{ date('d M Y', strtotime($latestBooking->tanggal)) }} pukul {{ substr($latestBooking->waktu, 0, 5) }} WIB.
                            @elseif($statusStep === 4)
                                Foto mentah sesi Anda sedang dalam tahap sortir awal, kurasi resolusi tinggi, dan retouching oleh tim editor RanggaMotret.
                            @else
                                Koleksi foto hasil pemotretan telah selesai diproses. Anda dapat melihat, mengunduh, atau memilih foto cetak di Galeri Proofing.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Trigger Button in Banner -->
                <div class="shrink-0 self-end sm:self-auto">
                    @if($statusStep === 2)
                        <a href="{{ route('payments.create') }}" class="btn-dark px-3.5 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-xs">
                            <i class="ph-duotone ph-lightning text-emerald-400"></i>
                            <span>Bayar Sekarang</span>
                        </a>
                    @elseif($statusStep === 5 && $latestBooking->galeriFotos->count() > 0)
                        <a href="{{ route('customer.proofing.show', $latestBooking->id) }}" class="btn-dark px-3.5 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-xs">
                            <i class="ph-duotone ph-images text-emerald-400"></i>
                            <span>Buka Galeri Proofing</span>
                        </a>
                    @else
                        <a href="{{ route('customer.bookings.show', $latestBooking->id) }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 inline-flex items-center gap-1 py-1 px-2.5 rounded-lg hover:bg-slate-200/50 transition-colors">
                            <span>Lihat Detail</span>
                            <i class="ph-bold ph-caret-right text-[10px]"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Grid (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Sesi Foto -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl space-y-2 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Sesi Foto</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="ph-duotone ph-camera text-base"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $totalBookings ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Sesi pemotretan Anda</div>
        </div>

        <!-- 2. Sesi Aktif -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl space-y-2 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="ph-duotone ph-calendar-check text-base"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700">{{ $activeBookings ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Terjadwal & diproses</div>
        </div>

        <!-- 3. Kursus Terdaftar -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl space-y-2 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Kursus Akademi</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="ph-duotone ph-graduation-cap text-base"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-blue-700">{{ $enrolledCourses ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Kelas yang diikuti</div>
        </div>

        <!-- 4. Tagihan Menunggu -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl space-y-2 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Tagihan Pending</span>
                <div class="w-8 h-8 rounded-xl {{ ($pendingPayments ?? 0) > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs">
                    <i class="ph-duotone ph-receipt text-base"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black {{ ($pendingPayments ?? 0) > 0 ? 'text-amber-700' : 'text-slate-900' }}">{{ $pendingPayments ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Menunggu pembayaran</div>
        </div>
    </div>

    <!-- Main Content Widgets Grid (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Recent Bookings (8 Cols) -->
        <div class="lg:col-span-8 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="ph-duotone ph-calendar-blank text-slate-500 text-lg"></i> Sesi Pemotretan Terbaru
                </h2>
                <a href="{{ route('customer.bookings.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentBookings as $b)
                    <div class="clean-card p-5 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-slate-300 transition-all bg-white border border-slate-200 shadow-sm">
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-[10px] font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $b->nomor_booking }}
                                </span>
                                <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $b->layanan?->nama ?? 'Layanan Pemotretan' }}</h3>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600 font-medium">
                                <span><i class="ph-duotone ph-package text-slate-400 mr-1"></i> Paket {{ $b->paket?->nama_paket ?? '-' }}</span>
                                <span><i class="ph-duotone ph-calendar-blank text-slate-400 mr-1"></i> {{ date('d M Y', strtotime($b->tanggal)) }} ({{ substr($b->waktu, 0, 5) }} WIB)</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end shrink-0">
                            @php
                                $bBadge = match($b->status) {
                                    'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'Selesai' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'Dibatalkan', 'Ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase border {{ $bBadge }}">
                                {{ $b->status }}
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn-dark px-3.5 py-1.5 rounded-xl text-xs font-bold">
                                    Detail
                                </a>
                                @if($b->galeriFotos->count() > 0)
                                    <a href="{{ route('customer.proofing.show', $b->id) }}" class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm flex items-center gap-1.5">
                                        <i class="ph-duotone ph-images text-sm"></i> Proofing
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="clean-card p-10 text-center rounded-3xl space-y-3 bg-white border border-slate-200">
                        <i class="ph-duotone ph-camera-plus text-4xl text-slate-300"></i>
                        <h4 class="text-sm font-bold text-slate-900">Belum Ada Sesi Pemotretan</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda belum memiliki pesanan jadwal foto. Mulai abadikan momen spesial Anda bersama RanggaMotret.</p>
                        <a href="{{ route('customer.bookings.create') }}" class="btn-dark inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold shadow-md mt-2">
                            <i class="ph-duotone ph-plus"></i> Buat Reservasi Sekarang
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: LMS Courses & Quick Links (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Enrolled Courses -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="ph-duotone ph-graduation-cap text-slate-500 text-lg"></i> Kursus Saya
                    </h2>
                    <a href="{{ route('student.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                        Ruang Belajar &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($enrolledList as $enroll)
                        @php
                            $isCompleted = ($enroll->progress_persen >= 100 || $enroll->status === 'selesai');
                        @endphp
                        <div class="clean-card p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-extrabold text-slate-900 leading-snug line-clamp-1">
                                        {{ $enroll->kursus->nama }}
                                    </h4>
                                    @if($isCompleted)
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 shrink-0">
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium">Pengampu: {{ $enroll->kursus->instruktur->name ?? 'Owner RanggaMotret' }}</p>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between text-[10px] font-bold text-slate-700">
                                    <span>Progress Belajar</span>
                                    <span class="{{ $isCompleted ? 'text-emerald-700' : 'text-slate-900' }}">{{ $enroll->progress_persen }}%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                    <div class="h-full {{ $isCompleted ? 'bg-emerald-600' : 'bg-slate-900' }} rounded-full transition-all duration-500" style="width: {{ $enroll->progress_persen }}%;"></div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <a href="{{ route('student.learn', $enroll->kursus_id) }}" class="flex-1 btn-dark py-2 rounded-xl text-[10px] font-black text-center block uppercase tracking-wider shadow-sm">
                                    {{ $isCompleted ? 'Buka Kelas' : 'Lanjutkan Belajar' }}
                                </a>
                                @if($enroll->sertifikat)
                                    <a href="{{ route('student.certificate.show', $enroll->sertifikat->id) }}" target="_blank" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] uppercase tracking-wider shrink-0 flex items-center gap-1 shadow-sm" title="Lihat Sertifikat">
                                        <i class="ph-duotone ph-certificate text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="clean-card p-6 text-center rounded-2xl space-y-2 bg-white border border-slate-200">
                            <i class="ph-duotone ph-book-open text-3xl text-slate-300"></i>
                            <p class="text-xs text-slate-500 font-medium">Belum ada kursus aktif.</p>
                            <a href="{{ route('public.courses') }}" class="text-xs font-bold text-slate-900 hover:underline block pt-1">
                                Eksplor Katalog Kursus &rarr;
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
