@extends('layouts.app')

@section('title', 'Verifikasi Keaslian Sertifikat — RanggaMotret.id')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Pusat Validasi</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Verifikasi Dokumen Sertifikat</h1>
        <p class="text-xs text-slate-600">Masukkan Nomor Sertifikat atau Kode QR Verifikasi untuk menguji validitas terbitan RanggaMotret Academy.</p>
    </div>

    <!-- Form -->
    <div class="clean-card p-6 rounded-2xl">
        <form method="GET" action="" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="code" value="{{ request()->query('code', $code) }}" placeholder="Contoh: VERIFY-RM-998822 atau CERT-RM-2026-0089"
                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:border-slate-900 focus:outline-none font-medium">
            <button type="submit" class="btn-dark px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider whitespace-nowrap shadow-md">
                <i class="fa-solid fa-shield-halved mr-1"></i> Validasi
            </button>
        </form>
    </div>

    <!-- Verification Output -->
    @if($code)
        @if($sertifikat)
            <div class="clean-card p-8 rounded-2xl border-emerald-300 bg-emerald-50/50 space-y-6 text-center shadow-md">
                <div class="w-14 h-14 rounded-full bg-emerald-600 text-white font-bold text-2xl flex items-center justify-center mx-auto shadow-md">
                    <i class="fa-solid fa-check"></i>
                </div>

                <div class="space-y-1">
                    <span class="text-[10px] uppercase font-extrabold text-emerald-700 tracking-widest">OFFICIAL VERIFIED DOCUMENT</span>
                    <h2 class="text-2xl font-extrabold text-slate-900">Sertifikat Sah & Terdaftar Resmi</h2>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 text-xs text-left grid grid-cols-1 sm:grid-cols-2 gap-4 shadow-sm">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Pelanggan:</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $sertifikat->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Pelatihan:</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $sertifikat->kursus->nama }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Nomor Sertifikat:</span>
                        <span class="font-mono text-slate-900 font-bold">{{ $sertifikat->nomor_sertifikat }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Kode Verifikasi:</span>
                        <span class="font-mono text-slate-900 font-bold">{{ $sertifikat->kode_verifikasi }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Tanggal Terbit:</span>
                        <span class="text-slate-700 font-semibold">{{ date('d F Y', strtotime($sertifikat->tanggal_terbit)) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Nilai Kelulusan:</span>
                        <span class="text-emerald-700 font-extrabold text-sm">{{ $sertifikat->nilai_akhir }} / 100</span>
                    </div>
                </div>
            </div>
        @else
            <div class="clean-card p-8 rounded-2xl border-rose-300 bg-rose-50/50 text-center space-y-3 shadow-md">
                <div class="w-12 h-12 rounded-full bg-rose-600 text-white text-xl font-bold flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Sertifikat Tidak Ditemukan</h3>
                <p class="text-xs text-slate-600">Kode "<span class="text-slate-900 font-mono font-bold">{{ $code }}</span>" tidak ditemukan pada basis data penerbitan sertifikat RanggaMotret.</p>
            </div>
        @endif
    @endif
</div>
@endsection
