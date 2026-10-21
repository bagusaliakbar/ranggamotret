@extends('layouts.admin')

@section('title', 'Daftar Booking Saya — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Riwayat Booking Saya</h1>
            <p class="text-xs text-slate-600 font-medium">Kelola jadwal sesi pemotretan, unggah pembayaran, dan akses galeri proofing.</p>
        </div>
        <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-md">
            <i class="fa-solid fa-plus"></i> Booking Pemotretan Baru
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($bookings as $b)
            <div class="clean-card p-6 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-extrabold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">{{ $b->nomor_booking }}</span>
                        <h3 class="text-lg font-extrabold text-slate-900">{{ $b->layanan->nama }}</h3>
                    </div>

                    <div class="flex flex-wrap gap-4 text-xs text-slate-600 font-medium">
                        <span><i class="fa-solid fa-box text-slate-400 mr-1"></i> Paket: {{ $b->paket->nama_paket }} (Rp {{ number_format($b->paket->harga ?? 0, 0, ',', '.') }})</span>
                        <span><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> {{ date('d M Y', strtotime($b->tanggal)) }} ({{ substr($b->waktu, 0, 5) }} WIB)</span>
                        <span><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> {{ $b->lokasi }}</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full md:w-auto">
                    @php
                        $cBadgeClass = match($b->status) {
                            'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'Selesai' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'Dibatalkan', 'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
                            default => 'bg-amber-100 text-amber-800 border-amber-300',
                        };
                    @endphp
                    <span class="px-3.5 py-1.5 rounded-full text-[10px] font-extrabold uppercase border {{ $cBadgeClass }}">
                        {{ $b->status }}
                    </span>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold block text-center">
                            Detail Sesi
                        </a>
                        @if($b->galeriFotos->count() > 0)
                            <a href="{{ route('customer.proofing.show', $b->id) }}" class="px-4 py-2 rounded-xl bg-purple-600 text-white hover:bg-purple-700 text-xs font-bold block text-center shadow-sm">
                                <i class="fa-solid fa-images mr-1"></i> Proofing
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="clean-card p-12 text-center rounded-2xl space-y-3">
                <i class="fa-solid fa-camera text-4xl text-slate-300"></i>
                <h3 class="text-base font-bold text-slate-900">Belum Ada Riwayat Pemesanan</h3>
                <p class="text-xs text-slate-500 font-medium">Anda belum memiliki pesanan sesi foto. Klik tombol di atas untuk memulai reservasi.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
