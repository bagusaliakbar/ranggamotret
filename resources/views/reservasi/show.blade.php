@extends('layouts.admin')

@section('title', 'Detail Booking — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('customer.bookings.index') }}" class="hover:text-slate-700 transition-colors">Riwayat Booking</a>
                <span>/</span>
                <span class="text-slate-700">{{ $booking->nomor_booking }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-extrabold text-slate-900">{{ $booking->nomor_booking }}</h1>
                @php
                    $bShowBadgeClass = match($booking->status) {
                        'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'Selesai' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'Dibatalkan', 'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                    };
                @endphp
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold uppercase border {{ $bShowBadgeClass }}">
                    {{ $booking->status }}
                </span>
            </div>
            <p class="text-xs text-slate-600 font-medium mt-1">Dibuat pada {{ date('d F Y, H:i', strtotime($booking->created_at)) }}</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('customer.bookings.index') }}" class="btn-outline px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5">
                <i class="ph-duotone ph-arrow-left"></i> Kembali
            </a>
            @if($booking->pembayaran && $booking->pembayaran->invoice)
                <a href="{{ route('invoices.show', $booking->pembayaran->invoice->id) }}" target="_blank" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5">
                    <i class="ph-duotone ph-receipt"></i> Cetak Invoice
                </a>
            @endif
        </div>
    </div>

    <!-- Booking Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <!-- Layanan Card -->
            <div class="clean-card p-6 rounded-2xl space-y-4">
                <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Informasi Sesi Fotografi</h3>
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 font-medium block">Layanan:</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $booking->layanan->nama }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-medium block">Paket:</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $booking->paket->nama_paket }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-medium block">Tanggal Pemotretan:</span>
                        <span class="font-bold text-slate-900">{{ date('d F Y', strtotime($booking->tanggal)) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-medium block">Waktu Sesi:</span>
                        <span class="font-bold text-slate-900">{{ substr($booking->waktu, 0, 5) }} WIB</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-500 font-medium block">Lokasi:</span>
                        <span class="font-bold text-slate-900">{{ $booking->lokasi }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-500 font-medium block">Catatan Tambahan:</span>
                        <span class="text-slate-700 italic">{{ $booking->kebutuhan_khusus ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Proofing Gallery -->
            @if($booking->galeriFotos->count() > 0)
                <div class="clean-card p-6 rounded-2xl space-y-4 border-purple-200 bg-purple-50/20">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">Galeri Proofing Foto</h3>
                            <p class="text-xs text-slate-600 font-medium">Tersedia {{ $booking->galeriFotos->count() }} foto untuk ditinjau dan dipilih.</p>
                        </div>
                        <a href="{{ route('customer.proofing.show', $booking->id) }}" class="px-5 py-2.5 rounded-xl bg-purple-700 text-white font-bold text-xs hover:bg-purple-800 shadow-md">
                            Buka Panel Proofing
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Side: Rincian Harga & Pembayaran -->
        <div class="space-y-6">
            <div class="clean-card p-6 rounded-2xl space-y-4">
                <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Rincian Pembayaran</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Harga Paket ({{ $booking->paket->nama_paket }})</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($booking->paket->harga ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-900 font-extrabold text-sm pt-2 border-t border-slate-100">
                        <span>Total Tagihan</span>
                        <span class="text-slate-900">Rp {{ number_format($booking->paket->harga ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(!$booking->pembayaran || $booking->pembayaran->status !== 'Dibayar')
                    <div class="pt-3 space-y-2">
                        <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}" class="btn-dark w-full py-3.5 rounded-xl text-center text-xs font-bold block shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-credit-card"></i> Bayar Tagihan (QRIS / VA / Transfer)
                        </a>
                        @if($booking->pembayaran && $booking->pembayaran->status === 'Menunggu Verifikasi')
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-medium text-center">
                                <i class="fa-solid fa-clock mr-1"></i> Bukti transfer manual sedang diverifikasi admin.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="pt-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-medium">Status:</span>
                            <span class="font-extrabold text-emerald-800 uppercase px-2 py-0.5 rounded bg-emerald-100 border border-emerald-300 text-[10px]">
                                <i class="fa-solid fa-circle-check mr-1"></i> Lunas / Terverifikasi
                            </span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Metode:</span>
                            <span class="font-bold text-slate-900">{{ $booking->pembayaran->metode_pembayaran }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Dibayar Pada:</span>
                            <span class="text-slate-900">{{ $booking->pembayaran->tanggal_pembayaran ? date('d M Y, H:i', strtotime($booking->pembayaran->tanggal_pembayaran)) : '-' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
