@extends('layouts.admin')

@section('title', 'Layanan Fotografi Saya — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Layanan Fotografi Saya</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar layanan fotografi yang telah Anda pesan, jadwal sesi pemotretan terkonfirmasi, dan akses galeri hasil foto.</p>
        </div>

        <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md inline-flex items-center gap-2 self-start sm:self-auto hover:scale-[1.02] transition-transform">
            <i class="ph-duotone ph-plus text-sm"></i>
            <span>Reservasi Layanan Baru</span>
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Sesi Aktif / Terjadwal -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-calendar-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ $activeBookings->count() }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Sesi dalam persiapan / jadwal</div>
        </div>

        <!-- 2. Menunggu Pembayaran -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Bayar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-credit-card"></i>
                </div>
            </div>
            @php
                $unpaidCount = $bookings->filter(fn($b) => in_array($b->status, ['Menunggu Pembayaran', 'Menunggu Konfirmasi']) && (!$b->pembayaran || $b->pembayaran->status !== 'Dibayar'))->count();
            @endphp
            <div class="text-2xl font-black text-amber-600">{{ $unpaidCount }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Tagihan sesi belum lunas</div>
        </div>

        <!-- 3. Sesi Selesai -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $completedBookings->count() }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Pemotretan terlaksana</div>
        </div>

        <!-- 4. Foto Proofing Siap -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Foto Proofing</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-images"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-600">{{ $totalPhotosAvailable }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Foto siap dipilih / diunduh</div>
        </div>
    </div>

    <!-- Ordered Services List -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ph-duotone ph-camera text-slate-700"></i>
                <span>Layanan Fotografi yang Dipesan</span>
            </h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $bookings->count() }} Layanan Terdaftar</span>
        </div>

        @if($bookings->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($bookings as $b)
                    @php
                        $isPaid = ($b->pembayaran && $b->pembayaran->status === 'Dibayar') || in_array($b->status, ['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai']);
                        $statusBadgeClass = match($b->status) {
                            'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'Selesai' => 'bg-blue-50 text-blue-800 border-blue-200',
                            'Dibatalkan', 'Ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                            default => 'bg-amber-50 text-amber-800 border-amber-200',
                        };
                    @endphp
                    <div class="clean-card rounded-3xl overflow-hidden flex flex-col justify-between group border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 bg-white">
                        <div>
                            <!-- Thumbnail & Category Tag -->
                            <div class="aspect-[16/9] overflow-hidden relative bg-slate-100">
                                <img src="{{ $b->layanan->thumbnail }}" alt="{{ $b->layanan->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                <div class="absolute top-3 left-3 flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-900/90 text-white text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm shadow-sm">
                                        {{ $b->layanan->kategori ?? 'Fotografi' }}
                                    </span>
                                </div>

                                <div class="absolute top-3 right-3">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border shadow-sm backdrop-blur-sm {{ $statusBadgeClass }}">
                                        {{ $b->status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Body Info -->
                            <div class="p-6 space-y-4">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-400">
                                        <span>Kode: {{ $b->nomor_booking }}</span>
                                        <span class="font-sans font-bold text-slate-800">Rp {{ number_format($b->paket->harga ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <h3 class="text-lg font-black text-slate-900 group-hover:text-slate-700 transition-colors">
                                        {{ $b->layanan->nama }}
                                    </h3>
                                    <p class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                                        <i class="ph-duotone ph-package text-slate-400"></i>
                                        <span>Paket: <strong class="text-slate-900">{{ $b->paket->nama_paket }}</strong></span>
                                    </p>
                                </div>

                                <!-- Schedule & Venue Details -->
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs text-slate-700 font-medium">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-duotone ph-calendar-blank text-slate-500 text-sm shrink-0"></i>
                                        <span>Jadwal Sesi: <strong class="text-slate-900">{{ date('d M Y', strtotime($b->tanggal)) }}</strong> ({{ substr($b->waktu, 0, 5) }} WIB)</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <i class="ph-duotone ph-map-pin text-slate-500 text-sm shrink-0 mt-0.5"></i>
                                        <span class="line-clamp-1">Lokasi: <strong class="text-slate-900">{{ $b->lokasi }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2 pt-1 border-t border-slate-200/60 text-[11px] text-slate-500">
                                        <i class="ph-duotone ph-users text-slate-400"></i>
                                        <span>Kru: {{ $b->paket->jumlah_fotografer ?? 1 }} Fotografer • {{ $b->paket->durasi ?? 'Sesi' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Footer -->
                        <div class="p-6 pt-0 space-y-2.5">
                            @if($b->galeriFotos->count() > 0)
                                <a href="{{ route('customer.proofing.show', $b->id) }}" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition-all hover:scale-[1.01] active:scale-95">
                                    <i class="ph-duotone ph-images text-base"></i>
                                    <span>Buka Galeri Proofing ({{ $b->galeriFotos->count() }} Foto)</span>
                                </a>
                            @endif

                            @if(!$isPaid)
                                <a href="{{ route('payments.create', ['booking_id' => $b->id]) }}" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black flex items-center justify-center gap-2 shadow-sm transition-all hover:scale-[1.01] active:scale-95">
                                    <i class="ph-duotone ph-credit-card text-base"></i>
                                    <span>Selesaikan Pembayaran Sekarang</span>
                                </a>
                            @endif

                            <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn-dark w-full py-2.5 rounded-xl text-center text-xs font-bold flex items-center justify-center gap-2 shadow-sm hover:scale-[1.01] active:scale-95 transition-all">
                                <i class="ph-duotone ph-arrow-right text-sm"></i>
                                <span>Lihat Rincian Layanan & Sesi</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="clean-card p-12 sm:p-16 text-center rounded-3xl space-y-4 border border-slate-200 bg-white">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    <i class="ph-duotone ph-camera"></i>
                </div>
                <div class="space-y-1.5 max-w-md mx-auto">
                    <h3 class="text-lg font-black text-slate-900">Belum Ada Layanan Fotografi yang Dipesan</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Abadikan momen bahagia pernikahan, prewedding, wisuda, atau personal branding Anda bersama tim fotografer profesional RanggaMotret.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 shadow-md hover:scale-[1.02] transition-transform">
                        <i class="ph-duotone ph-plus text-sm"></i>
                        <span>Pesan Layanan Fotografi Sekarang</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
