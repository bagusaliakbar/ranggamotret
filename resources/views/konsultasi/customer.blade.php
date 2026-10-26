@extends('layouts.admin')

@section('title', 'Riwayat Konsultasi — RanggaMotret.id')

@section('content')
<div class="space-y-8" x-data="{ 
    openModal: false, 
    activeFilter: 'all',
    searchQuery: '',
    selectedBudget: '',
    expandedChatId: {{ $consultations->first()?->id ?? 'null' }},
    toggleChat(id) {
        this.expandedChatId = (this.expandedChatId === id) ? null : id;
    },
    matchesFilter(itemStatus, itemText) {
        const matchesStatus = (this.activeFilter === 'all') || (itemStatus === this.activeFilter);
        const matchesSearch = !this.searchQuery || itemText.toLowerCase().includes(this.searchQuery.toLowerCase());
        return matchesStatus && matchesSearch;
    }
}">
    
    <!-- Top Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-extrabold">Konsultasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Riwayat Konsultasi</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Diskusikan ide tema visual, rekomendasi outfit, pemilihan lokasi pemotretan, hingga kepastian estimasi budget langsung bersama tim fotografer profesional RanggaMotret.</p>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center gap-3 text-xs font-semibold shadow-sm animate-fade-in">
            <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <i class="ph-duotone ph-check-circle text-xl"></i>
            </div>
            <div class="space-y-0.5">
                <p class="font-extrabold text-sm text-emerald-950">Berhasil!</p>
                <p class="text-emerald-700 font-medium leading-relaxed">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1.5 text-rose-950 text-sm">
                <i class="ph-duotone ph-warning-circle text-xl text-rose-600"></i>
                <span>Terdapat kendala pada formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-4 font-medium">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Konsultasi -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Konsultasi</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-chats-circle"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['total'] }}</div>
                <span class="text-xs font-bold text-slate-500">Topik Sesi</span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium">Histori pengajuan konsep</div>
        </div>

        <!-- 2. Menunggu Balasan -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Menunggu Balasan</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-clock"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl sm:text-3xl font-black text-amber-600">{{ $stats['pending'] }}</div>
                <span class="text-xs font-bold text-amber-600">Ditinjau</span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium">Sedang ditinjau fotografer</div>
        </div>

        <!-- 3. Sudah Ditanggapi -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Sudah Ditanggapi</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-chat-circle-dots"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl sm:text-3xl font-black text-emerald-600">{{ $stats['replied'] }}</div>
                <span class="text-xs font-bold text-emerald-600">Ada Arahan</span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium">Owner telah membalas</div>
        </div>

        <!-- 4. Konsultasi Selesai -->
        <div class="clean-card p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">Konsultasi Selesai</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl sm:text-3xl font-black text-blue-600">{{ $stats['selesai'] ?? 0 }}</div>
                <span class="text-xs font-bold text-blue-600">Tuntas</span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium">Pertanyaan tuntas terjawab</div>
        </div>
    </div>

    <!-- Control Bar: Tabs, Search & Action Button -->
    <div class="clean-card p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <!-- Interactive Category Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto p-1 bg-slate-100 rounded-2xl no-scrollbar text-xs">
            <button type="button" @click="activeFilter = 'all'" 
                    :class="activeFilter === 'all' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-chats-circle"></i>
                <span>Semua Sesi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-200/70 text-slate-700 ml-1">{{ $stats['total'] }}</span>
            </button>

            <button type="button" @click="activeFilter = 'pending'" 
                    :class="activeFilter === 'pending' ? 'bg-white text-amber-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-clock"></i>
                <span>Menunggu Balasan</span>
                @if($stats['pending'] > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 ml-1">{{ $stats['pending'] }}</span>
                @endif
            </button>

            <button type="button" @click="activeFilter = 'replied'" 
                    :class="activeFilter === 'replied' ? 'bg-white text-emerald-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-chat-circle-dots"></i>
                <span>Sudah Dibalas</span>
                @if($stats['replied'] > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 ml-1">{{ $stats['replied'] }}</span>
                @endif
            </button>

            <button type="button" @click="activeFilter = 'selesai'" 
                    :class="activeFilter === 'selesai' ? 'bg-white text-blue-700 font-black shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                <i class="ph-duotone ph-check-circle"></i>
                <span>Selesai</span>
                @if(($stats['selesai'] ?? 0) > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 ml-1">{{ $stats['selesai'] ?? 0 }}</span>
                @endif
            </button>
        </div>

        <!-- Right Side: Search Input + Tombol Ajukan Konsultasi Baru -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari layanan, tema, lokasi..."
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all">
            </div>

            <!-- Tombol Ajukan Konsultasi Baru -->
            <button @click="openModal = true" 
                class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-2 shadow-sm hover:scale-[1.02] active:scale-95 transition-all whitespace-nowrap">
                <i class="ph-duotone ph-plus-circle text-emerald-400 text-base"></i>
                <span>Ajukan Konsultasi Baru</span>
            </button>
        </div>
    </div>

    <!-- Consultations Master-Detail / Feed List -->
    @if($consultations->isEmpty())
        <div class="clean-card p-12 sm:p-16 rounded-3xl border border-slate-200 bg-white text-center space-y-4 shadow-sm">
            <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-4xl border border-emerald-100 shadow-sm">
                <i class="ph-duotone ph-camera-rotate"></i>
            </div>
            <div class="space-y-1 max-w-md mx-auto">
                <h3 class="font-black text-lg text-slate-900 tracking-tight">Belum Ada Pengajuan Konsultasi</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Punya ide foto pernikahan, kelulusan wisuda, potret studio, atau produk komersial? Kirimkan konsep Anda dan diskusikan langsung bersama tim fotografer kami.
                </p>
            </div>
            <div class="pt-2">
                <button @click="openModal = true" class="btn-dark px-6 py-3 rounded-2xl text-xs font-bold inline-flex items-center gap-2 shadow-md hover:scale-105 active:scale-95 transition-all">
                    <i class="ph-duotone ph-plus-circle text-emerald-400 text-base"></i>
                    <span>Ajukan Konsultasi Pertama</span>
                </button>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($consultations as $item)
                @php
                    $searchable = strtolower($item->jenis_layanan . ' ' . ($item->konsep ?? '') . ' ' . ($item->lokasi ?? '') . ' ' . ($item->kebutuhan ?? ''));
                    $msgs = $item->chat_messages;
                    $msgCount = count($msgs);
                    $lastMessage = $msgCount > 0 ? $msgs[$msgCount - 1] : null;

                    $statusConfig = match($item->status) {
                        'selesai' => [
                            'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
                            'icon' => 'ph-check-circle',
                            'iconColor' => 'text-blue-600',
                            'label' => 'Konsultasi Selesai'
                        ],
                        'replied' => [
                            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'icon' => 'ph-chat-circle-dots',
                            'iconColor' => 'text-emerald-600',
                            'label' => 'Dibalas Owner'
                        ],
                        default => [
                            'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'icon' => 'ph-clock',
                            'iconColor' => 'text-amber-600',
                            'label' => 'Menunggu Tanggapan'
                        ]
                    };
                @endphp

                <div x-show="matchesFilter('{{ $item->status }}', '{{ addslashes($searchable) }}')" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="clean-card rounded-3xl overflow-hidden border border-slate-200/90 bg-white hover:border-slate-300 hover:shadow-md transition-all duration-300">
                    
                    <!-- Main Header Strip of Card -->
                    <div class="p-5 sm:p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 border-b border-slate-100 bg-linear-to-r from-slate-50/50 via-white to-white">
                        
                        <!-- Left Identity -->
                        <div class="flex items-start sm:items-center gap-4">
                            <!-- Service/Topic Avatar Icon Box -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs group relative flex items-center justify-center text-slate-700">
                                <i class="ph-duotone ph-chats-circle text-2xl sm:text-3xl text-emerald-600"></i>
                            </div>

                            <!-- Consultation Info & Metadata -->
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-[11px] font-black text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded-lg border border-slate-200">
                                        {{ $item->jenis_layanan }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-400">
                                        Diajukan: {{ $item->created_at->timezone(config('app.timezone', 'Asia/Jakarta'))->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @if($item->status === 'replied')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                            <i class="ph-duotone ph-sparkle text-xs"></i> Ada Arahan Baru
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug">
                                    {{ $item->konsep ?: 'Konsultasi Layanan ' . $item->jenis_layanan }}
                                </h3>

                                @if($item->kebutuhan)
                                    <p class="text-xs text-slate-500 font-medium line-clamp-1 max-w-xl leading-relaxed">
                                        {{ $item->kebutuhan }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-2 text-xs pt-0.5">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px]">
                                        Lokasi: {{ $item->lokasi ?: 'Fleksibel' }}
                                    </span>
                                    <span class="font-mono font-black text-slate-900 text-xs">
                                        {{ $item->budget ? 'Estimasi Rp ' . number_format($item->budget, 0, ',', '.') : 'Budget Fleksibel' }}
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

                            <!-- Action: Selesaikan Chat jika belum selesai -->
                            @if($item->status !== 'selesai')
                                <form method="POST" action="{{ route('customer.consultations.close', $item->id) }}" onsubmit="return confirm('Apakah seluruh pertanyaan Anda sudah terjawab dan ingin mengakhiri sesi chat ini?')">
                                    @csrf
                                    <button type="submit" 
                                        class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-600 hover:text-emerald-800 text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95" 
                                        title="Tandai konsultasi selesai">
                                        <i class="ph-duotone ph-check-square-offset text-sm text-emerald-600"></i>
                                        <span>Selesaikan</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Action: Toggle Ruang Obrolan -->
                            <button type="button" @click="toggleChat({{ $item->id }})" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95 transition-all"
                                :class="expandedChatId === {{ $item->id }} ? 'bg-slate-900 text-white' : 'btn-dark'">
                                <span x-text="expandedChatId === {{ $item->id }} ? 'Tutup Obrolan' : 'Buka Obrolan'"></span>
                                <i class="ph-bold ph-caret-down text-xs transition-transform duration-200" 
                                   :class="expandedChatId === {{ $item->id }} ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Secondary Information Strip (Schedule, Location, Contact, Messages Count) -->
                    <div class="px-5 sm:px-6 py-3.5 bg-white flex flex-wrap items-center justify-between gap-4 text-xs text-slate-600 font-medium">
                        <div class="flex flex-wrap items-center gap-y-2 gap-x-5">
                            <div class="flex items-center gap-1.5 text-slate-700">
                                <i class="ph-duotone ph-calendar-blank text-slate-400 text-sm"></i>
                                <span>Rencana: <strong class="text-slate-900 font-bold">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') : 'Fleksibel' }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-700">
                                <i class="ph-duotone ph-whatsapp-logo text-emerald-600 text-sm"></i>
                                <span>WA: <strong class="text-slate-900 font-bold">{{ $item->phone }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-700">
                                <i class="ph-duotone ph-chats text-slate-400 text-sm"></i>
                                <span>Diskusi: <strong class="text-slate-900 font-bold">{{ $msgCount }} Pesan</strong></span>
                            </div>
                        </div>

                        @if($lastMessage)
                            <div class="text-[11px] text-slate-400 truncate max-w-md">
                                Terakhir oleh <strong class="text-slate-700 font-semibold">{{ $lastMessage['nama'] ?? 'Pengguna' }}</strong>: &ldquo;{{ Str::limit($lastMessage['pesan'] ?? '', 45) }}&rdquo;
                            </div>
                        @endif
                    </div>

                    <!-- Ruang Percakapan & Diskusi Konsep (Collapsible Chat Section) -->
                    <div x-show="expandedChatId === {{ $item->id }}" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-[1000px]"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-[1000px]"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="bg-slate-50/60 p-6 sm:p-7 space-y-5 border-t border-slate-100">
                        
                        <!-- Chat Header Banner Inside Thread -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center shrink-0 shadow-sm relative">
                                    <i class="ph-duotone ph-camera text-emerald-400 text-xl"></i>
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white" title="Fotografer Aktif"></span>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900 flex items-center gap-1.5">
                                        <span>Ruang Diskusi Konsep</span>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Owner RanggaMotret</span>
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">Saluran diskusi pre-booking personal langsung bersama pengelola studio.</p>
                                </div>
                            </div>

                            <div>
                                <span class="text-[11px] font-extrabold text-slate-400">
                                    {{ $msgCount }} Pesan Tercatat
                                </span>
                            </div>
                        </div>

                        <!-- Chat Messages Bubbles Stream -->
                        <div class="space-y-4 max-h-[420px] overflow-y-auto p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 custom-scrollbar shadow-inner">
                            @forelse($item->chat_messages as $msg)
                                @if(($msg['sender'] ?? '') === 'system')
                                    <!-- Bubble Informasi Sistem (Tengah) -->
                                    <div class="my-3 flex items-center justify-center">
                                        <div class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold inline-flex items-center gap-2 border border-slate-200 shadow-2xs">
                                            <i class="ph-duotone ph-info text-xs text-slate-500"></i>
                                            <span>{{ $msg['pesan'] ?? '' }}</span>
                                            <span class="text-[10px] text-slate-400 font-normal">&bull; {{ $msg['waktu'] ?? '' }}</span>
                                        </div>
                                    </div>
                                @elseif(($msg['sender'] ?? '') === 'owner')
                                    <!-- Bubble dari Owner (Sisi Kiri) -->
                                    <div class="flex items-start gap-3 max-w-xl">
                                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0 shadow-xs text-sm mt-0.5">
                                            <i class="ph-duotone ph-camera text-emerald-400"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-extrabold text-slate-900">Owner RanggaMotret</span>
                                                <span class="text-[9px] font-bold text-slate-400">{{ $msg['waktu'] ?? '' }}</span>
                                            </div>
                                            <div class="p-3.5 rounded-2xl rounded-tl-xs bg-slate-50 border border-slate-200 text-slate-800 text-xs leading-relaxed font-medium shadow-2xs">
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
                                            <div class="p-3.5 rounded-2xl rounded-tr-xs bg-slate-900 text-white text-xs leading-relaxed font-medium text-left shadow-2xs">
                                                {{ $msg['pesan'] ?? '' }}
                                            </div>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0 shadow-xs text-xs font-bold mt-0.5">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="text-center py-8 text-slate-400 text-xs font-medium space-y-1">
                                    <i class="ph-duotone ph-chat-circle text-2xl block text-slate-300"></i>
                                    <p>Belum ada percakapan. Tuliskan pesan pertama Anda di bawah.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Chat Footer: Status Selesai ATAU Form Composer Balasan -->
                        @if($item->status === 'selesai')
                            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm border border-slate-800">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shrink-0">
                                            <i class="ph-duotone ph-check-circle"></i>
                                        </span>
                                        <h4 class="font-bold text-sm text-white">Percakapan Konsultasi Telah Selesai</h4>
                                    </div>
                                    <p class="text-xs text-slate-300 font-normal leading-relaxed">
                                        Pertanyaan konsep dan kebutuhan Anda telah selesai dijawab oleh fotografer.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <form method="POST" action="{{ route('customer.consultations.reopen', $item->id) }}" onsubmit="return confirm('Buka kembali obrolan ini jika masih ada pertanyaan lanjutan?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-bold transition-all flex items-center gap-1.5" title="Buka kembali jika masih butuh diskusi">
                                            <i class="ph-duotone ph-arrow-counter-clockwise text-sm text-slate-300"></i>
                                            <span>Buka Obrolan</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Form Composer Balasan -->
                            <form method="POST" action="{{ route('customer.consultations.reply', $item->id) }}" class="space-y-3 pt-1">
                                @csrf
                                <div class="relative">
                                    <textarea name="pesan" rows="2" required 
                                        placeholder="Ketik balasan atau pertanyaan lanjutan mengenai konsep foto, jadwal, atau lokasi..." 
                                        class="w-full px-4 py-3.5 pr-28 rounded-2xl bg-white border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none text-xs text-slate-900 font-medium leading-relaxed transition-all shadow-2xs"></textarea>
                                    
                                    <div class="absolute right-2.5 bottom-3.5">
                                        <button type="submit" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-sm hover:scale-[1.02] active:scale-95 transition-all">
                                            <i class="ph-duotone ph-paper-plane-tilt text-emerald-400 text-sm"></i>
                                            <span>Kirim</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium px-1">
                                    <span class="flex items-center gap-1">
                                        <i class="ph-duotone ph-info"></i> Owner akan menerima notifikasi otomatis begitu Anda mengirimkan pesan.
                                    </span>
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

    <!-- Modal Form: Ajukan Konsultasi Baru (Modern Luxury Form) -->
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
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto text-xs custom-scrollbar">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="space-y-0.5">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 block">Formulir Pre-Booking</span>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Ajukan Konsultasi Konsep Baru</h3>
                    <p class="text-xs text-slate-500 font-medium">Diskusikan kebutuhan sesi Anda bersama fotografer kami (Respons dalam 1x24 jam).</p>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('public.consultation.store') }}" class="space-y-5">
                @csrf

                <!-- Section 1: Data Diri & Kontak -->
                <div class="space-y-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i class="ph-duotone ph-user text-sm text-slate-600"></i>
                        <span>1. Informasi Kontak Klien</span>
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', Auth::user()->name) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-semibold text-xs transition-colors">
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required placeholder="08123456789"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-semibold text-xs transition-colors">
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-semibold text-xs transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Kategori & Rencana Sesi -->
                <div class="space-y-3 pt-2 border-t border-slate-100">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i class="ph-duotone ph-camera text-sm text-slate-600"></i>
                        <span>2. Rencana Pemotretan & Estimasi</span>
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Kategori Layanan <span class="text-rose-500">*</span></label>
                            <select name="jenis_layanan" required 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-extrabold text-xs transition-colors">
                                @foreach($layananList as $lay)
                                    <option value="{{ $lay }}">{{ $lay }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Rencana Tanggal</label>
                            <input type="date" name="tanggal" min="{{ date('Y-m-d') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-semibold text-xs transition-colors">
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Estimasi Budget (Rp)</label>
                            <input type="number" name="budget" placeholder="Misal: 5000000"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-mono font-bold text-xs transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Ide Konsep & Catatan Kebutuhan -->
                <div class="space-y-3 pt-2 border-t border-slate-100">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i class="ph-duotone ph-lightbulb text-sm text-slate-600"></i>
                        <span>3. Ide Konsep & Preferensi Klien</span>
                    </span>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Judul / Ide Tema Konsep</label>
                        <input type="text" name="konsep" placeholder="Contoh: Sunset Outdoor Pantai / Casual Studio Graduation"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-semibold text-xs transition-colors">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Rencana Lokasi / Venue Acara</label>
                        <input type="text" name="lokasi" placeholder="Contoh: Subang Kota / Lembang Bandung / Studio RanggaMotret"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-semibold text-xs transition-colors">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Ceritakan Detail Kebutuhan Anda</label>
                        <textarea name="kebutuhan" rows="3" placeholder="Jelaskan preferensi busana, jumlah anggota keluarga, referensi foto, atau waktu pemotretan yang diinginkan..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium text-xs leading-relaxed transition-colors"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="btn-dark px-6 py-2.5 rounded-xl font-black text-xs flex items-center gap-2 shadow-md hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph-duotone ph-paper-plane-tilt text-emerald-400 text-sm"></i>
                        <span>Kirim Permintaan Konsultasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
