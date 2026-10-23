@extends('layouts.admin')

@section('title', 'Kelola Reservasi Kursus — Owner RanggaMotret.id')

@section('content')
<div class="space-y-8"
     x-data="{
        showBuktiModal: false,
        buktiSrc: '',
        buktiTitle: '',
        openBukti(src, title) {
            this.buktiSrc = src;
            this.buktiTitle = title;
            this.showBuktiModal = true;
        }
     }">

    <!-- Page Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Reservasi Kursus</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 flex items-center gap-2.5">
                <span>Kelola Reservasi Kursus Akademi</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $totalReservations }} Pendaftar
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Pantau seluruh pendaftaran kelas pelatihan fotografi, rincian peserta, metode bayar, dan aktivasi akses materi belajar.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-graduation-cap text-base text-slate-600"></i>
                <span>Kelola Materi Kursus</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" 
               class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                <i class="ph-duotone ph-receipt text-base text-emerald-400"></i>
                <span>Verifikasi Semua Pembayaran</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ph-duotone ph-check-circle text-lg text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <i class="ph ph-x"></i>
            </button>
        </div>
    @endif

    <!-- Quick Stats Cards (Ringkasan Metrik) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Pendaftar Kursus -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Reservasi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-users-three"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalReservations) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Peserta mendaftar kursus</div>
        </div>

        <!-- 2. Terverifikasi / Lunas -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Akses Aktif (Lunas)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($totalLunas) }}</div>
            <div class="text-[11px] text-emerald-700 font-medium">Bisa langsung belajar LMS</div>
        </div>

        <!-- 3. Menunggu Verifikasi Manual -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Verifikasi</span>
                <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm border border-sky-100">
                    <i class="ph-duotone ph-clock animate-pulse"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-sky-600">{{ number_format($totalMenunggu) }}</div>
            <div class="text-[11px] text-sky-700 font-medium">Perlu dicek bukti transfernya</div>
        </div>

        <!-- 4. Total Omset Kursus -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan Akademi</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm border border-indigo-100">
                    <i class="ph-duotone ph-wallet"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($totalPendapatanKursus, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Pemasukan kursus lunas</div>
        </div>
    </div>

    <!-- Filter & Pencarian Toolbar -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.courses.reservations.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Pencarian Transaksi/Peserta -->
            <div class="sm:col-span-5 relative">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode transaksi, nama peserta, email..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all">
            </div>

            <!-- Filter Pilihan Kursus -->
            <div class="sm:col-span-3">
                <select name="kursus_id" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent bg-white font-medium">
                    <option value="">Semua Program Kursus</option>
                    @foreach($allCourses as $c)
                        <option value="{{ $c->id }}" {{ $kursusId == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Pembayaran / Reservasi -->
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent bg-white font-medium">
                    <option value="">Semua Status</option>
                    <option value="Dibayar" {{ $status === 'Dibayar' ? 'selected' : '' }}>Dibayar (Lunas)</option>
                    <option value="Menunggu Verifikasi" {{ $status === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Belum Dibayar" {{ $status === 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="Ditolak" {{ $status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Dibatalkan" {{ $status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                    Filter
                </button>
                @if($search || $status || $kursusId)
                    <a href="{{ route('admin.courses.reservations.index') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors" title="Reset Filter">
                        <i class="ph ph-arrow-counter-clockwise text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Reservasi Kursus -->
    <div class="clean-card rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">Kode Transaksi</th>
                        <th class="p-4">Peserta / Siswa</th>
                        <th class="p-4">Program Kursus</th>
                        <th class="p-4">Total Biaya & Metode</th>
                        <th class="p-4">Tanggal Daftar</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi / Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reservations as $r)
                        @php
                            $badgeClass = match($r->status) {
                                'Dibayar' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'Menunggu Verifikasi' => 'bg-sky-50 text-sky-800 border-sky-200',
                                'Belum Dibayar' => 'bg-amber-100 text-amber-900 border-amber-300 font-black',
                                'Ditolak', 'Dibatalkan' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                            $isGateway = str_contains($r->metode_pembayaran ?? '', 'Midtrans') || !empty($r->snap_token);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $r->status === 'Menunggu Verifikasi' ? 'bg-sky-50/30' : '' }}">
                            
                            <!-- 1. Kode Transaksi -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono font-black text-slate-900 text-xs">{{ $r->nomor_transaksi }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        KRS-{{ $r->kursus_id }}
                                    </span>
                                </div>
                                @if($r->catatan)
                                    <div class="text-[10px] text-slate-500 italic mt-0.5 max-w-xs truncate" title="{{ $r->catatan }}">
                                        Catatan: {{ $r->catatan }}
                                    </div>
                                @endif
                            </td>

                            <!-- 2. Peserta / Siswa -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <i class="ph-duotone ph-user text-slate-600"></i>
                                    <span>{{ $r->user->name ?? 'Pengguna Dihapus' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                    {{ $r->user->email ?? '-' }} • {{ $r->user->phone ?? 'Tanpa Nomor' }}
                                </div>
                            </td>

                            <!-- 3. Program Kursus -->
                            <td class="p-4">
                                <div class="font-black text-slate-900 text-xs line-clamp-1" title="{{ $r->kursus->nama ?? '-' }}">
                                    {{ $r->kursus->nama ?? 'Kursus Tidak Ditemukan' }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                                    <span>Tingkat: <strong>{{ ucfirst($r->kursus->level ?? 'Semua') }}</strong></span>
                                    <span>•</span>
                                    <span>Durasi: <strong>{{ $r->kursus->durasi ?? '-' }}</strong></span>
                                </div>
                            </td>

                            <!-- 4. Total Biaya & Metode Bayar -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-black text-slate-900 text-xs">
                                    Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-bold mt-0.5 flex items-center gap-1">
                                    @if($isGateway)
                                        <i class="ph-duotone ph-lightning text-emerald-600"></i>
                                        <span>Midtrans Snap (Gateway)</span>
                                    @else
                                        <i class="ph-duotone ph-bank text-indigo-600"></i>
                                        <span>{{ $r->metode_pembayaran ?: 'Transfer Bank' }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 5. Tanggal Transaksi -->
                            <td class="p-4 text-slate-500 whitespace-nowrap text-[11px] font-medium">
                                {{ date('d M Y, H:i', strtotime($r->tanggal_pembayaran ?? $r->created_at)) }} WIB
                            </td>

                            <!-- 6. Status Transaksi -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeClass }}">
                                    @if($r->status === 'Menunggu Verifikasi')
                                        <i class="ph-duotone ph-clock mr-0.5 animate-pulse"></i>
                                    @elseif($r->status === 'Dibayar')
                                        <i class="ph-duotone ph-check-circle mr-0.5 text-emerald-600"></i>
                                    @endif
                                    {{ $r->status }}
                                </span>
                            </td>

                            <!-- 7. Aksi / Ubah Status -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Bukti Transfer Modal Trigger -->
                                    @if($r->bukti_pembayaran)
                                        <button type="button" 
                                                @click="openBukti('{{ asset($r->bukti_pembayaran) }}', 'Bukti Transfer {{ $r->nomor_transaksi }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200 shadow-2xs inline-flex items-center gap-1 transition-all"
                                                title="Lihat Bukti Struk Transfer">
                                            <i class="ph-duotone ph-image text-xs text-indigo-600"></i>
                                            <span>Bukti Struk</span>
                                        </button>
                                    @endif

                                    <!-- Quick Change Status Form -->
                                    <form method="POST" action="{{ route('admin.courses.reservations.update-status', $r->id) }}" class="inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" 
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-[10px] text-slate-900 font-black focus:bg-white focus:outline-none cursor-pointer">
                                            <option value="Menunggu Verifikasi" {{ $r->status === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                            <option value="Dibayar" {{ $r->status === 'Dibayar' ? 'selected' : '' }}>Setujui (Lunas)</option>
                                            <option value="Belum Dibayar" {{ $r->status === 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                                            <option value="Ditolak" {{ $r->status === 'Ditolak' ? 'selected' : '' }}>Tolak Pembayaran</option>
                                            <option value="Dibatalkan" {{ $r->status === 'Dibatalkan' ? 'selected' : '' }}>Batalkan</option>
                                        </select>
                                    </form>

                                    <!-- Cetak Invoice jika ada -->
                                    @if($r->invoice)
                                        <a href="{{ route('invoices.show', $r->invoice->id) }}" target="_blank" 
                                           class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 transition-colors shadow-2xs"
                                           title="Cetak Invoice PDF">
                                            <i class="ph-duotone ph-receipt text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500 space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                    <i class="ph-duotone ph-graduation-cap"></i>
                                </div>
                                <div class="font-extrabold text-slate-800 text-sm">Belum Ada Data Reservasi Kursus</div>
                                <p class="text-xs text-slate-400 font-medium">Tidak ada transaksi pendaftaran kursus yang cocok dengan kriteria pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reservations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL PREVIEW BUKTI TRANSFER               -->
    <!-- ========================================== -->
    <div x-show="showBuktiModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-200 relative"
             @click.away="showBuktiModal = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="ph-duotone ph-receipt text-lg text-indigo-600"></i>
                    <h3 class="text-sm font-extrabold text-slate-900" x-text="buktiTitle"></h3>
                </div>
                <button @click="showBuktiModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 max-h-[70vh] flex items-center justify-center">
                <img :src="buktiSrc" class="max-w-full max-h-[70vh] object-contain">
            </div>

            <div class="flex justify-between items-center pt-2">
                <a :href="buktiSrc" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                    <i class="ph-duotone ph-arrows-out"></i>
                    <span>Buka Ukuran Penuh</span>
                </a>
                <button type="button" @click="showBuktiModal = false" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
