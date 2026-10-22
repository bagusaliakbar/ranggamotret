@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran — Admin RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Verifikasi Pembayaran</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">Verifikasi Pembayaran</h1>
        <p class="text-xs text-slate-600 font-medium">Tinjau bukti pembayaran transfer bank/QRIS dan perbarui status transaksi.</p>
    </div>

    <div class="clean-card rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode Transaksi</th>
                        <th class="p-4">ID Reservasi</th>
                        <th class="p-4">Jumlah</th>
                        <th class="p-4">Bukti Upload</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Tindakan Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembayarans as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-mono font-bold text-slate-900">{{ $p->nomor_transaksi }}</td>
                            <td class="p-4 font-mono text-slate-700">{{ $p->booking ? $p->booking->nomor_booking : ($p->kursus ? $p->kursus->nama : '-') }}</td>
                            <td class="p-4 font-extrabold text-slate-900">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td class="p-4">
                                @if($p->bukti_pembayaran)
                                    <a href="{{ asset($p->bukti_pembayaran) }}" target="_blank" class="text-slate-900 font-bold hover:underline inline-flex items-center gap-1">
                                        <i class="ph-duotone ph-image"></i> Lihat Gambar
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @php
                                    $pStatusClass = match($p->status) {
                                        'Dibayar' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'Ditolak', 'Dikembalikan' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $pStatusClass }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <form method="POST" action="{{ route('admin.payments.verify', $p->id) }}" class="flex items-center justify-center gap-2">
                                    @csrf
                                    <button type="submit" name="status" value="Dibayar" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] uppercase shadow-sm">
                                        Setujui (Lunas)
                                    </button>
                                    <button type="submit" name="status" value="Ditolak" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[10px] uppercase shadow-sm">
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Belum ada transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
