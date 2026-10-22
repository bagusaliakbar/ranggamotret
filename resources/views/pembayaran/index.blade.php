@extends('layouts.admin')

@section('title', 'Riwayat Transaksi Pembayaran — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Riwayat Pembayaran</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Riwayat Transaksi Pembayaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar transaksi pembayaran sesi foto dan kursus akademi Anda secara instan atau transfer manual.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-sm hover:scale-105 transition-all">
                <i class="ph-duotone ph-camera text-base"></i>
                <span>Pesan Sesi Foto Baru</span>
            </a>
            <a href="{{ route('public.courses') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 shadow-xs transition-all">
                <i class="ph-duotone ph-graduation-cap text-base"></i>
                <span>Jelajahi Kursus</span>
            </a>
        </div>
    </div>

    <!-- Helpful Order Info Notice -->
    @php
        $pendingCount = $pembayarans->whereIn('status', ['Belum Dibayar', 'Menunggu Verifikasi'])->count();
    @endphp
    @if($pendingCount > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg shrink-0 shadow-xs">
                    <i class="ph-duotone ph-info"></i>
                </div>
                <div class="text-xs">
                    <h4 class="font-black text-slate-900">Ada {{ $pendingCount }} Transaksi Menunggu Penyelesaian</h4>
                    <p class="text-amber-900 font-medium mt-0.5 leading-relaxed">
                        Anda dapat melanjutkan pembayaran dengan menekan tombol <strong>Bayar Sekarang</strong>. Anda juga <strong>tetap bisa melakukan pemesanan sesi foto atau kursus baru</strong> lainnya kapan saja.
                    </p>
                </div>
            </div>
            <a href="{{ route('payments.create') }}" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shrink-0 shadow-xs self-start sm:self-auto">
                <i class="ph-duotone ph-upload-simple"></i>
                <span>Unggah Bukti Manual</span>
            </a>
        </div>
    @endif

    <!-- Payment History Table -->
    <div class="clean-card rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">Kode Transaksi</th>
                        <th class="p-4">Layanan / Program</th>
                        <th class="p-4">Total Tagihan</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">Tanggal Transaksi</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi / Tindakan</th>
                        <th class="p-4 text-center">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembayarans as $p)
                        @php
                            $isCourse = !empty($p->kursus_id) || str_contains($p->nomor_transaksi, 'KRS');
                            $isBooking = !empty($p->booking_id) || str_contains($p->nomor_transaksi, 'BK');
                            $isGateway = str_contains($p->metode_pembayaran ?? '', 'Midtrans') || !empty($p->snap_token);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $p->status === 'Belum Dibayar' ? 'bg-amber-50/20' : '' }}">
                            
                            <!-- 1. Kode Transaksi -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-slate-900 text-xs">{{ $p->nomor_transaksi }}</span>
                                    @if($isCourse)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Kursus
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Foto
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Layanan / Program (Jelas untuk Booking Foto & Kursus) -->
                            <td class="p-4">
                                @if($p->booking)
                                    <div class="font-black text-slate-900 text-xs flex items-center gap-1.5">
                                        <i class="ph-duotone ph-camera text-slate-600"></i>
                                        <span>{{ $p->booking->layanan->nama ?? 'Sesi Pemotretan' }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                        Paket: <strong>{{ $p->booking->paket->nama_paket ?? '-' }}</strong> • Kode: <code class="font-mono text-slate-700">{{ $p->booking->nomor_booking }}</code>
                                    </div>
                                @elseif($p->kursus)
                                    <div class="font-black text-slate-900 text-xs flex items-center gap-1.5">
                                        <i class="ph-duotone ph-graduation-cap text-slate-600"></i>
                                        <span>{{ $p->kursus->nama }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                        Akademi Online • Tingkat: <strong>{{ ucfirst($p->kursus->level ?? 'Semua') }}</strong> • <code class="font-mono text-slate-700">KRS-{{ $p->kursus->id }}</code>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>

                            <!-- 3. Total Tagihan -->
                            <td class="p-4 font-black text-slate-900 text-xs whitespace-nowrap">
                                Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </td>

                            <!-- 4. Metode Bayar -->
                            <td class="p-4 whitespace-nowrap">
                                @if($isGateway)
                                    <span class="inline-flex items-center gap-1 text-slate-800 font-bold text-xs">
                                        <i class="ph-duotone ph-lightning text-emerald-600 text-sm"></i>
                                        <span>Midtrans Snap (Gateway)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-slate-800 font-bold text-xs">
                                        <i class="ph-duotone ph-bank text-indigo-600 text-sm"></i>
                                        <span>{{ $p->metode_pembayaran ?: 'Transfer Bank' }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 5. Tanggal Transaksi -->
                            <td class="p-4 text-slate-500 whitespace-nowrap text-[11px] font-medium">
                                {{ date('d M Y, H:i', strtotime($p->tanggal_pembayaran ?? $p->created_at)) }}
                            </td>

                            <!-- 6. Status Transaksi -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @php
                                    $payBadgeClass = match($p->status) {
                                        'Dibayar' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'Ditolak', 'Dikembalikan' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        'Belum Dibayar' => 'bg-amber-100 text-amber-900 border-amber-300 font-black',
                                        default => 'bg-sky-50 text-sky-800 border-sky-200',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $payBadgeClass }}">
                                    {{ $p->status }}
                                </span>
                            </td>

                            <!-- 7. Aksi / Tindakan -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($p->status !== 'Dibayar')
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($p->booking_id)
                                            <a href="{{ route('payments.create', ['booking_id' => $p->booking_id]) }}" 
                                               class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-black text-[11px] shadow-sm inline-flex items-center gap-1 hover:scale-105 active:scale-95 transition-all">
                                                <i class="ph-duotone ph-credit-card text-emerald-400"></i>
                                                <span>Bayar Sekarang</span>
                                            </a>
                                        @elseif($p->kursus_id)
                                            <a href="{{ route('payments.create', ['kursus_id' => $p->kursus_id]) }}" 
                                               class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-black text-[11px] shadow-sm inline-flex items-center gap-1 hover:scale-105 active:scale-95 transition-all">
                                                <i class="ph-duotone ph-credit-card text-emerald-400"></i>
                                                <span>Bayar Sekarang</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('payments.sync', $p->id) }}" 
                                           class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] border border-slate-200 shadow-xs inline-flex items-center gap-1 transition-all" 
                                           title="Cek Status Pembayaran ke Gateway">
                                            <i class="ph-duotone ph-arrows-clockwise text-xs"></i>
                                            <span>Cek Status</span>
                                        </a>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                        <i class="ph-duotone ph-check-circle text-base"></i>
                                        <span>Lunas</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 8. Invoice PDF -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($p->invoice)
                                    <a href="{{ route('invoices.show', $p->invoice->id) }}" target="_blank" 
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-800 font-extrabold text-[11px] inline-flex items-center gap-1.5 border border-slate-200 transition-all shadow-xs">
                                        <i class="ph-duotone ph-receipt text-emerald-600"></i>
                                        <span>PDF</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-500 space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                    <i class="ph-duotone ph-receipt"></i>
                                </div>
                                <div class="font-extrabold text-slate-800 text-sm">Belum Ada Riwayat Transaksi</div>
                                <p class="text-xs text-slate-400 font-medium">Anda belum melakukan transaksi reservasi sesi foto maupun pembelian kursus akademi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
