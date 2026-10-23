@extends('layouts.admin')

@section('title', 'Owner Dashboard — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section (Luxury Editorial Greeting) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1.5">
                <span>Dashboard</span>
                <span>/</span>
                <span class="text-slate-900 font-extrabold">Ringkasan Studio &amp; Akademi</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Panel Owner Studio</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold uppercase tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Studio Aktif</span>
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Selamat datang kembali, <strong class="text-slate-800">{{ Auth::user()->name }}</strong>. Pantau metrik performa reservasi pemotretan, arus kas pendapatan, dan progres akademi fotografi secara real-time.
            </p>
        </div>

        <!-- Quick Summary Action Badges -->
        <div class="flex flex-wrap items-center gap-2 sm:self-auto">
            @if($pembayaranPending > 0)
                <a href="{{ route('admin.payments.index', ['status' => 'Menunggu Verifikasi']) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all">
                    <i class="ph-duotone ph-warning-circle text-sm animate-pulse"></i>
                    <span>{{ $pembayaranPending }} Pembayaran Perlu Dicek</span>
                </a>
            @endif

            <a href="{{ route('admin.bookings.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all">
                <i class="ph-duotone ph-calendar-check text-sm text-slate-300"></i>
                <span>Kelola Reservasi</span>
            </a>
        </div>
    </div>

    <!-- 4 Main Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Reservasi Foto -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-3 relative overflow-hidden group hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Reservasi</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-2xs group-hover:scale-110 transition-transform">
                    <i class="ph-duotone ph-camera"></i>
                </div>
            </div>
            <div>
                <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalBooking) }}</div>
                <div class="flex items-center gap-1.5 mt-1.5 text-[11px] font-semibold text-slate-500">
                    @if($bookingPending > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-amber-700 font-bold">{{ $bookingPending }} menunggu konfirmasi</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-500">Semua jadwal terkonfirmasi</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Pendapatan Terverifikasi -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-3 relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-emerald-700 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-2xs group-hover:scale-110 transition-transform">
                    <i class="ph-duotone ph-wallet"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1.5 mt-1.5 text-[11px] font-bold text-emerald-600">
                    <i class="ph-duotone ph-check-circle text-xs"></i>
                    <span>Terverifikasi lunas masuk</span>
                </div>
            </div>
        </div>

        <!-- 3. Siswa Akademi & Program -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-3 relative overflow-hidden group hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Peserta Akademi</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-2xs group-hover:scale-110 transition-transform">
                    <i class="ph-duotone ph-graduation-cap"></i>
                </div>
            </div>
            <div>
                <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalPeserta) }}</div>
                <div class="flex items-center gap-1.5 mt-1.5 text-[11px] font-semibold text-slate-500">
                    <i class="ph-duotone ph-book-open text-xs text-blue-500"></i>
                    <span>{{ number_format($totalKursus) }} program kursus aktif</span>
                </div>
            </div>
        </div>

        <!-- 4. Antrean Verifikasi Pembayaran -->
        <div class="clean-card p-5 sm:p-6 rounded-3xl bg-white border {{ $pembayaranPending > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/80' }} shadow-2xs space-y-3 relative overflow-hidden group hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold {{ $pembayaranPending > 0 ? 'text-amber-800' : 'text-slate-400' }} uppercase tracking-wider">Verifikasi Manual</span>
                <div class="w-10 h-10 rounded-2xl {{ $pembayaranPending > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-lg shadow-2xs group-hover:scale-110 transition-transform">
                    <i class="ph-duotone ph-receipt"></i>
                </div>
            </div>
            <div>
                <div class="text-3xl font-black {{ $pembayaranPending > 0 ? 'text-amber-700' : 'text-slate-900' }} tracking-tight">
                    {{ number_format($pembayaranPending) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1.5 text-[11px] font-bold {{ $pembayaranPending > 0 ? 'text-amber-700' : 'text-slate-500' }}">
                    @if($pembayaranPending > 0)
                        <i class="ph-duotone ph-clock text-xs animate-spin"></i>
                        <span>Perlu verifikasi Owner</span>
                    @else
                        <i class="ph-duotone ph-check-circle text-xs text-emerald-600"></i>
                        <span class="text-emerald-700">Semua transfer terverifikasi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Insights Bar (Quick Stat Badges) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm shrink-0">
                <i class="ph-duotone ph-users"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pelanggan</div>
                <div class="text-sm font-black text-slate-900 truncate">{{ number_format($totalCustomers ?? 0) }} Klien</div>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
                <i class="ph-duotone ph-chats-circle"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Konsultasi Baru</div>
                <div class="text-sm font-black text-slate-900 truncate">{{ number_format($pendingConsultations ?? 0) }} Pesan</div>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm shrink-0">
                <i class="ph-duotone ph-star"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ulasan Masuk</div>
                <div class="text-sm font-black text-slate-900 truncate">{{ number_format($pendingTestimonials ?? 0) }} Ulasan</div>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                <i class="ph-duotone ph-chart-line-up"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Studio</div>
                <div class="text-sm font-black text-emerald-600 truncate">Operasional Optimal</div>
            </div>
        </div>
    </div>

    <!-- Main Operational Grid (Recent Bookings & Pending Payment Verification) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- 1. Recent Bookings (Reservasi Pemotretan Terbaru) -->
        <div class="clean-card p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center text-sm">
                        <i class="ph-duotone ph-calendar-check"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-slate-900 text-base">Reservasi Sesi Pemotretan</h2>
                        <p class="text-[11px] text-slate-400 font-medium">Aktivitas booking klien terbaru di studio.</p>
                    </div>
                </div>
                <a href="{{ route('admin.bookings.index') }}" 
                   class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 hover:text-slate-600 transition-colors group">
                    <span>Lihat Semua</span>
                    <i class="ph-duotone ph-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentBookings as $rb)
                    @php
                        $badgeClass = match($rb->status) {
                            'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Pemotretan Selesai' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'Selesai' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Dibatalkan', 'Ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                        };
                    @endphp
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50/70 hover:bg-slate-50 border border-slate-200/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-base text-slate-700 shrink-0 font-mono font-bold shadow-2xs">
                                <i class="ph-duotone ph-camera"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm truncate">
                                        {{ $rb->customer->name ?? 'Guest User' }}
                                    </span>
                                    <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600">
                                        #{{ $rb->nomor_booking }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500 font-medium">
                                    <span class="text-slate-800 font-semibold">{{ $rb->layanan->nama ?? 'Layanan Foto' }}</span>
                                    <span>&bull;</span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="ph-duotone ph-calendar-blank text-slate-400"></i>
                                        {{ $rb->tanggal ? date('d M Y', strtotime($rb->tanggal)) : 'Tanggal belum diatur' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/50">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $badgeClass }}">
                                {{ $rb->status }}
                            </span>
                            <a href="{{ route('admin.bookings.show', $rb->id) }}" 
                               class="p-2 rounded-xl bg-white hover:bg-slate-900 text-slate-600 hover:text-white border border-slate-200 hover:border-slate-900 transition-colors"
                               title="Lihat Detail Reservasi">
                                <i class="ph-duotone ph-arrow-up-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                            <i class="ph-duotone ph-calendar-blank"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Belum Ada Reservasi Terbaru</p>
                        <p class="text-[11px] text-slate-400 font-medium">Reservasi dari klien yang memesan layanan akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Recent Payments (Verifikasi Pembayaran Masuk) -->
        <div class="clean-card p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm">
                        <i class="ph-duotone ph-receipt"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-slate-900 text-base">Verifikasi Pembayaran</h2>
                        <p class="text-[11px] text-slate-400 font-medium">Antrean konfirmasi transfer bank manual klien.</p>
                    </div>
                </div>
                <a href="{{ route('admin.payments.index', ['status' => 'Menunggu Verifikasi']) }}" 
                   class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 hover:text-slate-600 transition-colors group">
                    <span>Lihat Antrean</span>
                    <i class="ph-duotone ph-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentPayments as $rp)
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/40 hover:bg-amber-50/70 border border-amber-200/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-amber-100/70 text-amber-800 flex items-center justify-center text-base shrink-0 shadow-2xs font-bold">
                                <i class="ph-duotone ph-bank"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-xs font-black text-slate-900">
                                        {{ $rp->nomor_transaksi }}
                                    </span>
                                    <span class="text-xs font-extrabold text-emerald-700">
                                        Rp {{ number_format($rp->jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500 font-medium">
                                    <span class="text-slate-800 font-semibold">{{ $rp->user->name ?? 'Pelanggan' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $rp->metode_pembayaran }}</span>
                                    <span>&bull;</span>
                                    <span>{{ date('d M, H:i', strtotime($rp->tanggal_pembayaran ?? $rp->created_at)) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-amber-200/40">
                            <a href="{{ route('admin.payments.index', ['status' => 'Menunggu Verifikasi']) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-[11px] shadow-2xs transition-all hover:scale-[1.02]">
                                <i class="ph-duotone ph-check-circle text-xs text-emerald-400"></i>
                                <span>Verifikasi</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto">
                            <i class="ph-duotone ph-check-circle"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-900">Tidak Ada Antrean Verifikasi</p>
                        <p class="text-[11px] text-slate-400 font-medium">Seluruh bukti transfer pembayaran dari klien telah berhasil diverifikasi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
