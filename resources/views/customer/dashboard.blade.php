@extends('layouts.admin')

@section('title', 'Dashboard Member — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Saya</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Ringkasan jadwal sesi pemotretan, galeri proofing, dan aktivitas ruang belajar akademi Anda.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Booking Foto Baru
            </a>
            <a href="{{ route('student.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 hover:bg-slate-50 text-xs font-bold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-slate-500"></i> Ruang Belajar
            </a>
        </div>
    </div>

    <!-- Stats Grid (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Sesi Foto -->
        <div class="clean-card p-5 sm:p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Sesi Foto</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $totalBookings ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Sesi pemotretan Anda</div>
        </div>

        <!-- 2. Sesi Aktif -->
        <div class="clean-card p-5 sm:p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700">{{ $activeBookings ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Terjadwal & diproses</div>
        </div>

        <!-- 3. Kursus Terdaftar -->
        <div class="clean-card p-5 sm:p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Kursus Akademi</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-blue-700">{{ $enrolledCourses ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Kelas yang diikuti</div>
        </div>

        <!-- 4. Tagihan Menunggu -->
        <div class="clean-card p-5 sm:p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Tagihan Pending</span>
                <div class="w-8 h-8 rounded-lg {{ ($pendingPayments ?? 0) > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs">
                    <i class="fa-solid fa-receipt"></i>
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
                    <i class="fa-solid fa-calendar-days text-slate-500"></i> Sesi Pemotretan Terbaru
                </h2>
                <a href="{{ route('customer.bookings.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentBookings as $b)
                    <div class="clean-card p-5 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-slate-300 transition-all">
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-[10px] font-extrabold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $b->nomor_booking }}
                                </span>
                                <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $b->layanan->nama }}</h3>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600 font-medium">
                                <span><i class="fa-solid fa-box text-slate-400 mr-1"></i> Paket {{ $b->paket->nama_paket }}</span>
                                <span><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> {{ date('d M Y', strtotime($b->tanggal)) }} ({{ substr($b->waktu, 0, 5) }} WIB)</span>
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
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase border {{ $bBadge }}">
                                {{ $b->status }}
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn-dark px-3 py-1.5 rounded-lg text-xs font-bold">
                                    Detail
                                </a>
                                @if($b->galeriFotos->count() > 0)
                                    <a href="{{ route('customer.proofing.show', $b->id) }}" class="px-3 py-1.5 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-xs font-bold shadow-sm">
                                        Proofing
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="clean-card p-10 text-center rounded-2xl space-y-3">
                        <i class="fa-solid fa-calendar-plus text-3xl text-slate-300"></i>
                        <h4 class="text-sm font-bold text-slate-900">Belum Ada Sesi Pemotretan</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda belum memiliki pesanan jadwal foto. Mulai abadikan momen spesial Anda bersama RanggaMotret.</p>
                        <a href="{{ route('customer.bookings.create') }}" class="btn-dark inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold shadow-md mt-2">
                            <i class="fa-solid fa-plus"></i> Buat Booking Sekarang
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
                        <i class="fa-solid fa-graduation-cap text-slate-500"></i> Kursus Saya
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
                        <div class="clean-card p-4 rounded-2xl space-y-3">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-extrabold text-slate-900 leading-snug line-clamp-1">
                                        {{ $enroll->kursus->nama }}
                                    </h4>
                                    @if($isCompleted)
                                        <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 shrink-0">
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500">Instruktur: {{ $enroll->kursus->instruktur->name ?? 'Bagus Ali Akbar' }}</p>
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
                                <a href="{{ route('student.learn', $enroll->kursus_id) }}" class="flex-1 btn-dark py-1.5 rounded-lg text-[10px] font-bold text-center block uppercase tracking-wider">
                                    {{ $isCompleted ? 'Buka Kelas' : 'Lanjutkan Belajar' }}
                                </a>
                                @if($enroll->sertifikat)
                                    <a href="{{ route('student.certificate.show', $enroll->sertifikat->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] uppercase tracking-wider shrink-0 flex items-center gap-1 shadow-sm" title="Lihat Sertifikat">
                                        <i class="fa-solid fa-award"></i> Sertifikat
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="clean-card p-6 text-center rounded-2xl space-y-2">
                            <i class="fa-solid fa-book-open text-2xl text-slate-300"></i>
                            <p class="text-xs text-slate-500 font-medium">Belum ada kursus aktif.</p>
                            <a href="{{ route('public.courses') }}" class="text-xs font-bold text-slate-900 hover:underline block pt-1">
                                Eksplor Katalog Kursus &rarr;
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Assistance Card -->
            <div class="clean-card p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white space-y-3 shadow-md">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white text-xs">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-sm text-white">Butuh Bantuan Sesi?</h4>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">Tim fotografer kami siap membantu konsultasi konsep foto, lokasi, dan persiapan busana.</p>
                </div>
                <a href="{{ route('public.consultation') }}" class="w-full bg-white text-slate-900 hover:bg-slate-100 py-2 rounded-xl text-xs font-extrabold text-center block shadow-sm">
                    Konsultasi Sekarang
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
