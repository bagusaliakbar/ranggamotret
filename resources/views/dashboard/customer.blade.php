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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-[10px] font-extrabold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Portal Pelanggan Terpadu
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola jadwal pemotretan, kurasi galeri proofing foto, dan ruang belajar akademi fotografi Anda.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2 hover:scale-[1.02] active:scale-95 transition-all">
                <i class="ph-duotone ph-plus text-emerald-400 text-sm"></i> Reservasi Sesi Foto
            </a>
            <a href="{{ route('student.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 hover:bg-slate-50 text-xs font-bold shadow-sm flex items-center gap-2">
                <i class="ph-duotone ph-graduation-cap text-slate-500 text-sm"></i> Ruang Belajar
            </a>
        </div>
    </div>

    <!-- Upcoming Session Spotlight Card -->
    @if($upcomingBooking)
        <div class="clean-card p-6 sm:p-7 rounded-3xl bg-slate-900 text-white border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2 max-w-2xl">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                            <i class="ph-duotone ph-lightning mr-1"></i> Sesi Pemotretan Terdekat
                        </span>
                        <span class="font-mono text-slate-400 text-[11px]">{{ $upcomingBooking->nomor_booking }}</span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        {{ $upcomingBooking->layanan->nama }} — Paket {{ $upcomingBooking->paket->nama_paket }}
                    </h2>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-300 font-medium pt-1">
                        <span class="flex items-center gap-1.5">
                            <i class="ph-duotone ph-calendar-blank text-emerald-400 text-sm"></i>
                            <span>{{ date('d F Y', strtotime($upcomingBooking->tanggal)) }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="ph-duotone ph-clock text-emerald-400 text-sm"></i>
                            <span>{{ substr($upcomingBooking->waktu, 0, 5) }} WIB</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="ph-duotone ph-map-pin text-rose-400 text-sm"></i>
                            <span class="truncate max-w-xs">{{ $upcomingBooking->lokasi }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('customer.bookings.show', $upcomingBooking->id) }}" class="btn-dark bg-white hover:bg-slate-100 text-slate-900 px-5 py-3 rounded-2xl text-xs font-black shadow-lg flex items-center gap-2 transition-transform active:scale-95">
                        <span>Lihat Detail Sesi</span> &rarr;
                    </a>
                    @if($upcomingBooking->status === 'Menunggu Pembayaran' || !$upcomingBooking->is_paid)
                        <a href="{{ route('payments.create', ['booking_id' => $upcomingBooking->id]) }}" class="px-4 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black shadow-md flex items-center gap-1.5 transition-transform active:scale-95">
                            <i class="ph-duotone ph-receipt text-sm"></i> Bayar Sesi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Visual 5-Step Booking Stepper Tracker for Latest Booking -->
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
        @endphp
        <div class="clean-card p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Timeline Alur Reservasi</span>
                    <span class="font-bold text-slate-900 text-xs">• {{ $latestBooking->nomor_booking }}</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-800">
                    Status: {{ $latestBooking->status }}
                </span>
            </div>

            <!-- Stepper Bar -->
            <div class="grid grid-cols-5 gap-2 text-center text-xs">
                <!-- Step 1 -->
                <div class="space-y-1.5">
                    <div class="w-7 h-7 mx-auto rounded-full flex items-center justify-center font-black text-xs {{ $statusStep >= 1 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-400' }}">
                        1
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold block leading-tight {{ $statusStep >= 1 ? 'text-slate-900' : 'text-slate-400' }}">Reservasi Dibuat</span>
                </div>

                <!-- Step 2 -->
                <div class="space-y-1.5">
                    <div class="w-7 h-7 mx-auto rounded-full flex items-center justify-center font-black text-xs {{ $statusStep >= 2 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-400' }}">
                        2
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold block leading-tight {{ $statusStep >= 2 ? 'text-slate-900' : 'text-slate-400' }}">Pembayaran DP</span>
                </div>

                <!-- Step 3 -->
                <div class="space-y-1.5">
                    <div class="w-7 h-7 mx-auto rounded-full flex items-center justify-center font-black text-xs {{ $statusStep >= 3 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-400' }}">
                        3
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold block leading-tight {{ $statusStep >= 3 ? 'text-slate-900' : 'text-slate-400' }}">Jadwal Terkunci</span>
                </div>

                <!-- Step 4 -->
                <div class="space-y-1.5">
                    <div class="w-7 h-7 mx-auto rounded-full flex items-center justify-center font-black text-xs {{ $statusStep >= 4 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-400' }}">
                        4
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold block leading-tight {{ $statusStep >= 4 ? 'text-slate-900' : 'text-slate-400' }}">Sesi Pemotretan</span>
                </div>

                <!-- Step 5 -->
                <div class="space-y-1.5">
                    <div class="w-7 h-7 mx-auto rounded-full flex items-center justify-center font-black text-xs {{ $statusStep >= 5 ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                        5
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold block leading-tight {{ $statusStep >= 5 ? 'text-emerald-700' : 'text-slate-400' }}">Proofing & Retouch</span>
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
                                <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $b->layanan->nama }}</h3>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600 font-medium">
                                <span><i class="ph-duotone ph-package text-slate-400 mr-1"></i> Paket {{ $b->paket->nama_paket }}</span>
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
                                <p class="text-[11px] text-slate-500 font-medium">Instruktur: {{ $enroll->kursus->instruktur->name ?? 'Tim RanggaMotret' }}</p>
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

            <!-- Quick Assistance Card -->
            <div class="clean-card p-6 rounded-3xl bg-slate-900 text-white space-y-3 shadow-xl">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-white text-xs">
                    <i class="ph-duotone ph-chats-circle text-base"></i>
                </div>
                <div>
                    <h4 class="font-black text-sm text-white">Butuh Konsultasi Konsep?</h4>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed font-medium">Tim kreatif kami siap membantu moodboard, rekomendasi venue, dan persiapan busana pemotretan Anda.</p>
                </div>
                <a href="{{ route('customer.consultations.index') }}" class="w-full bg-white text-slate-900 hover:bg-slate-100 py-2.5 rounded-xl text-xs font-black text-center block shadow-sm transition-colors">
                    Riwayat & Konsultasi
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
