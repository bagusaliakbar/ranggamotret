@extends('layouts.admin')

@section('title', 'Kelola Reservasi Fotografi — Admin RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section (Luxury Editorial Breadcrumb & Action Toolbar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Reservasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kelola Reservasi Fotografi</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Pantau seluruh sesi pemotretan klien, jadwalkan fotografer, verifikasi persetujuan tanggal, dan moderasi kurasi foto.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('calendar') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-calendar-blank text-base text-slate-600"></i>
                <span>Lihat Kalender Jadwal</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" 
               class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                <i class="ph-duotone ph-receipt text-base text-emerald-400"></i>
                <span>Verifikasi Pembayaran</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (Ringkasan Metrik) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Reservasi -->
        <a href="{{ route('admin.bookings.index') }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border border-slate-200 shadow-2xs hover:border-slate-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Reservasi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-camera"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalBookings) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Semua riwayat pemesanan</div>
        </a>

        <!-- 2. Menunggu Konfirmasi -->
        <a href="{{ route('admin.bookings.index', ['status' => 'Menunggu Konfirmasi']) }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border-2 {{ $countPending > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' }} shadow-2xs hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider">Perlu Konfirmasi</span>
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-hourglass-medium"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($countPending) }}</div>
            <div class="text-[11px] text-amber-700 font-medium">Jadwal menunggu persetujuan</div>
        </a>

        <!-- 3. Jadwal Aktif / Terjadwal -->
        <a href="{{ route('admin.bookings.index', ['status' => 'Terjadwal']) }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border border-slate-200 shadow-2xs hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Jadwal Siap</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-calendar-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($countScheduled) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Telah dibayar & terkonfirmasi</div>
        </a>

        <!-- 4. Selesai Tuntas -->
        <a href="{{ route('admin.bookings.index', ['status' => 'Selesai']) }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border border-slate-200 shadow-2xs hover:border-blue-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai Tuntas</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600">{{ number_format($countCompleted) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Sesi & kurasi foto tuntas</div>
        </a>
    </div>

    <!-- Filter, Search, & Toolbar Component -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <!-- Search Query -->
            <div class="sm:col-span-6 relative">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kode booking, nama customer, email, telepon, lokasi..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
            </div>

            <!-- Filter Status Dropdown -->
            <div class="sm:col-span-4">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                    <option value="all">Semua Status ({{ $totalBookings }})</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-black transition-all text-center shadow-2xs">
                    Filter
                </button>
                @if(!empty($search) || (!empty($status) && $status !== 'all'))
                    <a href="{{ route('admin.bookings.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all flex items-center justify-center shrink-0" title="Reset filter">
                        <i class="ph-duotone ph-arrows-counter-clockwise text-base"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card Container -->
    <div class="clean-card rounded-3xl overflow-hidden shadow-2xs border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="p-4">Kode Reservasi</th>
                        <th class="p-4">Nama Pelanggan</th>
                        <th class="p-4">Layanan & Paket</th>
                        <th class="p-4">Jadwal & Lokasi Foto</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Tindakan Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($bookings as $b)
                        @php
                            $badgeClass = match($b->status) {
                                'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'Pemotretan Selesai' => 'bg-purple-50 text-purple-800 border-purple-200',
                                'Selesai' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'Dibatalkan', 'Ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-amber-50 text-amber-800 border-amber-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4">
                                <div class="font-mono font-black text-slate-900 text-xs">#{{ $b->nomor_booking }}</div>
                                <div class="text-[10px] text-slate-400">{{ $b->created_at ? $b->created_at->format('d M Y, H:i') : '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $b->customer->name ?? 'Guest User' }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $b->customer->phone ?? ($b->customer->email ?? '-') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900">{{ $b->layanan->nama ?? 'Layanan' }}</div>
                                <div class="text-[11px] text-slate-500 font-semibold">{{ $b->paket->nama_paket ?? 'Paket Foto' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 inline-flex items-center gap-1.5">
                                    <i class="ph-duotone ph-calendar-blank text-slate-400"></i>
                                    <span>{{ $b->tanggal ? date('d M Y', strtotime($b->tanggal)) : '-' }}</span>
                                    <span class="text-slate-400">({{ substr($b->waktu, 0, 5) }} WIB)</span>
                                </div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5 flex items-center gap-1">
                                    <i class="ph-duotone ph-map-pin text-slate-400"></i>
                                    <span title="{{ $b->lokasi }}">{{ $b->lokasi ?: 'Studio RanggaMotret' }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $badgeClass }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Change Status Form Quick Selector -->
                                    <form method="POST" action="{{ route('admin.bookings.update-status', $b->id) }}">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" 
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-[11px] text-slate-900 font-bold focus:bg-white focus:outline-none transition-all cursor-pointer">
                                            @foreach($statuses as $st)
                                                <option value="{{ $st }}" {{ $b->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <!-- Detail Action Button -->
                                    <a href="{{ route('admin.bookings.show', $b->id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-[11px] shadow-2xs inline-flex items-center gap-1 transition-all hover:scale-105"
                                       title="Lihat Rincian & Proofing">
                                        <span>Detail</span>
                                        <i class="ph-duotone ph-arrow-up-right text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
                                    <i class="ph-duotone ph-calendar-blank"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Tidak ada reservasi ditemukan</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Coba sesuaikan kata kunci pencarian atau ganti filter status di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
