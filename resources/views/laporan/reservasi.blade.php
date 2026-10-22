@extends('layouts.admin')

@section('title', 'Laporan Reservasi Fotografi & Kursus — Owner RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header Section & Action Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span>Laporan</span>
                <span>/</span>
                <span class="text-slate-700">Laporan Reservasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Laporan Reservasi Fotografi & Kursus</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Rekapitulasi volume pemesanan sesi foto dan pendaftaran pelatihan akademi fotografi.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-printer text-base text-slate-600"></i>
                <span>Cetak Laporan</span>
            </button>
            <a href="{{ route('admin.reports.pendapatan') }}" 
               class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                <i class="ph-duotone ph-wallet text-base text-emerald-400"></i>
                <span>Lihat Laporan Pendapatan</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (Ringkasan Reservasi) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Seluruh Reservasi -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Reservasi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-receipt"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalReservasiCount) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Gabungan foto & kursus</div>
        </div>

        <!-- 2. Reservasi Fotografi -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Sesi Fotografi</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                    <i class="ph-duotone ph-camera"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($totalFotoCount) }}</div>
            <div class="text-[11px] text-emerald-700 font-medium">{{ $totalSelesaiFoto }} sesi terkonfirmasi/selesai</div>
        </div>

        <!-- 3. Pendaftaran Kursus -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Pendaftar Kursus</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm border border-indigo-100">
                    <i class="ph-duotone ph-graduation-cap"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-indigo-600">{{ number_format($totalKursusCount) }}</div>
            <div class="text-[11px] text-indigo-700 font-medium">{{ $totalLunasKursus }} akses aktif belajar</div>
        </div>

        <!-- 4. Estimasi Nilai Perputaran -->
        @php
            $totalNilai = $allReservations->sum('biaya');
        @endphp
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Nilai Total Reservasi</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-100">
                    <i class="ph-duotone ph-coins"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
            <div class="text-[11px] text-blue-600 font-medium">Periode tanggal terpilih</div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.reservasi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <!-- Tanggal Mulai -->
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
            </div>

            <!-- Tanggal Akhir -->
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
            </div>

            <!-- Jenis Reservasi -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Tipe Layanan</label>
                <select name="tipe" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                    <option value="semua" {{ $tipe === 'semua' ? 'selected' : '' }}>Semua Layanan</option>
                    <option value="foto" {{ $tipe === 'foto' ? 'selected' : '' }}>Sesi Fotografi</option>
                    <option value="kursus" {{ $tipe === 'kursus' ? 'selected' : '' }}>Akademi Kursus</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium bg-white">
                    <option value="">Semua Status</option>
                    <option value="Dibayar" {{ $status === 'Dibayar' ? 'selected' : '' }}>Dibayar (Lunas)</option>
                    <option value="Menunggu Konfirmasi" {{ $status === 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="Menunggu Verifikasi" {{ $status === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Terjadwal" {{ $status === 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Dibatalkan" {{ $status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="sm:col-span-2 flex items-end gap-2 pt-4 sm:pt-0">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-all shadow-xs mt-3">
                    Filter Laporan
                </button>
                @if(request()->has('start_date') || request()->has('tipe') || request()->has('status'))
                    <a href="{{ route('admin.reports.reservasi') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors mt-3" title="Reset Filter">
                        <i class="ph ph-arrow-counter-clockwise text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Rekapitulasi Reservasi -->
    <div class="clean-card rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="font-extrabold text-sm text-slate-900">Rekapitulasi Reservasi Masuk</h3>
                <p class="text-xs text-slate-400 font-medium">Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ $allReservations->count() }} Data Pemesanan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">Kode Reservasi</th>
                        <th class="p-4">Tipe & Program</th>
                        <th class="p-4">Pelanggan / Siswa</th>
                        <th class="p-4">Jadwal Pelaksanaan</th>
                        <th class="p-4 text-right">Biaya Tagihan</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($allReservations as $r)
                        @php
                            $badgeClass = match($r->status) {
                                'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'Selesai' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'Menunggu Verifikasi', 'Menunggu Konfirmasi' => 'bg-sky-50 text-sky-800 border-sky-200',
                                'Belum Dibayar' => 'bg-amber-100 text-amber-900 border-amber-300 font-black',
                                'Ditolak', 'Dibatalkan' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- 1. Kode -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="font-mono font-black text-slate-900 text-xs">{{ $r->kode }}</span>
                                <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    Daftar: {{ date('d M Y', strtotime($r->tanggal_daftar)) }}
                                </div>
                            </td>

                            <!-- 2. Tipe & Program -->
                            <td class="p-4">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    @if($r->tipe === 'Fotografi')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Foto
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Kursus
                                        </span>
                                    @endif
                                </div>
                                <div class="font-bold text-slate-900 text-xs line-clamp-1" title="{{ $r->item }}">
                                    {{ $r->item }}
                                </div>
                            </td>

                            <!-- 3. Pelanggan -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <i class="ph-duotone ph-user text-slate-500"></i>
                                    <span>{{ $r->nama_pelanggan }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    {{ $r->kontak }}
                                </div>
                            </td>

                            <!-- 4. Jadwal Pelaksanaan -->
                            <td class="p-4 text-slate-600 whitespace-nowrap text-[11px] font-medium">
                                <div class="flex items-center gap-1">
                                    <i class="ph-duotone ph-calendar text-xs text-slate-400"></i>
                                    <span>{{ $r->jadwal_event }}</span>
                                </div>
                            </td>

                            <!-- 5. Biaya Tagihan -->
                            <td class="p-4 text-right whitespace-nowrap">
                                <span class="font-black text-slate-900 text-xs">
                                    Rp {{ number_format($r->biaya, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- 6. Status -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeClass }}">
                                    {{ $r->status }}
                                </span>
                            </td>

                            <!-- 7. Aksi -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <a href="{{ $r->detail_url }}" 
                                   class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 font-bold text-[10px] inline-flex items-center gap-1 transition-all shadow-2xs hover:scale-105 active:scale-95"
                                   title="Buka Data Rinci">
                                    <i class="ph-duotone ph-arrow-square-out text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500 space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                    <i class="ph-duotone ph-calendar-blank"></i>
                                </div>
                                <div class="font-extrabold text-slate-800 text-sm">Tidak Ada Data Reservasi</div>
                                <p class="text-xs text-slate-400 font-medium">Tidak ada reservasi fotografi maupun kursus pada filter periode yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
