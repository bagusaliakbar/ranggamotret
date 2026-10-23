@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran — Admin RanggaMotret.id')

@section('content')
<div class="space-y-8" x-data="{ previewImage: null }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Verifikasi Pembayaran</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Verifikasi Pembayaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Kelola dan verifikasi transaksi transfer bank manual pelanggan serta pantau pembayaran otomatis dari Payment Gateway.
            </p>
        </div>

        @if($countManualPending > 0)
            <div class="px-4 py-2.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center gap-2.5 shadow-sm">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></div>
                <div class="text-xs font-extrabold">
                    <span>{{ $countManualPending }} Pembayaran Manual Menunggu Verifikasi</span>
                </div>
            </div>
        @endif
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
            <div class="text-2xl font-black text-slate-900">{{ $countTotal }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Semua transaksi masuk</div>
        </a>

        <!-- 2. Perlu Verifikasi Manual -->
        <a href="{{ route('admin.payments.index', ['status' => 'Menunggu Verifikasi']) }}" class="clean-card p-5 rounded-2xl space-y-1 block border-2 {{ $countManualPending > 0 ? 'border-amber-300 bg-amber-50/30' : '' }} hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider">Verifikasi Manual</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-hourglass-high"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ $countManualPending }}</div>
            <div class="text-[11px] text-amber-700 font-medium">Struk transfer perlu dicek</div>
        </a>

        <!-- 3. Gateway Otomatis -->
        <a href="{{ route('admin.payments.index', ['metode' => 'gateway']) }}" class="clean-card p-5 rounded-2xl space-y-1 block hover:border-slate-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Payment Gateway</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-lightning"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ $countGateway }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Verifikasi otomatis sistem</div>
        </a>

        <!-- 4. Total Lunas -->
        <a href="{{ route('admin.payments.index', ['status' => 'Dibayar']) }}" class="clean-card p-5 rounded-2xl space-y-1 block hover:border-slate-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Lunas Disetujui</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600">{{ $countLunas }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Pembayaran berhasil</div>
        </a>
    </div>

    <!-- Filter Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('admin.payments.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ empty($status) && empty($metode) ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }}">
            <i class="ph-duotone ph-list-bullets text-sm"></i>
            <span>Semua Transaksi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ empty($status) && empty($metode) ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">{{ $countTotal }}</span>
        </a>

        <a href="{{ route('admin.payments.index', ['status' => 'Menunggu Verifikasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $status === 'Menunggu Verifikasi' ? 'bg-amber-500 text-slate-950 shadow-sm font-black' : 'bg-white hover:bg-amber-50 text-slate-700 border border-slate-200' }}">
            <i class="ph-duotone ph-hourglass-medium text-sm"></i>
            <span>Perlu Verifikasi Manual</span>
            @if($countManualPending > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white animate-pulse">{{ $countManualPending }}</span>
            @endif
        </a>

        <a href="{{ route('admin.payments.index', ['metode' => 'gateway']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $metode === 'gateway' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }}">
            <i class="ph-duotone ph-lightning text-sm text-emerald-400"></i>
            <span>Otomatis Gateway</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $metode === 'gateway' ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">{{ $countGateway }}</span>
        </a>

        <a href="{{ route('admin.payments.index', ['metode' => 'manual']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $metode === 'manual' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }}">
            <i class="ph-duotone ph-bank text-sm text-indigo-400"></i>
            <span>Transfer Bank Manual</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $metode === 'manual' ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">{{ $countManual }}</span>
        </a>

        <a href="{{ route('admin.payments.index', ['status' => 'Dibayar']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 {{ $status === 'Dibayar' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }}">
            <i class="ph-duotone ph-check-circle text-sm text-emerald-400"></i>
            <span>Lunas</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Dibayar' ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">{{ $countLunas }}</span>
        </a>
    </div>

    <!-- Payment Verification Table -->
    <div class="clean-card rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">Kode Transaksi & Tanggal</th>
                        <th class="p-4">Customer & Layanan</th>
                        <th class="p-4">Jumlah Nominal</th>
                        <th class="p-4">Metode Pembayaran</th>
                        <th class="p-4 text-center">Bukti Upload</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Tindakan Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembayarans as $p)
                        @php
                            $isGateway = str_contains($p->metode_pembayaran ?? '', 'Midtrans') || !empty($p->snap_token);
                            $isManual = !$isGateway || str_contains($p->metode_pembayaran ?? '', 'Transfer');
                            $hasProof = !empty($p->bukti_pembayaran);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $p->status === 'Menunggu Verifikasi' ? 'bg-amber-50/30' : '' }}">
                            
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

                            <!-- 4. METODE PEMBAYARAN (PEMBEDA UTAMA: OTOMATIS GATEWAY VS TRANSFER MANUAL) -->
                            <td class="p-4 whitespace-nowrap">
                                @if($isGateway)
                                    <!-- PAYMENT GATEWAY OTOMATIS -->
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-black shadow-xs">
                                            <i class="ph-duotone ph-lightning text-emerald-600 text-sm"></i>
                                            <span>Otomatis (Payment Gateway)</span>
                                        </span>
                                        <div class="text-[10px] text-slate-500 font-medium">
                                            Midtrans Snap • QRIS / VA Bank
                                        </div>
                                    </div>
                                @else
                                    <!-- TRANSFER BANK MANUAL -->
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800 text-[11px] font-black shadow-xs">
                                            <i class="ph-duotone ph-bank text-indigo-600 text-sm"></i>
                                            <span>Transfer Bank Manual</span>
                                        </span>
                                        <div class="text-[10px] text-slate-600 font-bold">
                                            {{ $p->metode_pembayaran ?: 'Transfer Bank' }}
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Bukti Upload Struk -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($hasProof)
                                    <button type="button" @click="previewImage = '{{ asset($p->bukti_pembayaran) }}'" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-extrabold text-[11px] transition-all shadow-xs">
                                        <i class="ph-duotone ph-image text-slate-700 text-sm"></i>
                                        <span>Lihat Struk</span>
                                    </button>
                                @else
                                    @if($isGateway)
                                        <span class="text-[10px] font-bold text-slate-400 inline-flex items-center gap-1">
                                            <i class="ph-duotone ph-shield-check text-emerald-500 text-xs"></i>
                                            <span>Auto-verifikasi</span>
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium">Tidak ada berkas</span>
                                    @endif
                                @endif
                            </td>

                            <!-- 6. Status Transaksi -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @php
                                    $pStatusClass = match($p->status) {
                                        'Dibayar' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'Ditolak', 'Dikembalikan' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        'Menunggu Verifikasi' => 'bg-amber-100 text-amber-900 border-amber-300 ring-2 ring-amber-400/20',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $pStatusClass }}">
                                    {{ $p->status }}
                                </span>
                            </td>

                            <!-- 7. Tindakan Verifikasi Owner -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($p->status === 'Menunggu Verifikasi')
                                    <!-- Formulir Persetujuan Owner -->
                                    <form method="POST" action="{{ route('admin.payments.verify', $p->id) }}" class="inline-flex items-center justify-center gap-1.5">
                                        @csrf
                                        <button type="submit" name="status" value="Dibayar" 
                                                onclick="return confirm('Apakah Anda yakin dana pembayaran telah masuk ke rekening studio dan menyetujui transaksi ini?')"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] uppercase tracking-wider inline-flex items-center gap-1 shadow-sm transition-all hover:scale-105 active:scale-95">
                                            <i class="ph-bold ph-check text-xs"></i>
                                            <span>Setujui</span>
                                        </button>
                                        <button type="submit" name="status" value="Ditolak" 
                                                onclick="return confirm('Tolak bukti pembayaran ini?')"
                                                class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-rose-50 text-rose-600 border border-rose-300 font-bold text-[10px] uppercase tracking-wider inline-flex items-center gap-1 shadow-xs transition-all hover:border-rose-500">
                                            <i class="ph-bold ph-x text-xs"></i>
                                            <span>Tolak</span>
                                        </button>
                                    </form>
                                @elseif($p->status === 'Dibayar')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                        <i class="ph-duotone ph-check-circle text-base"></i>
                                        <span>Selesai (Lunas)</span>
                                    </span>
                                @elseif($isGateway && $p->status === 'Belum Dibayar')
                                    <a href="{{ route('payments.sync', $p->id) }}" 
                                       class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] inline-flex items-center gap-1 border border-slate-200 shadow-xs" 
                                       title="Periksa status langsung ke server Midtrans">
                                        <i class="ph-duotone ph-arrows-clockwise text-xs"></i>
                                        <span>Cek Status</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500 space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                    <i class="ph-duotone ph-receipt"></i>
                                </div>
                                <div class="font-extrabold text-slate-800 text-sm">Tidak ada transaksi yang cocok</div>
                                <p class="text-xs text-slate-400 font-medium">Belum ada riwayat pembayaran pada filter yang Anda pilih saat ini.</p>
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

    <!-- Modal Preview Gambar Bukti Struk -->
    <div x-show="previewImage" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4"
         @keydown.escape.window="previewImage = null"
         style="display: none;">
        
        <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4" @click.outside="previewImage = null">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="ph-duotone ph-file-text text-xl text-slate-700"></i>
                    <h3 class="font-black text-sm text-slate-900">Bukti Transfer Pelanggan</h3>
                </div>
                <button type="button" @click="previewImage = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-sm font-bold">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <div class="max-h-[70vh] overflow-y-auto rounded-2xl bg-slate-50 border border-slate-200 p-2 flex items-center justify-center">
                <img :src="previewImage" alt="Bukti Transfer" class="max-w-full max-h-[65vh] object-contain rounded-xl shadow-sm">
            </div>

            <div class="flex items-center justify-between pt-2">
                <a :href="previewImage" target="_blank" download class="btn-dark px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ph-duotone ph-download-simple"></i>
                    <span>Unduh Berkas Asli</span>
                </a>
                <button type="button" @click="previewImage = null" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
