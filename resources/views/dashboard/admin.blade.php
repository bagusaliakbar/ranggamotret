@extends('layouts.admin')

@section('title', 'Admin Dashboard — RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <span>Dashboard</span>
                <span>/</span>
                <span class="text-slate-700">Ringkasan Admin</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Panel Ringkasan Admin</h1>
            <p class="text-xs text-slate-600 font-medium">Metrik statistik operasional studio layanan dan akademi kursus.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.layanan.index') }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all">
                <i class="fa-solid fa-camera-retro mr-1 text-slate-600"></i> Kelola Layanan
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md">
                <i class="fa-solid fa-camera mr-1"></i> Kelola Booking
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn-outline px-4 py-2.5 rounded-xl text-xs font-bold">
                <i class="fa-solid fa-chart-pie mr-1"></i> Laporan Keuangan
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Total Bookings</span>
                <i class="fa-solid fa-calendar-check text-slate-700 text-base"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ number_format($totalBooking) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">{{ $bookingPending }} menunggu persetujuan</div>
        </div>

        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Pendapatan Kotor</span>
                <i class="fa-solid fa-wallet text-slate-700 text-base"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-600 font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Terverifikasi lunas</div>
        </div>

        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Peserta Kursus</span>
                <i class="fa-solid fa-graduation-cap text-slate-700 text-base"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ number_format($totalPeserta) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">{{ number_format($totalKursus) }} program aktif</div>
        </div>

        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Verifikasi Pembayaran</span>
                <i class="fa-solid fa-receipt text-slate-700 text-base"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ number_format($pembayaranPending) }}</div>
            <div class="text-[11px] text-amber-600 font-bold"><i class="fa-solid fa-clock mr-1"></i> Perlu verifikasi admin</div>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Bookings -->
        <div class="clean-card p-6 rounded-2xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Booking Terakhir</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs font-bold text-slate-900 hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-3 text-xs">
                @foreach($recentBookings as $rb)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <div class="font-bold text-slate-900">{{ $rb->customer->name ?? 'Guest' }} &bull; <span class="font-mono text-slate-700">{{ $rb->nomor_booking }}</span></div>
                            <div class="text-slate-500 font-medium text-[11px]">{{ $rb->layanan->nama ?? 'Layanan' }} - {{ $rb->tanggal ? date('d M Y', strtotime($rb->tanggal)) : '-' }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border
                            @if(in_array($rb->status, ['Dibayar', 'Disetujui'])) bg-emerald-100 text-emerald-800 border-emerald-300
                            @else bg-amber-100 text-amber-800 border-amber-300 @endif">
                            {{ $rb->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="clean-card p-6 rounded-2xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Pembayaran Perlu Verifikasi</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-bold text-slate-900 hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-3 text-xs">
                @foreach($recentPayments as $rp)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <div class="font-bold text-slate-900">{{ $rp->nomor_transaksi }} &bull; Rp {{ number_format($rp->jumlah, 0, ',', '.') }}</div>
                            <div class="text-slate-500 font-medium text-[11px]">{{ $rp->metode_pembayaran }} &bull; {{ date('d M Y, H:i', strtotime($rp->tanggal_pembayaran ?? $rp->created_at)) }}</div>
                        </div>
                        <a href="{{ route('admin.payments.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold text-[10px] uppercase">
                            Verifikasi
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
