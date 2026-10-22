@extends('layouts.admin')

@section('title', 'Riwayat Reservasi Fotografi — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Reservasi Fotografi</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar histori pemesanan sesi foto, status konfirmasi jadwal, bukti transaksi, dan rincian pemotretan Anda.</p>
        </div>
        <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md inline-flex items-center gap-2 self-start sm:self-auto hover:scale-[1.02] transition-transform">
            <i class="ph-duotone ph-plus text-sm"></i>
            <span>Buat Reservasi Baru</span>
        </a>
    </div>

    <!-- Booking History List -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6">
        @forelse($bookings as $b)
            @php
                $isPaid = ($b->pembayaran && $b->pembayaran->status === 'Dibayar') || in_array($b->status, ['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai']);
                $cBadgeClass = match($b->status) {
                    'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                    'Selesai' => 'bg-blue-50 text-blue-800 border-blue-200',
                    'Dibatalkan', 'Ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                };
            @endphp
            <div class="clean-card p-6 sm:p-7 rounded-3xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 border border-slate-200/80 hover:shadow-md transition-shadow bg-white">
                <div class="space-y-3 flex-1">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="font-mono text-xs font-extrabold text-slate-900 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">{{ $b->nomor_booking }}</span>
                        <span class="text-xs font-bold text-slate-400">• Dipesan: {{ $b->created_at->format('d M Y') }}</span>
                    </div>

                    <div>
                        <h3 class="text-lg font-black text-slate-900">{{ $b->layanan->nama }}</h3>
                        <p class="text-xs text-slate-600 font-bold mt-0.5">
                            Paket: <span class="text-slate-900">{{ $b->paket->nama_paket }}</span> • Rp {{ number_format($b->paket->harga ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-4 text-xs text-slate-600 font-medium pt-1">
                        <span class="flex items-center gap-1.5"><i class="ph-duotone ph-calendar-blank text-slate-500"></i> {{ date('d M Y', strtotime($b->tanggal)) }} ({{ substr($b->waktu, 0, 5) }} WIB)</span>
                        <span class="flex items-center gap-1.5"><i class="ph-duotone ph-map-pin text-slate-500"></i> {{ $b->lokasi }}</span>
                        <span class="flex items-center gap-1.5"><i class="ph-duotone ph-users text-slate-500"></i> Kru: {{ $b->paket->jumlah_fotografer ?? 1 }} Fotografer</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                    <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase border shadow-sm {{ $cBadgeClass }}">
                        {{ $b->status }}
                    </span>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        @if($b->galeriFotos->count() > 0)
                            <a href="{{ route('customer.proofing.show', $b->id) }}" class="px-3.5 py-2 rounded-xl bg-purple-600 text-white hover:bg-purple-700 text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                                <i class="ph-duotone ph-images"></i>
                                <span>Proofing ({{ $b->galeriFotos->count() }})</span>
                            </a>
                        @endif

                        @if(!$isPaid)
                            <a href="{{ route('payments.create', ['booking_id' => $b->id]) }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black flex items-center gap-1.5 shadow-sm transition-colors">
                                <i class="ph-duotone ph-credit-card"></i>
                                <span>Bayar</span>
                            </a>
                        @endif

                        <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold block text-center">
                            Detail Reservasi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="clean-card p-12 sm:p-16 text-center rounded-3xl space-y-4 border border-slate-200 bg-white">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    <i class="ph-duotone ph-calendar-blank"></i>
                </div>
                <div class="space-y-1 max-w-sm mx-auto">
                    <h3 class="text-base font-black text-slate-900">Belum Ada Riwayat Reservasi</h3>
                    <p class="text-xs text-slate-500 font-medium">Anda belum pernah membuat reservasi sesi pemotretan.</p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 shadow-md">
                        <i class="ph-duotone ph-plus text-sm"></i>
                        <span>Mulai Reservasi Pertama</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
