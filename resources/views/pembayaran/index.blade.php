@extends('layouts.admin')

@section('title', 'Riwayat Pembayaran — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Riwayat Pembayaran</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Riwayat Transaksi Pembayaran</h1>
            <p class="text-xs text-slate-600 font-medium">Daftar konfirmasi pembayaran transfer bank dan QRIS pesanan Anda.</p>
        </div>
        <a href="{{ route('payments.create') }}" class="btn-dark px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-md">
            <i class="ph-duotone ph-upload-simple"></i> Unggah Bukti Pembayaran
        </a>
    </div>

    <div class="clean-card rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode Transaksi</th>
                        <th class="p-4">ID Reservasi</th>
                        <th class="p-4">Jumlah</th>
                        <th class="p-4">Metode</th>
                        <th class="p-4">Tanggal Unggah</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembayarans as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-mono font-bold text-slate-900">{{ $p->nomor_transaksi }}</td>
                            <td class="p-4 font-mono text-slate-700">{{ $p->booking ? $p->booking->nomor_booking : ($p->kursus ? $p->kursus->nama : '-') }}</td>
                            <td class="p-4 font-extrabold text-slate-900">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td class="p-4">{{ $p->metode_pembayaran }}</td>
                            <td class="p-4 text-slate-500">{{ date('d M Y, H:i', strtotime($p->tanggal_pembayaran ?? $p->created_at)) }}</td>
                            <td class="p-4">
                                @php
                                    $payBadgeClass = match($p->status) {
                                        'Dibayar' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'Ditolak', 'Dikembalikan' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        'Belum Dibayar' => 'bg-slate-100 text-slate-700 border-slate-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $payBadgeClass }}">
                                        {{ $p->status }}
                                    </span>
                                    @if($p->status === 'Belum Dibayar' && $p->booking_id)
                                        <a href="{{ route('payments.create', ['booking_id' => $p->booking_id]) }}" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 text-[10px] shadow-sm whitespace-nowrap">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                @if($p->invoice)
                                    <a href="{{ route('invoices.show', $p->invoice->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 text-[11px] inline-block shadow-sm">
                                        <i class="fa-solid fa-file-invoice mr-1"></i> PDF
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500 font-medium">Belum ada riwayat transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
