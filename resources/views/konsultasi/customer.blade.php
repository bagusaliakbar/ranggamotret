@extends('layouts.admin')

@section('title', 'Riwayat Konsultasi — RanggaMotret.id')

@section('content')
<div class="space-y-8" x-data="{ openModal: false, selectedBudget: '' }">
    
    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Riwayat Konsultasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Konsultasi Konsep</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Pantau status diskusi ide pemotretan, rekomendasi paket, dan tanggapan dari tim RanggaMotret.</p>
        </div>

        <div class="shrink-0">
            <button @click="openModal = true" class="btn-dark px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 shadow-md hover:scale-[1.02] active:scale-95 transition-all">
                <i class="ph-duotone ph-plus-circle text-base text-emerald-400"></i>
                <span>Ajukan Konsultasi</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="ph-duotone ph-check-circle text-2xl text-emerald-600 shrink-0"></i>
            <div>
                <p class="font-bold text-sm">Berhasil!</p>
                <p class="text-emerald-700 font-normal">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="ph-duotone ph-warning-circle text-lg text-rose-600"></i>
                <span>Terdapat kesalahan saat pengajuan:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-4 font-medium">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 Stat Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="clean-card p-4 rounded-2xl border border-slate-200 flex items-center gap-3 bg-white shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center shrink-0">
                <i class="ph-duotone ph-chats-circle text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Pengajuan</span>
                <div class="text-xl font-black text-slate-900">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="clean-card p-4 rounded-2xl border border-slate-200 flex items-center gap-3 bg-white shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <i class="ph-duotone ph-clock text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Menunggu Balasan</span>
                <div class="text-xl font-black text-amber-600">{{ $stats['pending'] }}</div>
            </div>
        </div>

        <div class="clean-card p-4 rounded-2xl border border-slate-200 flex items-center gap-3 bg-white shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <i class="ph-duotone ph-chat-circle-dots text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Telah Ditanggapi</span>
                <div class="text-xl font-black text-emerald-600">{{ $stats['replied'] }}</div>
            </div>
        </div>

        <div class="clean-card p-4 rounded-2xl border border-slate-200 flex items-center gap-3 bg-white shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                <i class="ph-duotone ph-check-circle text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Konsultasi Selesai</span>
                <div class="text-xl font-black text-blue-600">{{ $stats['selesai'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Consultations List -->
    @if($consultations->isEmpty())
        <div class="clean-card p-12 rounded-3xl border border-slate-200 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-3xl">
                <i class="ph-duotone ph-chats-circle"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-extrabold text-base text-slate-900">Belum Ada Pengajuan Konsultasi</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda belum pernah mengirimkan diskusi konsep. Mulai konsultasi gratis dengan fotografer kami sekarang.</p>
            </div>
            <button @click="openModal = true" class="btn-dark px-6 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2">
                <i class="ph-duotone ph-plus-circle text-emerald-400"></i>
                <span>Ajukan Konsultasi Pertama</span>
            </button>
        </div>
    @else
        <div class="space-y-5">
            @foreach($consultations as $item)
                <div class="clean-card p-6 sm:p-7 rounded-3xl border {{ $item->status === 'selesai' ? 'border-slate-200 bg-white' : ($item->status === 'replied' ? 'border-emerald-200 bg-white' : 'border-slate-200 bg-white') }} space-y-5 shadow-sm hover:shadow-md transition-shadow">
                    
                    <!-- Card Top Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-lg bg-slate-900 text-white font-extrabold text-xs">
                                    {{ $item->jenis_layanan }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    Diajukan: {{ $item->created_at->timezone(config('app.timezone', 'Asia/Jakarta'))->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                            @if($item->konsep)
                                <h3 class="text-base font-bold text-slate-900">
                                    {{ $item->konsep }}
                                </h3>
                            @endif
                        </div>

                        <!-- Status & End Chat Actions -->
                        <div class="flex items-center flex-wrap gap-2">
                            @if($item->status === 'selesai')
                                <span class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-800 font-bold text-xs border border-blue-200 inline-flex items-center gap-1.5">
                                    <i class="ph-duotone ph-check-circle text-sm text-blue-600"></i> Konsultasi Selesai
                                </span>
                            @elseif($item->status === 'replied')
                                <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300 inline-flex items-center gap-1.5">
                                    <i class="ph-duotone ph-check-circle text-sm text-emerald-600"></i> Sudah Ditanggapi
                                </span>
                                <form method="POST" action="{{ route('customer.consultations.close', $item->id) }}" onsubmit="return confirm('Apakah semua pertanyaan Anda sudah terjawab dan ingin mengakhiri sesi chat ini?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-2xs" title="Tandai konsultasi ini telah selesai">
                                        <i class="ph-duotone ph-check-square-offset text-sm text-emerald-600"></i>
                                        <span>Selesaikan Chat</span>
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 font-bold text-xs border border-amber-300 inline-flex items-center gap-1.5">
                                    <i class="ph-duotone ph-clock text-sm"></i> Menunggu Tanggapan
                                </span>
                                <form method="POST" action="{{ route('customer.consultations.close', $item->id) }}" onsubmit="return confirm('Tutup percakapan konsultasi ini?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 text-[11px] font-bold transition-all inline-flex items-center gap-1" title="Tutup sesi obrolan">
                                        <i class="ph-duotone ph-x-circle text-xs"></i>
                                        <span>Tutup</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Rencana Tanggal:</span>
                            <span class="font-bold text-slate-800">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') : 'Fleksibel / Belum Ditentukan' }}</span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Estimasi Budget:</span>
                            <span class="font-bold text-slate-800 font-mono">{{ $item->budget ? 'Rp ' . number_format($item->budget, 0, ',', '.') : 'Belum Ditentukan' }}</span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Kontak WhatsApp:</span>
                            <span class="font-bold text-slate-800">{{ $item->phone }}</span>
                        </div>
                    </div>

                    <!-- Ruang Percakapan & Diskusi Konsep (Chat Thread) -->
                    <div class="space-y-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="ph-duotone ph-chats-circle text-base text-slate-700"></i> Ruang Diskusi Konsep
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">
                                {{ count($item->chat_messages) }} Pesan
                            </span>
                        </div>

                        <!-- Chat Messages List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto p-4 rounded-2xl bg-slate-50/70 border border-slate-100">
                            @forelse($item->chat_messages as $msg)
                                @if(($msg['sender'] ?? '') === 'system')
                                    <!-- Bubble Informasi Sistem (Tengah) -->
                                    <div class="my-2.5 flex items-center justify-center">
                                        <div class="px-3.5 py-1.5 rounded-full bg-slate-200/80 text-slate-600 text-[10px] font-bold inline-flex items-center gap-1.5 border border-slate-300/80 shadow-2xs">
                                            <i class="ph-duotone ph-info text-xs text-slate-500"></i>
                                            <span>{{ $msg['pesan'] ?? '' }}</span>
                                            <span class="text-[9px] text-slate-400 font-normal">&bull; {{ $msg['waktu'] ?? '' }}</span>
                                        </div>
                                    </div>
                                @elseif(($msg['sender'] ?? '') === 'owner')
                                    <!-- Bubble dari Owner (Sisi Kiri) -->
                                    <div class="flex items-start gap-3 max-w-xl">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs text-sm">
                                            <i class="ph-duotone ph-camera"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-extrabold text-emerald-900">Owner RanggaMotret</span>
                                                <span class="text-[9px] font-bold text-slate-400">{{ $msg['waktu'] ?? '' }}</span>
                                            </div>
                                            <div class="p-3.5 rounded-2xl rounded-tl-xs bg-emerald-50 border border-emerald-200 text-slate-800 text-xs leading-relaxed font-medium shadow-xs">
                                                {{ $msg['pesan'] ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Bubble dari Pelanggan (Sisi Kanan) -->
                                    <div class="flex items-start justify-end gap-3 max-w-xl ml-auto">
                                        <div class="space-y-1 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="text-[9px] font-bold text-slate-400">{{ $msg['waktu'] ?? '' }}</span>
                                                <span class="text-[11px] font-extrabold text-slate-900">Anda ({{ $msg['nama'] ?? 'Pelanggan' }})</span>
                                            </div>
                                            <div class="p-3.5 rounded-2xl rounded-tr-xs bg-slate-900 text-white text-xs leading-relaxed font-medium text-left shadow-xs">
                                                {{ $msg['pesan'] ?? '' }}
                                            </div>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0 shadow-xs text-xs font-bold">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs font-medium">
                                    Belum ada percakapan. Tuliskan pesan pertama Anda di bawah.
                                </div>
                            @endforelse
                        </div>

                        <!-- Action Area: Form Balasan ATAU Banner Selesai + CTA Booking -->
                        @if($item->status === 'selesai')
                            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm border border-slate-700/50">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shrink-0">
                                            <i class="ph-duotone ph-check-circle"></i>
                                        </span>
                                        <h4 class="font-bold text-sm text-white">Percakapan Konsultasi Telah Selesai</h4>
                                    </div>
                                    <p class="text-xs text-slate-300 font-normal leading-relaxed">
                                        Pertanyaan konsep dan kebutuhan Anda telah terjawab. Siap mewujudkan sesi pemotretan impian Anda bersama tim RanggaMotret?
                                    </p>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <form method="POST" action="{{ route('customer.consultations.reopen', $item->id) }}" onsubmit="return confirm('Buka kembali obrolan ini jika masih ada pertanyaan lanjutan?')">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-bold transition-all flex items-center gap-1.5" title="Buka kembali jika masih butuh diskusi">
                                            <i class="ph-duotone ph-arrow-counter-clockwise text-sm text-slate-300"></i>
                                            <span>Buka Obrolan</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('reservasi.create') }}" class="px-4 py-2.5 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 shadow-md hover:scale-[1.02] active:scale-95 bg-emerald-500 hover:bg-emerald-400 text-slate-950">
                                        <i class="ph-duotone ph-calendar-plus text-sm"></i>
                                        <span>Lanjut Booking Foto</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- Form Balas Pesan untuk Pelanggan -->
                            <form method="POST" action="{{ route('customer.consultations.reply', $item->id) }}" class="space-y-3 pt-2">
                                @csrf
                                <div>
                                    <textarea name="pesan" rows="2" required 
                                        placeholder="Tulis balasan atau pertanyaan lanjutan mengenai konsep foto..." 
                                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none text-xs text-slate-900 font-medium leading-relaxed transition-colors shadow-2xs"></textarea>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        <i class="ph-duotone ph-info mr-1"></i> Owner akan menerima notifikasi balasan Anda.
                                    </span>
                                    <button type="submit" class="btn-dark px-5 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 shadow-sm hover:scale-[1.02] active:scale-95 transition-all">
                                        <i class="ph-duotone ph-paper-plane-tilt text-emerald-400 text-sm"></i>
                                        <span>Kirim Balasan</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-4">
                {{ $consultations->links() }}
            </div>
        </div>
    @endif

    <!-- Modal Form: Ajukan Konsultasi Baru -->
    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.outside="openModal = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto text-xs">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Formulir Konsultasi Konsep Baru</h3>
                    <p class="text-xs text-slate-500 font-medium">Tim kreatif kami akan meninjau dan merespons dalam 1x24 jam.</p>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('public.consultation.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', Auth::user()->name) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required placeholder="08123456789"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Kategori Layanan <span class="text-rose-500">*</span></label>
                        <select name="jenis_layanan" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-bold">
                            @foreach($layananList as $lay)
                                <option value="{{ $lay }}">{{ $lay }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Rencana Tanggal</label>
                        <input type="date" name="tanggal" min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Estimasi Budget (Rp)</label>
                        <input type="number" name="budget" placeholder="Misal: 5000000"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Ide Tema & Konsep Lokasi</label>
                    <input type="text" name="konsep" placeholder="Contoh: Sunset Outdoor Pantai / Casual Studio Portrait"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Catatan Kebutuhan Sesi</label>
                    <textarea name="kebutuhan" rows="3" placeholder="Ceritakan detail preferensi pemotretan Anda..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">
                        Batal
                    </button>
                    <button type="submit" class="btn-dark px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-md">
                        <i class="ph-duotone ph-paper-plane-tilt text-emerald-400"></i>
                        <span>Kirim Konsultasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
