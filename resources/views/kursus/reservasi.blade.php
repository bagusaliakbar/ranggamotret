@extends('layouts.admin')

@section('title', 'Riwayat Reservasi Kursus — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Reservasi Kursus</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Histori pendaftaran pelatihan fotografi, rincian pembayaran, kode transaksi, dan status akses kelas akademi Anda.</p>
        </div>
        <a href="{{ route('public.courses') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md inline-flex items-center gap-2 self-start sm:self-auto hover:scale-[1.02] transition-transform">
            <i class="ph-duotone ph-plus text-sm"></i>
            <span>Daftar Kursus Baru</span>
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Pendaftaran -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Reservasi</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-receipt"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $totalReservasi ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Transaksi kelas kursus</div>
        </div>

        <!-- 2. Akses Aktif & Lunas -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Akses Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ $enrollments->count() }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Kelas aktif dapat dipelajari</div>
        </div>

        <!-- 3. Total Investasi -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Investasi</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-wallet"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($totalInvestasi ?? 0, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Edukasi fotografi</div>
        </div>

        <!-- 4. Pengampu Utama -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Pengampu</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-user-circle"></i>
                </div>
            </div>
            <div class="text-base font-black text-slate-900">Owner Studio</div>
            <div class="text-[11px] text-slate-500 font-medium">RanggaMotret.id</div>
        </div>
    </div>

    <!-- Course Reservations List -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ph-duotone ph-list-dashes text-slate-700"></i>
                <span>Histori Pendaftaran Kursus</span>
            </h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $enrollments->count() }} Kursus Terdaftar</span>
        </div>

        @if($enrollments->isNotEmpty() || $coursePayments->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                {{-- 1. Iterasi dari Enrollment (Kursus Aktif Terdaftar) --}}
                @foreach($enrollments as $e)
                    @php
                        $pembayaran = $e->pembayaran;
                        $invoice = $pembayaran?->invoice;
                        $isPaid = true;
                    @endphp
                    <div class="clean-card p-6 sm:p-7 rounded-3xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 border border-slate-200/80 hover:shadow-md transition-shadow bg-white">
                        <div class="space-y-3 flex-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-mono text-xs font-extrabold text-slate-900 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">
                                    {{ $e->nomor_reservasi ?? ($invoice ? $invoice->nomor_invoice : ($pembayaran ? $pembayaran->nomor_transaksi : ('RSV-KRS-' . str_pad($e->id, 4, '0', STR_PAD_LEFT)))) }}
                                </span>
                                <span class="text-xs font-bold text-slate-400">• Terdaftar: {{ $e->tanggal ? date('d M Y', strtotime($e->tanggal)) : $e->created_at->format('d M Y') }}</span>
                            </div>

                            <div>
                                <h3 class="text-lg font-black text-slate-900">{{ $e->kursus->nama }}</h3>
                                <p class="text-xs text-slate-600 font-bold mt-0.5">
                                    Level: <span class="text-slate-900">{{ $e->kursus->level }}</span> • Durasi: {{ $e->kursus->durasi }} • Investasi: Rp {{ number_format($e->kursus->harga, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-4 text-xs text-slate-600 font-medium pt-1">
                                <span class="flex items-center gap-1.5"><i class="ph-duotone ph-user text-slate-500"></i> Pengampu: <strong class="text-slate-800">{{ $e->kursus->instruktur->name ?? 'Owner RanggaMotret' }}</strong></span>
                                <span class="flex items-center gap-1.5"><i class="ph-duotone ph-credit-card text-slate-500"></i> Pembayaran: {{ $pembayaran ? $pembayaran->metode_pembayaran : 'Akses Penuh Resmi' }}</span>
                                <span class="flex items-center gap-1.5"><i class="ph-duotone ph-chart-line-up text-slate-500"></i> Progress: <strong class="text-slate-900">{{ $e->progress_persen }}%</strong></span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase border shadow-sm bg-emerald-50 text-emerald-800 border-emerald-200 flex items-center gap-1">
                                <i class="ph-bold ph-check-circle"></i> Terdaftar / Lunas
                            </span>

                            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                @if($invoice)
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold flex items-center gap-1.5 transition-colors">
                                        <i class="ph-duotone ph-file-text"></i>
                                        <span>Invoice</span>
                                    </a>
                                @endif

                                <a href="{{ route('student.learn', $e->kursus_id) }}" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 text-center shadow-sm">
                                    <i class="ph-duotone ph-play-circle text-sm"></i>
                                    <span>Buka Kelas</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- 2. Transaksi Kursus Lainnya (Misal status belum lunas / pending pembayaran) --}}
                @foreach($coursePayments as $cp)
                    @if(!$enrollments->contains('kursus_id', $cp->kursus_id))
                        @php
                            $cpBadgeClass = match($cp->status) {
                                'Dibayar' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'Menunggu Verifikasi' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'Ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-amber-50 text-amber-800 border-amber-200',
                            };
                        @endphp
                        <div class="clean-card p-6 sm:p-7 rounded-3xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 border border-slate-200/80 hover:shadow-md transition-shadow bg-white">
                            <div class="space-y-3 flex-1">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <span class="font-mono text-xs font-extrabold text-slate-900 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">
                                        {{ $cp->nomor_transaksi }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400">• Dipesan: {{ $cp->created_at->format('d M Y') }}</span>
                                </div>

                                <div>
                                    <h3 class="text-lg font-black text-slate-900">{{ $cp->kursus->nama ?? 'Kursus Fotografi' }}</h3>
                                    <p class="text-xs text-slate-600 font-bold mt-0.5">
                                        Investasi: Rp {{ number_format($cp->jumlah, 0, ',', '.') }} • Metode: {{ $cp->metode_pembayaran }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase border shadow-sm {{ $cpBadgeClass }}">
                                    {{ $cp->status }}
                                </span>

                                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                    @if($cp->status !== 'Dibayar' && $cp->kursus_id)
                                        <a href="{{ route('payments.create', ['kursus_id' => $cp->kursus_id]) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black flex items-center gap-1.5 shadow-sm transition-colors">
                                            <i class="ph-duotone ph-credit-card"></i>
                                            <span>Selesaikan Pembayaran</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="clean-card p-12 sm:p-16 text-center rounded-3xl space-y-4 border border-slate-200 bg-white">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    <i class="ph-duotone ph-receipt"></i>
                </div>
                <div class="space-y-1 max-w-sm mx-auto">
                    <h3 class="text-base font-black text-slate-900">Belum Ada Riwayat Reservasi Kursus</h3>
                    <p class="text-xs text-slate-500 font-medium">Anda belum pernah mendaftar atau membeli kelas pelatihan fotografi.</p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('public.courses') }}" class="btn-dark px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 shadow-md">
                        <i class="ph-duotone ph-plus text-sm"></i>
                        <span>Pilih Kursus Sekarang</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
