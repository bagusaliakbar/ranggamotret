@extends('layouts.admin')

@section('title', 'Kelola Pembayaran — Owner RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Pembayaran</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kelola Transaksi Pembayaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Pantau seluruh transaksi pembayaran online otomatis melalui Midtrans Payment Gateway untuk sesi foto dan kursus akademi.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-shield-check text-base text-emerald-600"></i>
                <span>Midtrans Gateway Aktif</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Transaksi -->
        <a href="{{ route('admin.payments.index') }}" class="clean-card p-5 rounded-2xl space-y-1 block hover:border-slate-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-receipt"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($countTotal) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Semua transaksi masuk</div>
        </a>

        <!-- 2. Lunas Terbayar -->
        <a href="{{ route('admin.payments.index', ['status' => 'Dibayar']) }}" class="clean-card p-5 rounded-2xl space-y-1 block hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-emerald-700 uppercase tracking-wider">Lunas Terbayar</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($countLunas) }}</div>
            <div class="text-[11px] text-emerald-700 font-medium">Pembayaran sukses</div>
        </a>

        <!-- 3. Menunggu Pembayaran -->
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="clean-card p-5 rounded-2xl space-y-1 block hover:border-amber-400 transition-all {{ $countPending > 0 ? 'bg-amber-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider">Menunggu Bayar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-hourglass-high"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($countPending) }}</div>
            <div class="text-[11px] text-amber-700 font-medium">Menunggu penyelesaian klien</div>
        </a>

        <!-- 4. Gagal / Dibatalkan -->
        <a href="{{ route('admin.payments.index', ['status' => 'failed']) }}" class="clean-card p-5 rounded-2xl space-y-1 block hover:border-rose-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-rose-700 uppercase tracking-wider">Batal / Kadaluarsa</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-x-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($countFailed) }}</div>
            <div class="text-[11px] text-rose-700 font-medium">Transaksi tidak terselesaikan</div>
        </a>
    </div>

    <!-- Search & Filter Navigation Tabs -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-4">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col sm:flex-row gap-3 items-center">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1 w-full">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kode transaksi, nama pelanggan, email, atau nomor booking..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition-all shadow-2xs">
                Cari Transaksi
            </button>
            @if(!empty($search))
                <a href="{{ route('admin.payments.index', array_filter(['status' => $status])) }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all flex items-center justify-center" title="Reset Pencarian">
                    <i class="ph-duotone ph-arrows-counter-clockwise text-base"></i>
                </a>
            @endif
        </form>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
            <a href="{{ route('admin.payments.index', array_filter(['search' => $search])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ empty($status) ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }}">
                <i class="ph-duotone ph-list-bullets text-sm"></i>
                <span>Semua Transaksi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ empty($status) ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">{{ $countTotal }}</span>
            </a>

            <a href="{{ route('admin.payments.index', array_filter(['status' => 'Dibayar', 'search' => $search])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $status === 'Dibayar' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white hover:bg-emerald-50 text-slate-700 border border-slate-200' }}">
                <i class="ph-duotone ph-check-circle text-sm"></i>
                <span>Lunas (Dibayar)</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Dibayar' ? 'bg-emerald-700 text-emerald-100' : 'bg-slate-100 text-slate-600' }}">{{ $countLunas }}</span>
            </a>

            <a href="{{ route('admin.payments.index', array_filter(['status' => 'pending', 'search' => $search])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $status === 'pending' ? 'bg-amber-500 text-slate-950 font-black shadow-sm' : 'bg-white hover:bg-amber-50 text-slate-700 border border-slate-200' }}">
                <i class="ph-duotone ph-hourglass-medium text-sm"></i>
                <span>Menunggu Pembayaran</span>
                @if($countPending > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $status === 'pending' ? 'bg-slate-900 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $countPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.payments.index', array_filter(['status' => 'failed', 'search' => $search])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $status === 'failed' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }}">
                <i class="ph-duotone ph-x-circle text-sm text-rose-400"></i>
                <span>Batal / Kadaluarsa</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'failed' ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">{{ $countFailed }}</span>
            </a>
        </div>
    </div>

    <!-- Payment Verification Table -->
    <div class="clean-card rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">Kode Transaksi & Tanggal</th>
                        <th class="p-4">Customer & Layanan/Program</th>
                        <th class="p-4">Jumlah Tagihan</th>
                        <th class="p-4">Metode Gateway</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembayarans as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ in_array($p->status, ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Belum Dibayar']) ? 'bg-amber-50/20' : '' }}">
                            
                            <!-- 1. Kode Transaksi & Waktu -->
                            <td class="p-4">
                                <div class="font-mono font-black text-slate-900 text-xs">{{ $p->nomor_transaksi }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                                    <i class="ph-duotone ph-clock text-slate-400"></i>
                                    <span>{{ date('d M Y, H:i', strtotime($p->tanggal_pembayaran ?? $p->created_at)) }}</span>
                                </div>
                            </td>

                            <!-- 2. Customer & Item Layanan -->
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900">{{ $p->user->name ?? 'Pelanggan' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate max-w-xs flex items-center gap-1">
                                    @if($p->booking)
                                        <i class="ph-duotone ph-camera text-slate-500"></i>
                                        <span>{{ $p->booking->layanan->nama ?? 'Sesi Foto' }} ({{ $p->booking->nomor_booking }})</span>
                                    @elseif($p->kursus)
                                        <i class="ph-duotone ph-graduation-cap text-slate-500"></i>
                                        <span>{{ $p->kursus->nama }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 3. Jumlah Nominal -->
                            <td class="p-4 font-black text-slate-900 text-sm whitespace-nowrap">
                                Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </td>

                            <!-- 4. METODE PEMBAYARAN MIDTRANS GATEWAY -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-black shadow-2xs">
                                        <i class="ph-duotone ph-lightning text-emerald-600 text-sm"></i>
                                        <span>Midtrans Gateway</span>
                                    </span>
                                    <div class="text-[10px] text-slate-500 font-medium">
                                        QRIS • Virtual Account • E-Wallet • CC
                                    </div>
                                </div>
                            </td>

                            <!-- 5. Status Transaksi -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @php
                                    $pStatusClass = match($p->status) {
                                        'Dibayar' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'Ditolak', 'Dikembalikan', 'Dibatalkan', 'Kadaluarsa' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        'Menunggu Verifikasi', 'Menunggu Pembayaran', 'Belum Dibayar' => 'bg-amber-50 text-amber-900 border-amber-300',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $pStatusClass }}">
                                    {{ $p->status }}
                                </span>
                            </td>

                            <!-- 6. Tindakan Owner -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    @if($p->status === 'Dibayar')
                                        <a href="{{ route('invoices.show', $p->invoice ? $p->invoice->id : $p->id) }}" target="_blank"
                                           class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-[10px] inline-flex items-center gap-1.5 shadow-2xs transition-all hover:scale-105 active:scale-95"
                                           title="Cetak Invoice Resmi Lunas">
                                            <i class="ph-duotone ph-receipt text-xs text-emerald-400"></i>
                                            <span>Invoice PDF</span>
                                        </a>
                                    @else
                                        <!-- Tombol Sinkronisasi Status dari Midtrans -->
                                        <a href="{{ route('payments.sync', $p->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] inline-flex items-center gap-1 border border-slate-200 shadow-2xs transition-all" 
                                           title="Periksa status transaksi langsung ke server Midtrans">
                                            <i class="ph-duotone ph-arrows-clockwise text-xs text-indigo-600"></i>
                                            <span>Cek Midtrans</span>
                                        </a>

                                        <!-- Quick Approve / Reject Form jika Owner ingin konfirmasi manual -->
                                        <form method="POST" action="{{ route('admin.payments.verify', $p->id) }}" class="inline-flex items-center gap-1">
                                            @csrf
                                            <button type="submit" name="status" value="Dibayar" 
                                                    onclick="return confirm('Apakah Anda yakin menandai transaksi ini sebagai LUNAS?')"
                                                    class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold"
                                                    title="Tandai Lunas">
                                                <i class="ph-bold ph-check"></i>
                                            </button>
                                            <button type="submit" name="status" value="Dibatalkan" 
                                                    onclick="return confirm('Batalkan transaksi ini?')"
                                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold"
                                                    title="Batalkan Transaksi">
                                                <i class="ph-bold ph-x"></i>
                                            </button>
                                        </form>
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
                                <div class="font-extrabold text-slate-800 text-sm">Tidak ada transaksi yang cocok</div>
                                <p class="text-xs text-slate-400 font-medium">Belum ada riwayat pembayaran online pada filter atau pencarian Anda saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
