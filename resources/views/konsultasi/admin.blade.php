@extends('layouts.admin')

@section('title', 'Kelola Konsultasi Klien — Admin RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section (Luxury Editorial Breadcrumb & Action Toolbar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Kelola Konsultasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kelola Konsultasi Pre-Booking</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Tanggapi pertanyaan calon klien seputar konsep foto, jadwal sesi pemotretan, estimasi budget, dan rekomendasi paket studio.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.bookings.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-calendar-check text-base text-slate-600"></i>
                <span>Lihat Reservasi</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (Ringkasan Metrik Konsultasi) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Total Permintaan Konsultasi -->
        <a href="{{ route('admin.consultations.index') }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border border-slate-200 shadow-2xs hover:border-slate-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Konsultasi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-chats-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalConsultations) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Semua pertanyaan masuk</div>
        </a>

        <!-- 2. Menunggu Tanggapan -->
        <a href="{{ route('admin.consultations.index', ['status' => 'pending']) }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border-2 {{ $countPending > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' }} shadow-2xs hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider">Perlu Ditanggapi</span>
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-hourglass-medium"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($countPending) }}</div>
            <div class="text-[11px] text-amber-700 font-medium">Calon klien menunggu arahan</div>
        </a>

        <!-- 3. Selesai Ditanggapi -->
        <a href="{{ route('admin.consultations.index', ['status' => 'replied']) }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border border-slate-200 shadow-2xs hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-emerald-700 uppercase tracking-wider">Sudah Dibalas</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-chat-circle-dots"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($countReplied) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Balasan rekomendasi terkirim</div>
        </a>

        <!-- 4. Konsultasi Selesai -->
        <a href="{{ route('admin.consultations.index', ['status' => 'selesai']) }}" class="clean-card p-5 rounded-2xl space-y-1 block bg-white border border-slate-200 shadow-2xs hover:border-blue-400 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-blue-700 uppercase tracking-wider">Konsultasi Selesai</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600">{{ number_format($countSelesai ?? 0) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Diskusi tuntas & terjawab</div>
        </a>
    </div>

    <!-- Filter, Search, & Toolbar -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.consultations.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <!-- Search Query -->
            <div class="sm:col-span-7 relative">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama klien, nomor WA, layanan, konsep, lokasi..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                    <option value="all">Semua Status ({{ $totalConsultations }})</option>
                    <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Tanggapan ({{ $countPending }})</option>
                    <option value="replied" {{ ($status ?? '') === 'replied' ? 'selected' : '' }}>Sudah Dibalas ({{ $countReplied }})</option>
                    <option value="selesai" {{ ($status ?? '') === 'selesai' ? 'selected' : '' }}>Konsultasi Selesai ({{ $countSelesai ?? 0 }})</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-black transition-all text-center shadow-2xs">
                    Filter
                </button>
                @if(!empty($search) || (!empty($status) && $status !== 'all'))
                    <a href="{{ route('admin.consultations.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all flex items-center justify-center shrink-0" title="Reset filter">
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
                <thead class="bg-slate-50 text-slate-900 uppercase font-extrabold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama & Kontak</th>
                        <th class="p-4">Layanan</th>
                        <th class="p-4">Konsep & Lokasi</th>
                        <th class="p-4">Estimasi Budget</th>
                        <th class="p-4">Kebutuhan Klien</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Tanggapan Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($consultations as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors" x-data="{ openReply: false }">
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-xs">{{ $c->nama }}</div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1">
                                    <i class="ph-duotone ph-whatsapp-logo text-emerald-600"></i>
                                    <span>{{ $c->phone }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $c->email }}</div>
                            </td>
                            <td class="p-4">
                                <span class="font-extrabold text-slate-900">{{ $c->jenis_layanan }}</span>
                            </td>
                            <td class="p-4">
                                <div class="text-slate-900 font-semibold">{{ $c->konsep ?: '-' }}</div>
                                <div class="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="ph-duotone ph-map-pin text-slate-400"></i>
                                    <span>{{ $c->lokasi ?: 'Belum ditentukan' }}</span>
                                </div>
                            </td>
                            <td class="p-4 font-mono font-bold text-slate-900">
                                {{ $c->budget ? 'Rp ' . number_format($c->budget, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs">
                                <p class="line-clamp-2" title="{{ $c->kebutuhan }}">{{ $c->kebutuhan }}</p>
                            </td>
                            <td class="p-4 text-center">
                                @if($c->status === 'selesai')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border bg-blue-50 text-blue-700 border-blue-200 inline-flex items-center gap-1">
                                        <i class="ph-duotone ph-check-circle text-xs"></i> Selesai
                                    </span>
                                @elseif($c->status === 'replied')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border bg-emerald-50 text-emerald-700 border-emerald-200 inline-flex items-center gap-1">
                                        <i class="ph-duotone ph-check text-xs"></i> Sudah Dibalas
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border bg-amber-50 text-amber-700 border-amber-200 inline-flex items-center gap-1">
                                        <i class="ph-duotone ph-clock text-xs"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <button @click="openReply = true" 
                                        class="bg-slate-900 hover:bg-black text-white px-3 py-1.5 rounded-xl text-[11px] font-extrabold transition-all shadow-2xs inline-flex items-center gap-1.5 hover:scale-105">
                                    <i class="ph-duotone ph-chats-circle text-xs text-emerald-400"></i>
                                    <span>Diskusi ({{ count($c->chat_messages) }})</span>
                                </button>

                                <!-- Modal Diskusi & Tanggapan Owner -->
                                <div x-show="openReply" 
                                     x-cloak 
                                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                                    <div class="bg-white p-6 rounded-3xl max-w-xl w-full text-left space-y-4 border border-slate-200 shadow-2xl" 
                                         @click.away="openReply = false">
                                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                                            <div>
                                                <h4 class="font-black text-base text-slate-900 flex items-center gap-2">
                                                    <i class="ph-duotone ph-chats-circle text-emerald-600"></i> Ruang Diskusi Konsultasi
                                                </h4>
                                                <p class="text-xs text-slate-500 font-medium">Klien: <strong class="text-slate-800">{{ $c->nama }}</strong> &bull; Layanan: <strong class="text-slate-800">{{ $c->jenis_layanan }}</strong></p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($c->status === 'selesai')
                                                    <form method="POST" action="{{ route('admin.consultations.reopen', $c->id) }}" onsubmit="return confirm('Buka kembali sesi konsultasi ini?')">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-all flex items-center gap-1" title="Buka kembali obrolan">
                                                            <i class="ph-duotone ph-arrow-counter-clockwise text-xs"></i>
                                                            <span>Buka Lagi</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.consultations.close', $c->id) }}" onsubmit="return confirm('Tandai konsultasi ini telah selesai dan terjawab?')">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 text-[11px] font-bold transition-all flex items-center gap-1" title="Tandai selesai">
                                                            <i class="ph-duotone ph-check-circle text-xs text-emerald-600"></i>
                                                            <span>Tandai Selesai</span>
                                                        </button>
                                                    </form>
                                                @endif
                                                <button @click="openReply = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1 rounded-lg">
                                                    <i class="ph ph-x text-lg"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Riwayat Percakapan (Chat History) -->
                                        <div class="space-y-3 max-h-64 overflow-y-auto p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                            @forelse($c->chat_messages as $msg)
                                                @if(($msg['sender'] ?? '') === 'system')
                                                    <!-- Bubble Informasi Sistem (Tengah) -->
                                                    <div class="my-2 flex items-center justify-center">
                                                        <div class="px-3 py-1 rounded-full bg-slate-200/90 text-slate-600 text-[10px] font-bold inline-flex items-center gap-1.5 border border-slate-300">
                                                            <i class="ph-duotone ph-info text-xs text-slate-500"></i>
                                                            <span>{{ $msg['pesan'] ?? '' }}</span>
                                                            <span class="text-[9px] text-slate-400">&bull; {{ $msg['waktu'] ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                @elseif(($msg['sender'] ?? '') === 'owner')
                                                    <!-- Bubble Owner (Kanan) -->
                                                    <div class="flex items-start justify-end gap-2 max-w-md ml-auto">
                                                        <div class="space-y-1 text-right">
                                                            <div class="flex items-center justify-end gap-1.5">
                                                                 <span class="text-[9px] font-bold text-slate-400">{{ $msg['waktu'] ?? '' }}</span>
                                                                <span class="text-[10px] font-extrabold text-emerald-700">Anda (Owner)</span>
                                                            </div>
                                                            <div class="p-3 rounded-2xl rounded-tr-xs bg-emerald-600 text-white text-xs leading-relaxed font-medium text-left shadow-2xs">
                                                                {{ $msg['pesan'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Bubble Pelanggan (Kiri) -->
                                                    <div class="flex items-start gap-2 max-w-md">
                                                        <div class="space-y-1">
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="text-[10px] font-extrabold text-slate-800">{{ $msg['nama'] ?? 'Pelanggan' }}</span>
                                                                <span class="text-[9px] font-bold text-slate-400">{{ $msg['waktu'] ?? '' }}</span>
                                                            </div>
                                                            <div class="p-3 rounded-2xl rounded-tl-xs bg-white border border-slate-200 text-slate-800 text-xs leading-relaxed font-medium shadow-2xs">
                                                                {{ $msg['pesan'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @empty
                                                <p class="text-center text-slate-400 text-xs py-3 font-medium">Belum ada riwayat pesan.</p>
                                            @endforelse
                                        </div>

                                        <!-- Form Kirim Balasan Baru -->
                                        <form method="POST" action="{{ route('admin.consultations.reply', $c->id) }}" class="space-y-3 text-xs">
                                            @csrf
                                            <div>
                                                <label class="block text-slate-700 font-bold mb-1.5">Kirim Balasan / Arahan Konsep</label>
                                                <textarea name="pesan" rows="3" required 
                                                          placeholder="Tuliskan arahan konsep, estimasi biaya, atau jadwal temu studio..."
                                                          class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors leading-relaxed font-medium"></textarea>
                                            </div>
                                            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white py-3 rounded-xl font-extrabold shadow-md transition-all flex items-center justify-center gap-2">
                                                <i class="ph-duotone ph-paper-plane-tilt text-sm text-emerald-400"></i>
                                                <span>Kirim Balasan ke Klien</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
                                    <i class="ph-duotone ph-chats-circle"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Tidak ada konsultasi ditemukan</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Pertanyaan konsultasi pre-booking dari calon klien akan tercantum di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($consultations->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $consultations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
