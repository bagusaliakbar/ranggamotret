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
    
    <!-- Top Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white p-6 sm:p-8 shadow-xl border border-slate-800">
        <!-- Subtle Background Glow Elements -->
        <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-[11px] font-bold text-emerald-400 border border-white/10">
                    <i class="ph-duotone ph-sparkle text-xs"></i>
                    <span>Pre-Booking Direct Creative Advisory</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight">
                    Riwayat Konsultasi Konsep
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                    Diskusikan ide tema visual, rekomendasi outfit, pemilihan lokasi pemotretan, hingga kepastian estimasi budget langsung bersama fotografer profesional RanggaMotret.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <button @click="openModal = true" 
                    class="px-5 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs flex items-center gap-2 shadow-lg shadow-emerald-500/25 hover:scale-[1.02] active:scale-95 transition-all">
                    <i class="ph-duotone ph-plus-circle text-lg"></i>
                    <span>Ajukan Konsultasi Baru</span>
                </button>
            </div>
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

    <!-- 4 Interactive Filter Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Semua -->
        <button type="button" @click="activeFilter = 'all'" 
            :class="activeFilter === 'all' ? 'border-slate-900 bg-slate-900 text-white shadow-md ring-2 ring-slate-900/10' : 'border-slate-200 bg-white text-slate-800 hover:border-slate-300'"
            class="clean-card p-4 sm:p-5 rounded-3xl border transition-all text-left flex flex-col justify-between space-y-3 cursor-pointer group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider" :class="activeFilter === 'all' ? 'text-slate-300' : 'text-slate-400'">
                    Total Pengajuan
                </span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                     :class="activeFilter === 'all' ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-700'">
                    <i class="ph-duotone ph-chats-circle"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight" :class="activeFilter === 'all' ? 'text-white' : 'text-slate-900'">
                    {{ $stats['total'] }}
                </div>
                <span class="text-[11px] font-medium" :class="activeFilter === 'all' ? 'text-slate-300' : 'text-slate-500'">Semua topik diskusi</span>
            </div>
        </button>

        <!-- 2. Menunggu Balasan -->
        <button type="button" @click="activeFilter = 'pending'"
            :class="activeFilter === 'pending' ? 'border-amber-500 bg-amber-500 text-white shadow-md ring-2 ring-amber-500/20' : 'border-slate-200 bg-white text-slate-800 hover:border-amber-300'"
            class="clean-card p-4 sm:p-5 rounded-3xl border transition-all text-left flex flex-col justify-between space-y-3 cursor-pointer group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider" :class="activeFilter === 'pending' ? 'text-amber-100' : 'text-amber-600'">
                    Menunggu Balasan
                </span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                     :class="activeFilter === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600 border border-amber-100'">
                    <i class="ph-duotone ph-clock"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight" :class="activeFilter === 'pending' ? 'text-white' : 'text-amber-600'">
                    {{ $stats['pending'] }}
                </div>
                <span class="text-[11px] font-medium" :class="activeFilter === 'pending' ? 'text-amber-100' : 'text-slate-500'">Sedang ditinjau fotografer</span>
            </div>
        </button>

        <!-- 3. Sudah Dibalas -->
        <button type="button" @click="activeFilter = 'replied'"
            :class="activeFilter === 'replied' ? 'border-emerald-600 bg-emerald-600 text-white shadow-md ring-2 ring-emerald-600/20' : 'border-slate-200 bg-white text-slate-800 hover:border-emerald-300'"
            class="clean-card p-4 sm:p-5 rounded-3xl border transition-all text-left flex flex-col justify-between space-y-3 cursor-pointer group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider" :class="activeFilter === 'replied' ? 'text-emerald-100' : 'text-emerald-600'">
                    Sudah Ditanggapi
                </span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                     :class="activeFilter === 'replied' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                    <i class="ph-duotone ph-chat-circle-dots"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight" :class="activeFilter === 'replied' ? 'text-white' : 'text-emerald-600'">
                    {{ $stats['replied'] }}
                </div>
                <span class="text-[11px] font-medium" :class="activeFilter === 'replied' ? 'text-emerald-100' : 'text-slate-500'">Ada arahan dari Owner</span>
            </div>
        </button>

        <!-- 4. Konsultasi Selesai -->
        <button type="button" @click="activeFilter = 'selesai'"
            :class="activeFilter === 'selesai' ? 'border-blue-600 bg-blue-600 text-white shadow-md ring-2 ring-blue-600/20' : 'border-slate-200 bg-white text-slate-800 hover:border-blue-300'"
            class="clean-card p-4 sm:p-5 rounded-3xl border transition-all text-left flex flex-col justify-between space-y-3 cursor-pointer group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider" :class="activeFilter === 'selesai' ? 'text-blue-100' : 'text-blue-600'">
                    Konsultasi Selesai
                </span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                     :class="activeFilter === 'selesai' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 border border-blue-100'">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight" :class="activeFilter === 'selesai' ? 'text-white' : 'text-blue-600'">
                    {{ $stats['selesai'] ?? 0 }}
                </div>
                <span class="text-[11px] font-medium" :class="activeFilter === 'selesai' ? 'text-blue-100' : 'text-slate-500'">Pertanyaan tuntas terjawab</span>
            </div>
        </button>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative w-full sm:w-80">
            <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" x-model="searchQuery" placeholder="Cari layanan, tema konsep, lokasi..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
        </div>

        <!-- Filter Tab Buttons -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 text-xs">
            <button type="button" @click="activeFilter = 'all'"
                :class="activeFilter === 'all' ? 'bg-slate-900 text-white font-black shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold'"
                class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                Semua ({{ $stats['total'] }})
            </button>
            <button type="button" @click="activeFilter = 'pending'"
                :class="activeFilter === 'pending' ? 'bg-amber-500 text-white font-black shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold'"
                class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                Menunggu ({{ $stats['pending'] }})
            </button>
            <button type="button" @click="activeFilter = 'replied'"
                :class="activeFilter === 'replied' ? 'bg-emerald-600 text-white font-black shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold'"
                class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                Ditanggapi ({{ $stats['replied'] }})
            </button>
            <button type="button" @click="activeFilter = 'selesai'"
                :class="activeFilter === 'selesai' ? 'bg-blue-600 text-white font-black shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold'"
                class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                Selesai ({{ $stats['selesai'] ?? 0 }})
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
        <div class="space-y-6">
            @foreach($consultations as $item)
                @php
                    $searchable = strtolower($item->jenis_layanan . ' ' . ($item->konsep ?? '') . ' ' . ($item->lokasi ?? '') . ' ' . ($item->kebutuhan ?? ''));
                    $msgs = $item->chat_messages;
                    $msgCount = count($msgs);
                    $lastMessage = $msgCount > 0 ? $msgs[$msgCount - 1] : null;
                @endphp

                <div x-show="matchesFilter('{{ $item->status }}', '{{ addslashes($searchable) }}')" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="clean-card rounded-3xl border {{ $item->status === 'selesai' ? 'border-slate-200 bg-white' : ($item->status === 'replied' ? 'border-emerald-200/90 bg-white ring-1 ring-emerald-500/10' : 'border-slate-200 bg-white') }} overflow-hidden shadow-xs hover:shadow-md transition-all">
                    
                    <!-- Card Header Strip -->
                    <div class="p-6 sm:p-7 border-b border-slate-100 space-y-5">
                        
                        <!-- Top Meta & Status Badge -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="px-3 py-1 rounded-xl bg-slate-900 text-white font-black text-xs inline-flex items-center gap-1.5 shadow-2xs">
                                    <i class="ph-duotone ph-camera text-emerald-400"></i>
                                    <span>{{ $item->jenis_layanan }}</span>
                                </span>
                                <span class="text-xs text-slate-400 font-medium inline-flex items-center gap-1">
                                    <i class="ph-duotone ph-calendar-blank"></i>
                                    <span>Diajukan: {{ $item->created_at->timezone(config('app.timezone', 'Asia/Jakarta'))->translatedFormat('d M Y, H:i') }}</span>
                                </span>
                            </div>

                            <!-- Status Pill Badge -->
                            <div>
                                @if($item->status === 'selesai')
                                    <span class="px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-700 font-extrabold text-xs border border-blue-200 inline-flex items-center gap-1.5 shadow-2xs">
                                        <i class="ph-duotone ph-check-circle text-sm text-blue-600"></i>
                                        <span>Konsultasi Selesai</span>
                                    </span>
                                @elseif($item->status === 'replied')
                                    <span class="px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-800 font-extrabold text-xs border border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <i class="ph-duotone ph-chat-circle-dots text-sm text-emerald-600"></i>
                                        <span>Sudah Ditanggapi Owner</span>
                                    </span>
                                @else
                                    <span class="px-3.5 py-1.5 rounded-full bg-amber-50 text-amber-800 font-extrabold text-xs border border-amber-200 inline-flex items-center gap-1.5 shadow-2xs">
                                        <i class="ph-duotone ph-clock text-sm text-amber-600"></i>
                                        <span>Menunggu Tanggapan</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Concept Headline & Topic -->
                        <div class="space-y-1">
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                                {{ $item->konsep ?: 'Konsultasi Layanan ' . $item->jenis_layanan }}
                            </h3>
                            @if($item->kebutuhan)
                                <p class="text-xs text-slate-600 font-medium line-clamp-2 leading-relaxed">
                                    {{ $item->kebutuhan }}
                                </p>
                            @endif
                        </div>

                        <!-- Metadata Micro-Grid (Tanggal, Budget, Lokasi & Kontak) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/90 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-700 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-2xs">
                                    <i class="ph-duotone ph-calendar text-base text-emerald-600"></i>
                                </div>
                                <div class="truncate">
                                    <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Rencana Tanggal</span>
                                    <span class="font-extrabold text-slate-800 truncate">
                                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') : 'Fleksibel / Belum Ditentukan' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/90 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-700 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-2xs">
                                    <i class="ph-duotone ph-wallet text-base text-emerald-600"></i>
                                </div>
                                <div class="truncate">
                                    <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Estimasi Budget</span>
                                    <span class="font-black text-slate-900 font-mono">
                                        {{ $item->budget ? 'Rp ' . number_format($item->budget, 0, ',', '.') : 'Belum Ditentukan' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100/90 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-700 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-2xs">
                                    <i class="ph-duotone ph-whatsapp-logo text-base text-emerald-600"></i>
                                </div>
                                <div class="truncate">
                                    <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Kontak WhatsApp</span>
                                    <span class="font-extrabold text-slate-800 truncate">{{ $item->phone }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bar Pengontrol & Cuplikan Pesan Terakhir -->
                        <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs text-slate-500 font-medium flex items-center gap-2 truncate">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-[11px] inline-flex items-center gap-1 shrink-0">
                                    <i class="ph-duotone ph-chats text-xs text-emerald-600"></i>
                                    <span>{{ $msgCount }} Pesan</span>
                                </span>
                                @if($lastMessage)
                                    <span class="text-slate-400 truncate">
                                        Terakhir oleh <strong class="text-slate-700 font-semibold">{{ $lastMessage['nama'] ?? 'Pengguna' }}</strong>: 
                                        &ldquo;{{ Str::limit($lastMessage['pesan'] ?? '', 45) }}&rdquo;
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($item->status !== 'selesai')
                                    <form method="POST" action="{{ route('customer.consultations.close', $item->id) }}" onsubmit="return confirm('Apakah seluruh pertanyaan Anda sudah terjawab dan ingin mengakhiri sesi chat ini?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-600 hover:text-emerald-800 text-xs font-bold transition-all inline-flex items-center gap-1.5" title="Selesaikan konsultasi">
                                            <i class="ph-duotone ph-check-square-offset text-sm text-emerald-600"></i>
                                            <span>Selesaikan Chat</span>
                                        </button>
                                    </form>
                                @endif

                                <button type="button" @click="toggleChat({{ $item->id }})" 
                                    class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2"
                                    :class="expandedChatId === {{ $item->id }} ? 'bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'">
                                    <span x-text="expandedChatId === {{ $item->id }} ? 'Tutup Obrolan' : 'Buka Ruang Obrolan'"></span>
                                    <i class="ph-bold ph-caret-down transition-transform duration-200" 
                                       :class="expandedChatId === {{ $item->id }} ? 'rotate-180' : ''"></i>
                                </button>
                            </div>
                        </div>
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
