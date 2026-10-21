@extends('layouts.app')

@section('title', $kursus->nama . ' — RanggaMotret.id')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    <!-- Course Banner Header -->
    <div class="clean-card p-8 sm:p-10 rounded-3xl space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1 rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs uppercase border border-slate-200">
                        Level: {{ $kursus->level }}
                    </span>
                    @if($kursus->status === 'draft')
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs uppercase">
                            Draft (Preview)
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $kursus->nama }}
                </h1>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                    {{ $kursus->deskripsi }}
                </p>

                <div class="flex flex-wrap items-center gap-6 text-xs text-slate-500 font-medium pt-2 border-t border-slate-100">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-slate-700"></i>
                        <span>Durasi: <strong class="text-slate-900">{{ $kursus->durasi }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-user-tie text-slate-700"></i>
                        <span>Instruktur: <strong class="text-slate-900">{{ $kursus->instruktur->name ?? 'Tim RanggaMotret' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-emerald-700 font-bold">
                        <i class="fa-solid fa-certificate text-emerald-600"></i>
                        <span>Sertifikat Resmi</span>
                    </div>
                </div>
            </div>

            <!-- Price & Registration Card -->
            <div class="clean-card p-6 sm:p-8 rounded-2xl border border-slate-200 space-y-4 text-center bg-slate-50/50 shadow-sm">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Investasi Pembelajaran</div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                    Rp {{ number_format($kursus->harga, 0, ',', '.') }}
                </div>
                
                @auth
                    <a href="{{ route('payments.create', ['kursus_id' => $kursus->id]) }}" class="btn-dark w-full py-3.5 rounded-xl font-bold text-xs shadow-md block transition-all hover:shadow-lg">
                        Daftar & Bayar Akses Kursus
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-dark w-full py-3.5 rounded-xl font-bold text-xs shadow-md block transition-all hover:shadow-lg">
                        Login Untuk Mendaftar Kursus
                    </a>
                @endauth
                <p class="text-[11px] text-slate-500 font-medium leading-normal">
                    Akses seumur hidup termasuk semua modul, materi video/teks, tugas praktik & sertifikat kompetensi.
                </p>
            </div>
        </div>
    </div>

    <!-- Curriculum Modules Accordion / List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <h3 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-book-open text-slate-700"></i> Silabus & Modul Pembelajaran
            </h3>
            <span class="text-xs font-bold text-slate-500">
                Total {{ $kursus->moduls->count() }} Modul
            </span>
        </div>

        <div class="space-y-5">
            @forelse($kursus->moduls as $index => $modul)
                @php
                    // Strip potential redundant "Modul X:" prefix in user input to prevent "Modul 1: Modul 1:"
                    $cleanTitle = preg_replace('/^Modul\s*\d+\s*:\s*/i', '', $modul->judul);
                @endphp
                <div class="clean-card p-6 sm:p-7 rounded-2xl space-y-4 border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h4 class="font-extrabold text-base sm:text-lg text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white text-[11px] font-extrabold inline-flex items-center justify-center flex-shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-slate-900">{{ $cleanTitle }}</span>
                        </h4>
                        <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                            {{ $modul->materis->count() }} Materi
                        </span>
                    </div>

                    @if($modul->deskripsi)
                        <p class="text-xs text-slate-600 leading-relaxed font-medium pl-8">{{ $modul->deskripsi }}</p>
                    @endif

                    <div class="space-y-2.5 pt-2 pl-0 sm:pl-8">
                        @forelse($modul->materis as $materi)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs hover:bg-slate-100/70 transition-colors">
                                <div class="flex items-center gap-3 text-slate-800 font-bold min-w-0">
                                    @if($materi->tipe === 'video')
                                        <i class="fa-solid fa-circle-play text-rose-500 text-base flex-shrink-0"></i>
                                    @elseif($materi->tipe === 'pdf')
                                        <i class="fa-solid fa-file-pdf text-amber-500 text-base flex-shrink-0"></i>
                                    @elseif($materi->tipe === 'link')
                                        <i class="fa-solid fa-link text-blue-500 text-base flex-shrink-0"></i>
                                    @else
                                        <i class="fa-solid fa-file-lines text-slate-600 text-base flex-shrink-0"></i>
                                    @endif
                                    <span class="truncate">{{ $materi->judul }}</span>
                                </div>
                                <span class="text-[10px] text-slate-600 uppercase font-extrabold px-2.5 py-0.5 rounded bg-white border border-slate-200 flex-shrink-0">
                                    {{ $materi->tipe }}
                                </span>
                            </div>
                        @empty
                            @if($modul->tugas->count() === 0 && $modul->quizs->count() === 0)
                                <div class="text-xs text-slate-400 italic">Materi untuk modul ini sedang disiapkan.</div>
                            @endif
                        @endforelse

                        @foreach($modul->tugas as $t)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 font-bold">
                                <div class="flex items-center gap-3 min-w-0">
                                    <i class="fa-solid fa-pen-ruler text-amber-600 text-sm flex-shrink-0"></i>
                                    <span class="truncate">Tugas Praktik: {{ $t->judul }}</span>
                                </div>
                                <span class="text-[10px] uppercase font-extrabold px-2.5 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 flex-shrink-0">
                                    Praktik
                                </span>
                            </div>
                        @endforeach

                        @foreach($modul->quizs as $q)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 font-bold">
                                <div class="flex items-center gap-3 min-w-0">
                                    <i class="fa-solid fa-circle-question text-blue-600 text-sm flex-shrink-0"></i>
                                    <span class="truncate">Quiz Evaluasi: {{ $q->judul }}</span>
                                </div>
                                <span class="text-[10px] uppercase font-extrabold px-2.5 py-0.5 rounded bg-blue-100 text-blue-800 border border-blue-300 flex-shrink-0">
                                    Quiz
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-8 text-center clean-card rounded-2xl text-slate-500 text-xs font-medium">
                    Silabus modul pembelajaran untuk kursus ini sedang disusun oleh instruktur.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
