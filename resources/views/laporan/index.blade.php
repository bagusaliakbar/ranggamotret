@extends('layouts.admin')

@section('title', 'Laporan Analitis — Admin RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Laporan Analitis</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">Laporan Analitis Operasional & Keuangan</h1>
        <p class="text-xs text-slate-600 font-medium">Rekapitulasi transaksi layanan fotografi, akademi kursus, dan sertifikasi.</p>
    </div>

    <!-- Summary Metric Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan Terverifikasi</div>
            <div class="text-3xl font-extrabold text-slate-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-600 font-bold"><i class="ph-duotone ph-check-circle mr-1"></i> Transaksi Lunas</div>
        </div>

        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendapatan Layanan Fotografi</div>
            <div class="text-3xl font-extrabold text-slate-900">Rp {{ number_format($pendapatanLayanan, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 font-medium">{{ number_format($totalBookingDone) }} booking selesai</div>
        </div>

        <div class="clean-card p-6 rounded-2xl space-y-2">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendapatan Akademi Kursus</div>
            <div class="text-3xl font-extrabold text-slate-900">Rp {{ number_format($pendapatanKursus, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 font-medium">{{ number_format($totalPesertaLulus) }} kelulusan sertifikat terbit</div>
        </div>
    </div>
</div>
@endsection
