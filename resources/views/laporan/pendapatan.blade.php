@extends('layouts.admin')

@section('title', 'Laporan Pendapatan — Owner RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header Section & Print Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span>Laporan</span>
                <span>/</span>
                <span class="text-slate-700">Laporan Pendapatan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Laporan Pendapatan & Omzet Keuangan</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Rekapitulasi pemasukan transaksi lunas dari pemesanan sesi fotografi dan akademi kursus online.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-printer text-base text-slate-600"></i>
                <span>Cetak Laporan</span>
            </button>
            <a href="{{ route('admin.reports.reservasi') }}" 
               class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                <i class="ph-duotone ph-calendar-check text-base text-indigo-400"></i>
                <span>Lihat Laporan Reservasi</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (Ringkasan Keuangan Periode Ini) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Pendapatan Lunas -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                    <i class="ph-duotone ph-wallet"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-600 font-medium">{{ $transaksiCount }} transaksi lunas</div>
        </div>

        <!-- 2. Pendapatan Fotografi -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Sesi Fotografi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-camera"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($pendapatanFoto, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Layanan & paket studio</div>
        </div>

        <!-- 3. Pendapatan Kursus -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Akademi Kursus</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm border border-indigo-100">
                    <i class="ph-duotone ph-graduation-cap"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-indigo-600">Rp {{ number_format($pendapatanKursus, 0, ',', '.') }}</div>
            <div class="text-[11px] text-indigo-700 font-medium">Pelatihan & LMS online</div>
        </div>

        <!-- 4. Rata-rata Nilai Transaksi -->
        @php
            $avgOrder = $transaksiCount > 0 ? round($totalPendapatan / $transaksiCount) : 0;
        @endphp
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-rata Transaksi</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-100">
                    <i class="ph-duotone ph-chart-line-up"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($avgOrder, 0, ',', '.') }}</div>
            <div class="text-[11px] text-blue-600 font-medium">Nilai per transaksi</div>
        </div>
    </div>

    <!-- Filter Toolbar Berdasarkan Tanggal & Kategori -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.pendapatan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <!-- Tanggal Dari -->
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
            </div>

            <!-- Tanggal Sampai -->
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
            </div>

            <!-- Kategori Pendapatan -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Kategori</label>
                <select name="kategori" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                    <option value="">Semua Pemasukan</option>
                    <option value="foto" {{ $kategori === 'foto' ? 'selected' : '' }}>Sesi Fotografi</option>
                    <option value="kursus" {{ $kategori === 'kursus' ? 'selected' : '' }}>Akademi Kursus</option>
                </select>
            </div>

            <!-- Metode Pembayaran -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Metode Bayar</label>
                <select name="metode" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                    <option value="">Semua Metode</option>
                    <option value="gateway" {{ $metode === 'gateway' ? 'selected' : '' }}>Midtrans Gateway</option>
                    <option value="manual" {{ $metode === 'manual' ? 'selected' : '' }}>Transfer Bank Manual</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="sm:col-span-2 flex items-end gap-2 pt-4 sm:pt-0">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-all shadow-xs mt-3">
                    Filter Laporan
                </button>
                @if(request()->has('start_date') || request()->has('kategori') || request()->has('metode'))
                    <a href="{{ route('admin.reports.pendapatan') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors mt-3" title="Reset Filter">
                        <i class="ph ph-arrow-counter-clockwise text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Rincian Transaksi Pendapatan -->
    <div class="clean-card rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="font-extrabold text-sm text-slate-900">Rincian Transaksi Pendapatan Masuk</h3>
                <p class="text-xs text-slate-400 font-medium">Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ $pembayarans->total() }} Data Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">No. Transaksi / Invoice</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Layanan / Kursus</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">Tanggal Pembayaran</th>
                        <th class="p-4 text-right">Nominal Masuk</th>
                        <th class="p-4 text-center">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembayarans as $p)
                        @php
                            $isCourse = !empty($p->kursus_id);
                            $isGateway = str_contains($p->metode_pembayaran ?? '', 'Midtrans') || !empty($p->snap_token);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- 1. Kode Transaksi -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
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
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $p->invoice ? $p->invoice->nomor_invoice : '-' }}
                                </div>
                            </td>

                            <!-- 2. Pelanggan -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <i class="ph-duotone ph-user text-slate-500"></i>
                                    <span>{{ $p->user->name ?? 'Pelanggan' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    {{ $p->user->phone ?? ($p->user->email ?? '-') }}
                                </div>
                            </td>

                            <!-- 3. Layanan / Kursus -->
                            <td class="p-4">
                                @if($p->booking)
                                    <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                        <i class="ph-duotone ph-camera text-slate-600"></i>
                                        <span>{{ $p->booking->layanan->nama ?? 'Sesi Pemotretan' }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                        Paket: {{ $p->booking->paket->nama_paket ?? '-' }}
                                    </div>
                                @elseif($p->kursus)
                                    <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                        <i class="ph-duotone ph-graduation-cap text-slate-600"></i>
                                        <span>{{ $p->kursus->nama }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                        Tingkat: {{ ucfirst($p->kursus->level ?? 'Semua') }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- 4. Metode Bayar -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    @if($isGateway)
                                        <i class="ph-duotone ph-lightning text-emerald-600"></i>
                                        <span>Midtrans Snap</span>
                                    @else
                                        <i class="ph-duotone ph-bank text-indigo-600"></i>
                                        <span>{{ $p->metode_pembayaran ?: 'Transfer Bank' }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 5. Tanggal Pembayaran -->
                            <td class="p-4 text-slate-500 whitespace-nowrap text-[11px] font-medium">
                                {{ date('d M Y, H:i', strtotime($p->tanggal_pembayaran ?? $p->created_at)) }} WIB
                            </td>

                            <!-- 6. Nominal Masuk -->
                            <td class="p-4 text-right whitespace-nowrap">
                                <span class="font-black text-slate-900 text-xs">
                                    Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- 7. Invoice -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($p->invoice)
                                    <a href="{{ route('invoices.show', $p->invoice->id) }}" target="_blank"
                                       class="px-2.5 py-1 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-[10px] inline-flex items-center gap-1 transition-all shadow-2xs hover:scale-105 active:scale-95"
                                       title="Lihat / Cetak Invoice Resmi">
                                        <i class="ph-duotone ph-receipt text-emerald-400"></i>
                                        <span>PDF</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500 space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                    <i class="ph-duotone ph-wallet"></i>
                                </div>
                                <div class="font-extrabold text-slate-800 text-sm">Tidak Ada Transaksi Pendapatan</div>
                                <p class="text-xs text-slate-400 font-medium">Tidak ada transaksi lunas pada rentang tanggal yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($pembayarans->isNotEmpty())
                    <tfoot class="bg-slate-50 font-black text-slate-900 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="5" class="p-4 text-right uppercase tracking-wider text-xs">Total Pendapatan (Halaman Ini):</td>
                            <td class="p-4 text-right text-xs">Rp {{ number_format($pembayarans->sum('jumlah'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
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
