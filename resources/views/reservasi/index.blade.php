@extends('layouts.admin')

@section('title', 'Layanan Fotografi — RanggaMotret.id')

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: 'all', 
    searchQuery: '',
    filterStatus(bookingStatus, paymentStatus, hasGallery) {
        if (this.activeTab === 'all') return true;
        if (this.activeTab === 'unpaid') {
            return paymentStatus !== 'Dibayar' && !['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai', 'Dibatalkan', 'Ditolak'].includes(bookingStatus);
        }
        if (this.activeTab === 'scheduled') {
            return ['Dibayar', 'Disetujui', 'Terjadwal'].includes(bookingStatus);
        }
        if (this.activeTab === 'completed') {
            return bookingStatus === 'Selesai';
        }
        if (this.activeTab === 'proofing') {
            return hasGallery > 0;
        }
        return true;
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-extrabold">Layanan Fotografi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Layanan Fotografi Saya</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Kelola seluruh sesi pemotretan Anda, kepastian jadwal studio, bukti pelunasan invoice, dan kurasi galeri hasil foto.</p>
        </div>
    </div>

    @php
        $totalCount = $bookings->count();
        $unpaidList = $bookings->filter(fn($b) => (!$b->pembayaran || $b->pembayaran->status !== 'Dibayar') && !in_array($b->status, ['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai', 'Dibatalkan', 'Ditolak']));
        $activeList = $bookings->filter(fn($b) => in_array($b->status, ['Dibayar', 'Disetujui', 'Terjadwal']));
        $completedList = $bookings->filter(fn($b) => $b->status === 'Selesai');
        $proofingList = $bookings->filter(fn($b) => $b->galeriFotos->count() > 0);
        $totalNominal = $bookings->sum(fn($b) => $b->paket?->harga ?? 0);
    @endphp

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Reservasi -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Reservasi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-calendar-blank"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalCount }}</div>
                <span class="text-[10px] font-bold text-slate-400">Sesi Foto</span>
            </div>
            <div class="text-[11px] text-slate-500 font-medium">Histori pemesanan studio</div>
        </div>

        <!-- 2. Terjadwal & Aktif -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Jadwal Sesi Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-calendar-check"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-black text-emerald-600 tracking-tight">{{ $activeList->count() }}</div>
                <span class="text-[10px] font-bold text-emerald-600">Terkonfirmasi</span>
            </div>
            <div class="text-[11px] text-slate-500 font-medium">Menunggu hari pelaksanaan</div>
        </div>

        <!-- 3. Menunggu Pembayaran -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Menunggu Bayar</span>
                <div class="w-8 h-8 rounded-xl {{ $unpaidList->count() > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-credit-card"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-black {{ $unpaidList->count() > 0 ? 'text-amber-600' : 'text-slate-800' }} tracking-tight">{{ $unpaidList->count() }}</div>
                <span class="text-[10px] font-bold text-slate-400">Tagihan</span>
            </div>
            <div class="text-[11px] text-slate-500 font-medium">Perlu segera diselesaikan</div>
        </div>

        <!-- 4. Galeri Proofing Siap -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Proofing & Hasil</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-images"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-black text-purple-600 tracking-tight">{{ $proofingList->count() }}</div>
                <span class="text-[10px] font-bold text-purple-600">Sesi Siap</span>
            </div>
            <div class="text-[11px] text-slate-500 font-medium">Dapat dipilih dan diunduh</div>
        </div>
    </div>

    <!-- Alert Pembayaran Pending jika ada -->
    @if($unpaidList->isNotEmpty())
        <div class="p-4 sm:p-5 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm animate-pulse-subtle">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl shrink-0 shadow-xs">
                    <i class="ph-duotone ph-warning-circle"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">Tagihan Reservasi Membutuhkan Pelunasan</h3>
                    <p class="text-xs text-amber-900 font-medium mt-0.5 leading-relaxed">
                        Anda memiliki <strong>{{ $unpaidList->count() }} sesi pemotretan</strong> yang belum dibayar. Selesaikan pembayaran agar fotografer kami menjadwalkan persiapan studio untuk Anda.
                    </p>
                </div>
            </div>
            @if($unpaidList->count() === 1)
                <a href="{{ route('payments.create', ['booking_id' => $unpaidList->first()->id]) }}" 
                   class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-black inline-flex items-center gap-2 shadow-sm shrink-0 whitespace-nowrap hover:scale-105 active:scale-95 transition-all">
                    <i class="ph-duotone ph-credit-card text-emerald-400"></i>
                    <span>Bayar Sekarang (Rp {{ number_format($unpaidList->first()->paket->harga ?? 0, 0, ',', '.') }})</span>
                </a>
            @else
                <button @click="activeTab = 'unpaid'" 
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-black inline-flex items-center gap-2 shadow-sm shrink-0 whitespace-nowrap hover:scale-105 active:scale-95 transition-all">
                    <i class="ph-duotone ph-funnel"></i>
                    <span>Tampilkan Tagihan ({{ $unpaidList->count() }})</span>
                </button>
            @endif
        </div>
    @endif

    <!-- Control Bar: Tabs, Search & Action Button -->
    <div class="clean-card p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <!-- Interactive Category Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto p-1 bg-slate-100 rounded-2xl no-scrollbar text-xs">
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-list-dashes"></i>
                <span>Semua Sesi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-200/70 text-slate-700 ml-1">{{ $totalCount }}</span>
            </button>

            <button @click="activeTab = 'scheduled'" 
                    :class="activeTab === 'scheduled' ? 'bg-white text-emerald-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-calendar-check"></i>
                <span>Jadwal Aktif</span>
                @if($activeList->count() > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 ml-1">{{ $activeList->count() }}</span>
                @endif
            </button>

            <button @click="activeTab = 'unpaid'" 
                    :class="activeTab === 'unpaid' ? 'bg-white text-amber-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-credit-card"></i>
                <span>Menunggu Bayar</span>
                @if($unpaidList->count() > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 ml-1">{{ $unpaidList->count() }}</span>
                @endif
            </button>

            <button @click="activeTab = 'completed'" 
                    :class="activeTab === 'completed' ? 'bg-white text-blue-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-check-circle"></i>
                <span>Selesai</span>
                @if($completedList->count() > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 ml-1">{{ $completedList->count() }}</span>
                @endif
            </button>

            <button @click="activeTab = 'proofing'" 
                    :class="activeTab === 'proofing' ? 'bg-white text-purple-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-images"></i>
                <span>Ada Proofing Foto</span>
                @if($proofingList->count() > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-purple-100 text-purple-800 ml-1">{{ $proofingList->count() }}</span>
                @endif
            </button>
        </div>

        <!-- Right Side: Search Input + Tombol Reservasi Layanan Baru -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
            <!-- Quick Filter Input -->
            <div class="relative w-full sm:w-64">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari kode booking / layanan..." 
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all">
            </div>

            <!-- Tombol Reservasi Layanan Baru -->
            <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-2 shadow-sm hover:scale-[1.02] active:scale-95 transition-all whitespace-nowrap">
                <i class="ph-duotone ph-plus-circle text-emerald-400 text-base"></i>
                <span>Reservasi Layanan Baru</span>
            </a>
        </div>
    </div>

    <!-- Booking History Cards List -->
    <div class="space-y-4">
        @forelse($bookings as $b)
            @php
                $isPaid = ($b->pembayaran && $b->pembayaran->status === 'Dibayar') || in_array($b->status, ['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai']);
                $paymentStatus = $b->pembayaran?->status ?? 'Belum Dibayar';
                $hasGallery = $b->galeriFotos->count();
                $bookingDate = strtotime($b->tanggal);
                $isUpcoming = $bookingDate >= strtotime('today') && in_array($b->status, ['Dibayar', 'Disetujui', 'Terjadwal']);
                
                // Status badge configuration
                $statusConfig = match($b->status) {
                    'Dibayar', 'Disetujui', 'Terjadwal' => [
                        'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'icon' => 'ph-check-circle',
                        'iconColor' => 'text-emerald-600',
                        'label' => 'Terjadwal & Lunas'
                    ],
                    'Selesai' => [
                        'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
                        'icon' => 'ph-check-square-offset',
                        'iconColor' => 'text-blue-600',
                        'label' => 'Pemotretan Selesai'
                    ],
                    'Dibatalkan', 'Ditolak' => [
                        'bg' => 'bg-rose-50 text-rose-800 border-rose-200',
                        'icon' => 'ph-x-circle',
                        'iconColor' => 'text-rose-600',
                        'label' => $b->status
                    ],
                    default => [
                        'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'icon' => 'ph-clock',
                        'iconColor' => 'text-amber-600',
                        'label' => 'Menunggu Pembayaran'
                    ]
                };
            @endphp

            <div x-show="filterStatus('{{ $b->status }}', '{{ $paymentStatus }}', {{ $hasGallery }}) && ('{{ strtolower(($b->nomor_booking ?? '') . ' ' . ($b->layanan?->nama ?? '') . ' ' . ($b->lokasi ?? '')) }}'.includes(searchQuery.toLowerCase()))"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="clean-card rounded-3xl overflow-hidden border border-slate-200/90 bg-white hover:border-slate-300 hover:shadow-md transition-all duration-300">
                
                <!-- Main Header Strip of Card -->
                <div class="p-5 sm:p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 border-b border-slate-100 bg-linear-to-r from-slate-50/50 via-white to-white">
                    <!-- Left Identity -->
                    <div class="flex items-start sm:items-center gap-4">
                        <!-- Service Thumbnail Avatar -->
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs group relative">
                            @if($b->layanan?->thumbnail)
                                <img src="{{ $b->layanan->thumbnail }}" alt="{{ $b->layanan?->nama ?? 'Layanan' }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="ph-duotone ph-camera text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Service Title & Booking Metadata -->
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-[11px] font-black text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded-lg border border-slate-200">
                                    {{ $b->nomor_booking }}
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">
                                    Dipesan: {{ $b->created_at->format('d M Y, H:i') }}
                                </span>
                                @if($isUpcoming)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                        <i class="ph-duotone ph-sparkle text-xs"></i> Mendatang
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug">
                                {{ $b->layanan?->nama ?? 'Layanan Pemotretan' }}
                            </h3>

                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px]">
                                    Paket: {{ $b->paket?->nama_paket ?? 'Paket Standar' }}
                                </span>
                                <span class="font-mono font-black text-slate-900 text-xs">
                                    Rp {{ number_format($b->paket?->harga ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Status & Main Quick Actions -->
                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto justify-start lg:justify-end shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <!-- Status Badge -->
                        <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border shadow-2xs inline-flex items-center gap-1.5 {{ $statusConfig['bg'] }}">
                            <i class="ph-duotone {{ $statusConfig['icon'] }} text-xs {{ $statusConfig['iconColor'] }}"></i>
                            <span>{{ $statusConfig['label'] }}</span>
                        </span>

                        <!-- Action: Proofing Foto jika sudah ada galeri -->
                        @if($hasGallery > 0)
                            <a href="{{ route('customer.proofing.show', $b->id) }}" 
                               class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95 transition-all"
                               title="Pilih dan kurasi foto hasil pemotretan Anda">
                                <i class="ph-duotone ph-images text-sm"></i>
                                <span>Kurasi Foto ({{ $hasGallery }})</span>
                            </a>
                        @endif

                        <!-- Action: Bayar Sekarang jika belum lunas -->
                        @if(!$isPaid)
                            <a href="{{ route('payments.create', ['booking_id' => $b->id]) }}" 
                               class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs inline-flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95 transition-all">
                                <i class="ph-duotone ph-credit-card text-sm"></i>
                                <span>Bayar Sesi</span>
                            </a>
                        @endif

                        <!-- Action: Invoice PDF jika ada pembayaran lunas -->
                        @if($isPaid)
                            <a href="{{ route('invoices.show', $b->pembayaran?->invoice?->id ?? ($b->pembayaran?->id ?? $b->id)) }}" 
                               target="_blank"
                               class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95 transition-all"
                               title="Unduh / Cetak Bukti Invoice Resmi">
                                <i class="ph-duotone ph-receipt text-emerald-400 text-sm"></i>
                                <span>Invoice PDF</span>
                            </a>
                        @endif

                        <!-- Action: Detail Reservasi -->
                        <a href="{{ route('customer.bookings.show', $b->id) }}" 
                           class="btn-outline px-3.5 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1 shadow-2xs hover:scale-105 active:scale-95 transition-all">
                            <span>Detail</span>
                            <i class="ph-duotone ph-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Secondary Information Strip (Schedule, Location, Crew) -->
                <div class="px-5 sm:px-6 py-3.5 bg-white flex flex-wrap items-center justify-between gap-4 text-xs text-slate-600 font-medium">
                    <div class="flex flex-wrap items-center gap-y-2 gap-x-5">
                        <!-- Tanggal & Waktu -->
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <i class="ph-duotone ph-calendar-blank text-slate-400 text-sm"></i>
                            <span>Jadwal: <strong class="text-slate-900 font-bold">{{ date('d M Y', strtotime($b->tanggal)) }}</strong> ({{ substr($b->waktu, 0, 5) }} WIB)</span>
                        </div>

                        <!-- Lokasi Venue -->
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <i class="ph-duotone ph-map-pin text-slate-400 text-sm"></i>
                            <span class="max-w-xs truncate" title="{{ $b->lokasi }}">Lokasi: <strong class="text-slate-900 font-bold">{{ $b->lokasi }}</strong></span>
                        </div>

                        <!-- Kru Fotografer -->
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <i class="ph-duotone ph-users text-slate-400 text-sm"></i>
                            <span>Fotografer: <strong class="text-slate-900 font-bold">{{ $b->paket?->jumlah_fotografer ?? 1 }} Personil</strong></span>
                        </div>

                        <!-- Metode Pembayaran jika sudah tercatat -->
                        @if($b->pembayaran)
                            <div class="flex items-center gap-1.5 text-slate-700">
                                <i class="ph-duotone ph-wallet text-slate-400 text-sm"></i>
                                <span>Metode: <strong class="text-slate-900 font-bold">{{ $b->pembayaran->metode_pembayaran }}</strong></span>
                            </div>
                        @endif
                    </div>

                    <!-- Hubungi Tim Studio / Bantuan -->
                    <div class="flex items-center gap-2">
                        <a href="https://wa.me/6281234567890?text=Halo%20Studio%20RanggaMotret,%20saya%20ingin%20konsultasi%20mengenai%20booking%20{{ $b->nomor_booking }}" 
                           target="_blank" 
                           class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1 transition-colors">
                            <i class="ph-duotone ph-whatsapp-logo text-sm text-emerald-600"></i>
                            <span>Hubungi Studio</span>
                        </a>
                    </div>
                </div>

                <!-- Note Kebutuhan Khusus jika terisi -->
                @if($b->kebutuhan_khusus)
                    <div class="px-5 sm:px-6 py-2.5 bg-slate-50/80 border-t border-slate-100 text-[11px] text-slate-600 flex items-start gap-2">
                        <i class="ph-duotone ph-note-pencil text-slate-400 text-xs shrink-0 mt-0.5"></i>
                        <span><strong class="text-slate-700 font-bold">Catatan Konsep:</strong> {{ $b->kebutuhan_khusus }}</span>
                    </div>
                @endif
            </div>
        @empty
            <!-- Empty State Card -->
            <div class="clean-card p-12 sm:p-16 text-center rounded-3xl space-y-5 border border-slate-200 bg-white shadow-2xs">
                <div class="w-20 h-20 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-4xl mx-auto shadow-inner">
                    <i class="ph-duotone ph-calendar-x"></i>
                </div>
                <div class="space-y-1.5 max-w-sm mx-auto">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Belum Ada Riwayat Reservasi</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Anda belum pernah membuat pemesanan sesi pemotretan. Pilih paket foto terbaik untuk mengabadikan momen istimewa Anda bersama fotografer profesional.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('customer.bookings.create') }}" class="btn-dark px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider inline-flex items-center gap-2 shadow-md hover:scale-105 active:scale-95 transition-all">
                        <i class="ph-duotone ph-plus-circle text-base text-emerald-400"></i>
                        <span>Mulai Reservasi Pertama</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
