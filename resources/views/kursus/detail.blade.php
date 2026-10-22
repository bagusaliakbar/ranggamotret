@extends('layouts.app')

@section('title', $kursus->nama . ' — Akademi RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-slate-900">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('public.courses') }}" class="hover:text-slate-900">Katalog Kursus</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-900 truncate max-w-xs">{{ $kursus->nama }}</span>
    </div>

    <!-- Main Grid: Content & Sticky Investment Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- Left: Course Details, Instructor & Syllabus (8 Cols) -->
        <div class="lg:col-span-8 space-y-10">
            <!-- Header Hero Card -->
            <div class="clean-card p-8 sm:p-10 rounded-3xl space-y-6 shadow-sm border border-slate-200">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3.5 py-1 rounded-full bg-slate-900 text-white font-extrabold text-[11px] uppercase tracking-wider shadow-sm">
                        Level: {{ $kursus->level }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[11px] uppercase border border-blue-200">
                        <i class="fa-solid fa-infinity mr-1"></i> Akses Seumur Hidup
                    </span>
                    @if($kursus->status === 'draft')
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs uppercase">
                            Draft (Pratinjau)
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-[1.15] tracking-tight">
                    {{ $kursus->nama }}
                </h1>

                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                    {{ $kursus->deskripsi }}
                </p>

                <!-- Course Meta Highlights -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-100 text-xs">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Total Durasi</span>
                        <span class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-slate-700"></i> {{ $kursus->durasi }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Kurikulum</span>
                        <span class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-slate-700"></i> {{ $kursus->moduls->count() }} Modul
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Sertifikat</span>
                        <span class="font-extrabold text-emerald-700 text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-award text-emerald-600"></i> Resmi & QR
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Bahasa</span>
                        <span class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-language text-slate-700"></i> Bahasa Indo
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pengampu Kursus (Owner) -->
            <div class="clean-card p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-20 h-20 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-2xl shrink-0 shadow-md">
                    {{ strtoupper(substr($kursus->instruktur->name ?? 'R', 0, 1)) }}
                </div>
                <div class="space-y-1.5 flex-1">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Pengampu (Owner)</span>
                    <h3 class="text-xl font-black text-slate-900">{{ $kursus->instruktur->name ?? 'Owner RanggaMotret' }}</h3>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed">
                        Praktisi fotografi profesional sekaligus Founder & Owner RanggaMotret berpengalaman lebih dari 8 tahun dalam wedding komersial, portraiture, dan lighting studio terapan.
                    </p>
                </div>
            </div>

            <!-- Curriculum Modules Accordion / List -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Silabus Kursus</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1 flex items-center gap-2.5">
                            <i class="fa-solid fa-book-open text-slate-700"></i> Modul & Materi Pembelajaran
                        </h3>
                    </div>
                    <span class="text-xs font-extrabold text-slate-600 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                        {{ $kursus->moduls->count() }} Modul Pembelajaran
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($kursus->moduls as $index => $modul)
                        @php
                            $cleanTitle = preg_replace('/^Modul\s*\d+\s*:\s*/i', '', $modul->judul);
                        @endphp
                        <div class="clean-card p-6 sm:p-7 rounded-2xl space-y-4 border-slate-200 shadow-sm hover:border-slate-300 transition-all">
                            <div class="flex items-center justify-between">
                                <h4 class="font-extrabold text-base sm:text-lg text-slate-900 flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-extrabold inline-flex items-center justify-center shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <span>{{ $cleanTitle }}</span>
                                </h4>
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200">
                                    {{ $modul->materis->count() }} Materi
                                </span>
                            </div>

                            @if($modul->deskripsi)
                                <p class="text-xs text-slate-600 leading-relaxed font-medium pl-0 sm:pl-10">{{ $modul->deskripsi }}</p>
                            @endif

                            <div class="space-y-2.5 pt-2 pl-0 sm:pl-10">
                                @forelse($modul->materis as $mIndex => $materi)
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs hover:bg-slate-100 transition-colors">
                                        <div class="flex items-center gap-3 text-slate-800 font-bold min-w-0">
                                            @if($materi->tipe === 'video')
                                                <i class="fa-solid fa-circle-play text-rose-500 text-base shrink-0"></i>
                                            @elseif($materi->tipe === 'pdf')
                                                <i class="fa-solid fa-file-pdf text-amber-500 text-base shrink-0"></i>
                                            @elseif($materi->tipe === 'link')
                                                <i class="fa-solid fa-link text-blue-500 text-base shrink-0"></i>
                                            @else
                                                <i class="fa-solid fa-file-lines text-slate-600 text-base shrink-0"></i>
                                            @endif
                                            <span class="truncate">{{ $materi->judul }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($index === 0 && $mIndex === 0)
                                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-extrabold uppercase">
                                                    Pratinjau Gratis
                                                </span>
                                            @endif
                                            <span class="text-[10px] text-slate-600 uppercase font-extrabold px-2.5 py-0.5 rounded bg-white border border-slate-200">
                                                {{ $materi->tipe }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    @if($modul->tugas->count() === 0 && $modul->quizs->count() === 0)
                                        <div class="text-xs text-slate-400 italic">Materi untuk modul ini sedang disiapkan.</div>
                                    @endif
                                @endforelse

                                @foreach($modul->tugas as $t)
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-950 font-bold">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <i class="fa-solid fa-pen-ruler text-amber-600 text-sm shrink-0"></i>
                                            <span class="truncate">Tugas Praktik: {{ $t->judul }}</span>
                                        </div>
                                        <span class="text-[10px] uppercase font-extrabold px-2.5 py-0.5 rounded bg-amber-200 text-amber-900 border border-amber-300 shrink-0">
                                            Upload Karya
                                        </span>
                                    </div>
                                @endforeach

                                @foreach($modul->quizs as $q)
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-950 font-bold">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <i class="fa-solid fa-circle-question text-blue-600 text-sm shrink-0"></i>
                                            <span class="truncate">Kuis Evaluasi: {{ $q->judul }}</span>
                                        </div>
                                        <span class="text-[10px] uppercase font-extrabold px-2.5 py-0.5 rounded bg-blue-200 text-blue-900 border border-blue-300 shrink-0">
                                            Evaluasi Poin
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center clean-card rounded-3xl text-slate-500 text-xs font-medium">
                            Silabus modul pembelajaran untuk kursus ini sedang disusun oleh Owner.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Sticky Investment & Registration Card (4 Cols) -->
        <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
            <div class="clean-card p-6 sm:p-8 rounded-3xl border border-slate-200 space-y-6 bg-white shadow-xl">
                <!-- Cover Image Preview -->
                <div class="aspect-[16/10] rounded-2xl overflow-hidden bg-slate-200 border border-slate-200">
                    <img src="{{ $kursus->thumbnail }}" alt="{{ $kursus->nama }}" class="w-full h-full object-cover">
                </div>

                <div class="space-y-1">
                    <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Investasi Pembelajaran</div>
                    <div class="text-3xl sm:text-4xl font-black text-slate-900">
                        Rp {{ number_format($kursus->harga, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-emerald-700 font-bold flex items-center gap-1 pt-1">
                        <i class="fa-solid fa-circle-check"></i> Pembayaran 1x untuk akses selamanya
                    </p>
                </div>
                
                @auth
                    <a href="{{ route('payments.create', ['kursus_id' => $kursus->id]) }}" class="btn-dark w-full py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-slate-900/15 block text-center transition-all hover:scale-[1.02] active:scale-95">
                        <i class="fa-solid fa-bolt text-amber-400 mr-1.5"></i> Daftar & Bayar Akses Sekarang
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-dark w-full py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-slate-900/15 block text-center transition-all hover:scale-[1.02] active:scale-95">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Masuk Untuk Mendaftar Kursus
                    </a>
                @endauth

                <!-- Course Inclusions Checklist -->
                <div class="space-y-3 pt-4 border-t border-slate-100 text-xs font-semibold text-slate-700">
                    <span class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wider block">Fasilitas Peserta:</span>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Akses video & materi teks 24/7</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Tugas praktik dievaluasi oleh Owner</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Kuis interaktif uji kompetensi</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Sertifikat kelulusan dengan QR Code resmi</span>
                    </div>
                </div>
            </div>

            <!-- Guarantee Seal Box -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600 text-xs space-y-2 text-center">
                <i class="fa-solid fa-shield-halved text-2xl text-slate-900"></i>
                <div class="font-extrabold text-slate-900">100% Kepuasan Pembelajaran</div>
                <p class="text-[11px] text-slate-500 font-medium">Materi diperbarui secara berkala mengikuti perkembangan teknologi kamera dan tren industri fotografi terkini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
