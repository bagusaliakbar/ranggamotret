@extends('layouts.admin')

@section('title', 'Kelola Reservasi — Admin RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Reservasi</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Kelola Seluruh Reservasi</h1>
            <p class="text-xs text-slate-600 font-medium">Daftar reservasi sesi pemotretan dari pelanggan.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="clean-card rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode Reservasi</th>
                        <th class="p-4">Nama Customer</th>
                        <th class="p-4">Layanan / Paket</th>
                        <th class="p-4">Jadwal Foto</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi / Galeri</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($bookings as $b)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-mono font-bold text-slate-900">{{ $b->nomor_booking }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $b->customer->name ?? 'Guest' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $b->customer->phone ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $b->layanan->nama }}</div>
                                <div class="text-[10px] text-slate-500">{{ $b->paket->nama_paket }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ date('d M Y', strtotime($b->tanggal)) }} ({{ substr($b->waktu, 0, 5) }})</div>
                                <div class="text-[10px] text-slate-500 truncate max-w-xs">{{ $b->lokasi }}</div>
                            </td>
                            <td class="p-4">
                                @php
                                    $badgeClass = match($b->status) {
                                        'Dibayar', 'Disetujui', 'Terjadwal' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'Selesai' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'Dibatalkan', 'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $badgeClass }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Change Status Form -->
                                    <form method="POST" action="{{ route('admin.bookings.update-status', $b->id) }}">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[11px] text-slate-900 font-bold focus:bg-white focus:outline-none">
                                            @foreach($statuses as $st)
                                                <option value="{{ $st }}" {{ $b->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <a href="{{ route('admin.bookings.show', $b->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold text-[11px] shadow-sm">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Belum ada reservasi masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
