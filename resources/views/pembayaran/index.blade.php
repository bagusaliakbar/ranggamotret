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
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar transaksi pembayaran sesi foto dan kursus akademi Anda melalui Midtrans Payment Gateway.</p>
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
        $unpaidCount = $pembayarans->whereIn('status', ['Belum Dibayar', 'Menunggu Pembayaran', 'Menunggu Verifikasi'])->count();
    @endphp
    @if($unpaidCount > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg shrink-0 shadow-xs">
                    <i class="ph-duotone ph-warning-circle"></i>
                </div>
                <div class="text-xs">
                    <h4 class="font-black text-slate-900">Ada {{ $unpaidCount }} Transaksi Belum Selesai</h4>
                    <p class="text-amber-900 font-medium mt-0.5 leading-relaxed">
                        Silakan selesaikan pembayaran via Midtrans Gateway agar jadwal sesi foto terkunci atau akses materi kursus terbuka otomatis.
                    </p>
                </div>
            </div>
            <a href="{{ route('payments.create') }}" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shrink-0 shadow-xs self-start sm:self-auto">
                <i class="ph-duotone ph-lightning text-emerald-400"></i>
                <span>Bayar Sekarang (Instan)</span>
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
                        <th class="p-4 text-center">Status & Tindakan</th>
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

                            <!-- 6. Status & Tindakan (Terpadu) -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    @if($p->status === 'Dibayar')
                                        <!-- Badge Status Lunas -->
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1">
                                            <i class="ph-duotone ph-check-circle text-xs text-emerald-600"></i>
                                            <span>Dibayar</span>
                                        </span>

                                        <!-- Aksi Invoice PDF jika lunas -->
                                        <a href="{{ route('invoices.show', $p->invoice ? $p->invoice->id : $p->id) }}" target="_blank" 
                                           class="px-2.5 py-1 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-[10px] inline-flex items-center gap-1 transition-all shadow-2xs hover:scale-105 active:scale-95"
                                           title="Unduh / Cetak Bukti Invoice Resmi">
                                            <i class="ph-duotone ph-receipt text-xs text-emerald-400"></i>
                                            <span>Invoice PDF</span>
                                        </a>

                                    @elseif(in_array($p->status, ['Menunggu Verifikasi', 'Menunggu Pembayaran']))
                                        <!-- Badge Status Menunggu -->
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border bg-amber-50 text-amber-900 border-amber-300 inline-flex items-center gap-1">
                                            <i class="ph-duotone ph-clock text-xs text-amber-600 animate-pulse"></i>
                                            <span>Menunggu Pembayaran</span>
                                        </span>

                                        <a href="{{ route('payments.sync', $p->id) }}" 
                                           class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] border border-slate-200 shadow-2xs inline-flex items-center gap-1 transition-all" 
                                           title="Periksa status transaksi ke server Midtrans">
                                            <i class="ph-duotone ph-arrows-clockwise text-xs text-indigo-600"></i>
                                            <span>Cek Status</span>
                                        </a>

                                    @elseif($p->status === 'Belum Dibayar')
                                        <!-- Badge Status Belum Dibayar -->
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border bg-amber-100 text-amber-900 border-amber-300 inline-flex items-center gap-1">
                                            <i class="ph-duotone ph-warning-circle text-xs text-amber-700"></i>
                                            <span>Belum Dibayar</span>
                                        </span>

                                        <!-- Aksi Bayar Sekarang -->
                                        @if(!empty($p->snap_token))
                                            <button type="button" 
                                                    onclick="payWithSnapDirect('{{ $p->snap_token }}', '{{ $p->nomor_transaksi }}')"
                                                    class="px-3 py-1 rounded-xl bg-slate-900 hover:bg-black text-white font-black text-[10px] shadow-xs inline-flex items-center gap-1 hover:scale-105 active:scale-95 transition-all">
                                                <i class="ph-duotone ph-lightning text-amber-400 text-xs"></i>
                                                <span>Bayar Sekarang</span>
                                            </button>
                                        @elseif($p->booking_id)
                                            <a href="{{ route('payments.create', ['booking_id' => $p->booking_id]) }}" 
                                               class="px-3 py-1 rounded-xl bg-slate-900 hover:bg-black text-white font-black text-[10px] shadow-xs inline-flex items-center gap-1 hover:scale-105 active:scale-95 transition-all">
                                                <i class="ph-duotone ph-credit-card text-emerald-400 text-xs"></i>
                                                <span>Bayar Sekarang</span>
                                            </a>
                                        @elseif($p->kursus_id)
                                            <a href="{{ route('payments.create', ['kursus_id' => $p->kursus_id]) }}" 
                                               class="px-3 py-1 rounded-xl bg-slate-900 hover:bg-black text-white font-black text-[10px] shadow-xs inline-flex items-center gap-1 hover:scale-105 active:scale-95 transition-all">
                                                <i class="ph-duotone ph-credit-card text-emerald-400 text-xs"></i>
                                                <span>Bayar Sekarang</span>
                                            </a>
                                        @endif

                                        @if($isGateway)
                                            <a href="{{ route('payments.sync', $p->id) }}" 
                                               class="px-2 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] border border-slate-200 shadow-2xs inline-flex items-center gap-1 transition-all" 
                                               title="Sinkronisasi Status Gateway">
                                                <i class="ph-duotone ph-arrows-clockwise text-xs"></i>
                                                <span>Sync</span>
                                            </a>
                                        @endif

                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border bg-rose-50 text-rose-800 border-rose-200 inline-flex items-center gap-1">
                                            <i class="ph-duotone ph-x-circle text-xs text-rose-600"></i>
                                            <span>{{ $p->status }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-500 space-y-2">
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

@push('scripts')
@if(!empty($midtransSnapUrl))
<script src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey ?? '' }}"></script>
<script>
    function payWithSnapDirect(snapToken, trxNumber) {
        if (typeof window.snap === 'undefined') {
            alert('SDK Pembayaran Midtrans sedang dimuat. Mohon tunggu beberapa saat.');
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment Success:', result);
                window.location.href = "{{ route('payments.finish') }}?order_id=" + (result.order_id || trxNumber) + "&transaction_status=settlement";
            },
            onPending: function(result) {
                console.log('Payment Pending:', result);
                window.location.href = "{{ route('payments.finish') }}?order_id=" + (result.order_id || trxNumber) + "&transaction_status=pending";
            },
            onError: function(result) {
                console.error('Payment Error:', result);
                alert('Pembayaran gagal atau dibatalkan.');
            },
            onClose: function() {
                console.log('Customer closed the payment popup');
            }
        });
    }
</script>
@endif
@endpush
@endsection
