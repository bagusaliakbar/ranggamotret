@extends('layouts.app')

@section('title', 'Ruang Belajar — ' . $kursus->nama . ' — RanggaMotret.id')

@section('content')
@php
    $allCompleted = $allCompleted ?? false;
    $sertifikat = $sertifikat ?? null;
    $completedMateriIds = $completedMateriIds ?? [];
    $activeType = $activeType ?? 'materi';
    $progressPercentage = $progressPercentage ?? 0;

    // Build sequential navigation across modules, lessons, quizzes, and assignments
    $allItems = collect();
    foreach($kursus->moduls as $m) {
        foreach($m->materis as $mat) {
            $allItems->push([
                'type' => 'materi',
                'id' => $mat->id,
                'title' => $mat->judul,
                'url' => route('student.learn', [$kursus->id, 'materi_id' => $mat->id])
            ]);
        }
        foreach($m->quizs as $quz) {
            $allItems->push([
                'type' => 'quiz',
                'id' => $quz->id,
                'title' => 'Kuis: ' . $quz->judul,
                'url' => route('student.learn', [$kursus->id, 'quiz_id' => $quz->id])
            ]);
        }
        foreach($m->tugas as $tug) {
            $allItems->push([
                'type' => 'tugas',
                'id' => $tug->id,
                'title' => 'Tugas: ' . $tug->judul,
                'url' => route('student.learn', [$kursus->id, 'tugas_id' => $tug->id])
            ]);
        }
    }
    $currentActiveId = $activeMateri->id ?? ($activeQuiz->id ?? ($activeTugas->id ?? null));
    $currentIndex = $allItems->search(fn($it) => $it['type'] === $activeType && $it['id'] == $currentActiveId);
    $prevItem = ($currentIndex !== false && $currentIndex > 0) ? $allItems->get($currentIndex - 1) : null;
    $nextItem = ($currentIndex !== false && $currentIndex < $allItems->count() - 1) ? $allItems->get($currentIndex + 1) : null;
@endphp

<div class="bg-slate-50 min-h-screen py-6 sm:py-8"
     x-data="{ 
        theaterMode: false,
        activeTab: 'content',
        studentNotes: localStorage.getItem('rm_notes_{{ $kursus->id }}') || '',
        notesSaved: false,
        saveNotes() {
            localStorage.setItem('rm_notes_{{ $kursus->id }}', this.studentNotes);
            this.notesSaved = true;
            setTimeout(() => this.notesSaved = false, 2000);
        }
     }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Studio Classroom Bar -->
        <div class="clean-card p-4 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-[11px] font-bold text-slate-500">
                    <a href="{{ route('student.dashboard') }}" class="hover:text-slate-900 inline-flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left"></i> Dashboard Belajar
                    </a>
                    <span>•</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase tracking-wider">
                        Level: {{ $kursus->level ?? 'Semua Tingkat' }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $kursus->nama }}</h1>
                <p class="text-xs text-slate-500 font-medium">
                    Instruktur: <strong class="text-slate-800">{{ $kursus->instruktur->name ?? 'Tim RanggaMotret' }}</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
                <!-- Theater Mode Toggle Button -->
                <button type="button" @click="theaterMode = !theaterMode" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold border transition-all flex items-center gap-1.5"
                        :class="theaterMode ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                    <i class="fa-solid" :class="theaterMode ? 'fa-compress' : 'fa-expand'"></i>
                    <span x-text="theaterMode ? 'Mode Standar' : 'Theater Mode'"></span>
                </button>

                <!-- Progress Indicator -->
                <div class="w-40 sm:w-44 space-y-1.5">
                    <div class="flex justify-between text-xs font-extrabold">
                        <span class="text-slate-600">Progress</span>
                        <span class="{{ $progressPercentage >= 100 ? 'text-emerald-700' : 'text-slate-900' }}">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                        <div class="h-full {{ $progressPercentage >= 100 ? 'bg-emerald-600' : 'bg-slate-900' }} transition-all duration-500" style="width: {{ $progressPercentage }}%;"></div>
                    </div>
                </div>

                <!-- Certificate Actions -->
                @if($allCompleted && !$sertifikat)
                    <form method="POST" action="{{ route('student.certificate.issue', $kursus->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md flex items-center gap-1.5 transition-all animate-bounce">
                            <i class="fa-solid fa-award"></i> Klaim Sertifikat
                        </button>
                    </form>
                @elseif($sertifikat)
                    <a href="{{ route('student.certificate.show', $sertifikat->id) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-award"></i> Sertifikat Saya
                    </a>
                @endif
            </div>
        </div>

        <!-- Main Classroom Layout Grid (Dynamic Theater Mode) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <!-- LEFT: Main Player Area (8 Cols or 12 Cols in Theater Mode) -->
            <div :class="theaterMode ? 'lg:col-span-12' : 'lg:col-span-8'" class="space-y-6 transition-all duration-300">

                <!-- 1. ACTIVE VIEW: IN-PAGE QUIZ EVALUATION -->
                @if($activeType === 'quiz' && isset($activeQuiz))
                    <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6 bg-white border border-slate-200 shadow-sm">
                        <!-- Quiz Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                            <div class="space-y-1">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fa-solid fa-list-check mr-1"></i> Kuis Evaluasi Modul
                                </span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $activeQuiz->judul }}</h2>
                                <p class="text-xs text-slate-500 font-medium">
                                    Durasi: <strong class="text-slate-800">{{ $activeQuiz->durasi_menit ?? 15 }} Menit</strong> • Passing Grade: <strong class="text-slate-800">{{ $activeQuiz->passing_grade ?? 75 }}%</strong> • Total: <strong class="text-slate-800">{{ $activeQuiz->soals->count() }} Pertanyaan</strong>
                                </p>
                            </div>

                            @if($bestAttempt)
                                <div class="px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-right">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase">Nilai Tertinggi</div>
                                    <div class="text-lg font-black {{ $bestAttempt->is_passed ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $bestAttempt->nilai }}% <span class="text-xs font-bold">({{ $bestAttempt->is_passed ? 'Lulus' : 'Belum Lulus' }})</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Result Flash Banner -->
                        @if(session('quiz_result'))
                            @php $res = session('quiz_result'); @endphp
                            <div class="p-6 rounded-2xl {{ $res['passed'] ? 'bg-emerald-50 border border-emerald-200 text-emerald-950' : 'bg-amber-50 border border-amber-200 text-amber-950' }} space-y-2 text-center shadow-inner">
                                <div class="w-12 h-12 rounded-2xl {{ $res['passed'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-xl mx-auto">
                                    <i class="fa-solid {{ $res['passed'] ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                                </div>
                                <div class="text-3xl font-black">{{ $res['score'] }} / 100</div>
                                <div class="font-extrabold text-sm">
                                    {{ $res['passed'] ? '🎉 Selamat! Anda Berhasil Lulus Kuis Evaluasi!' : '⚠️ Nilai Anda Belum Mencapai Passing Grade (' . $res['passing_grade'] . '%).' }}
                                </div>
                                <p class="text-xs text-slate-600 font-medium max-w-md mx-auto">
                                    {{ $res['passed'] ? 'Pemahaman Anda pada modul ini sangat baik. Silakan lanjutkan ke modul berikutnya.' : 'Silakan tinjau kembali video dan materi bacaan, lalu ulangi kuis untuk menaikkan nilai Anda.' }}
                                </p>
                            </div>
                        @endif

                        <!-- In-Page Interactive Quiz Form -->
                        @if($activeQuiz->soals->count() > 0)
                            <form method="POST" action="{{ route('student.quiz.submit', $activeQuiz->id) }}" class="space-y-6 text-xs">
                                @csrf

                                <div class="space-y-6">
                                    @foreach($activeQuiz->soals as $index => $soal)
                                        <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4 shadow-sm hover:border-slate-300 transition-all">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3">
                                                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-extrabold text-xs inline-flex items-center justify-center shrink-0 mt-0.5">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <div class="font-bold text-sm text-slate-900 leading-relaxed pt-0.5">
                                                        {{ $soal->pertanyaan }}
                                                    </div>
                                                </div>
                                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-600 shrink-0">
                                                    {{ $soal->bobot_nilai ?? 20 }} Poin
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                                <label class="p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-400 transition-all flex items-center gap-3">
                                                    <input type="radio" name="answers[{{ $soal->id }}]" value="A" required class="text-slate-900 focus:ring-slate-900 w-4 h-4">
                                                    <span class="text-slate-800 font-semibold leading-snug"><strong class="text-slate-900">A.</strong> {{ $soal->pilihan_a }}</span>
                                                </label>
                                                <label class="p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-400 transition-all flex items-center gap-3">
                                                    <input type="radio" name="answers[{{ $soal->id }}]" value="B" required class="text-slate-900 focus:ring-slate-900 w-4 h-4">
                                                    <span class="text-slate-800 font-semibold leading-snug"><strong class="text-slate-900">B.</strong> {{ $soal->pilihan_b }}</span>
                                                </label>
                                                <label class="p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-400 transition-all flex items-center gap-3">
                                                    <input type="radio" name="answers[{{ $soal->id }}]" value="C" required class="text-slate-900 focus:ring-slate-900 w-4 h-4">
                                                    <span class="text-slate-800 font-semibold leading-snug"><strong class="text-slate-900">C.</strong> {{ $soal->pilihan_c }}</span>
                                                </label>
                                                <label class="p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-400 transition-all flex items-center gap-3">
                                                    <input type="radio" name="answers[{{ $soal->id }}]" value="D" required class="text-slate-900 focus:ring-slate-900 w-4 h-4">
                                                    <span class="text-slate-800 font-semibold leading-snug"><strong class="text-slate-900">D.</strong> {{ $soal->pilihan_d }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="pt-4 border-t border-slate-100">
                                    <button type="submit" class="w-full btn-dark py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-paper-plane text-emerald-400"></i> Kirim & Evaluasi Jawaban Kuis
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>

                <!-- 2. ACTIVE VIEW: IN-PAGE TUGAS PRAKTIK -->
                @elseif($activeType === 'tugas' && isset($activeTugas))
                    <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6 bg-white border border-slate-200 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                            <div class="space-y-1">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-pen-ruler mr-1"></i> Tugas Praktik Modul
                                </span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $activeTugas->judul }}</h2>
                                <p class="text-xs text-slate-500 font-medium">
                                    Batas Waktu: <strong class="text-slate-800">{{ $activeTugas->deadline ? date('d M Y', strtotime($activeTugas->deadline)) : 'Fleksibel' }}</strong>
                                </p>
                            </div>

                            @if($tugasSubmission)
                                <div class="px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-right">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase">Status Tugas</div>
                                    <div class="text-xs font-black text-emerald-700 flex items-center gap-1 justify-end">
                                        <i class="fa-solid fa-circle-check"></i> Sudah Terkirim
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Instruksi & Panduan Pengerjaan</h4>
                            <p class="text-xs text-slate-700 leading-relaxed font-medium">
                                {{ $activeTugas->instruksi ?? 'Kerjakan tugas praktik fotografi sesuai dengan instruksi modul dan unggah berkas hasil karya Anda.' }}
                            </p>
                        </div>

                        @if($tugasSubmission)
                            <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="font-extrabold text-xs flex items-center gap-2">
                                        <i class="fa-solid fa-file-arrow-up text-emerald-600 text-base"></i>
                                        <span>Berkas Tugas Anda Telah Dikirim</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700">
                                        {{ $tugasSubmission->tanggal_submit ? date('d M Y H:i', strtotime($tugasSubmission->tanggal_submit)) : '' }}
                                    </span>
                                </div>

                                @if($tugasSubmission->nilai !== null)
                                    <div class="p-3 bg-white rounded-xl border border-emerald-200 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700">Nilai Instruktur:</span>
                                        <span class="text-base font-black text-emerald-700">{{ $tugasSubmission->nilai }} / 100</span>
                                    </div>
                                    @if($tugasSubmission->feedback)
                                        <p class="text-xs text-slate-600 italic">"{{ $tugasSubmission->feedback }}"</p>
                                    @endif
                                @else
                                    <p class="text-xs text-emerald-800 font-medium">
                                        Tugas Anda sedang dalam antrean evaluasi dan review oleh instruktur.
                                    </p>
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('student.tugas.submit', $activeTugas->id) }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-slate-900 font-bold">Unggah Berkas Foto / ZIP Karya Tugas <span class="text-slate-400 font-normal">(Maks. 20MB)</span></label>
                                <input type="file" name="file_submission" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-slate-900 font-bold">Atau Tautan Google Drive / Cloud <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="link_submission" placeholder="https://drive.google.com/..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-slate-900 focus:outline-none font-medium">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-slate-900 font-bold">Catatan Pengerjaan / Pengaturan Kamera (Shutter, Aperture, ISO)</label>
                                <textarea name="catatan" rows="3" placeholder="Tuliskan pengaturan kamera dan konsep foto yang Anda ambil..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-slate-900 focus:outline-none font-medium leading-relaxed"></textarea>
                            </div>

                            <button type="submit" class="w-full btn-dark py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up text-amber-400"></i> {{ $tugasSubmission ? 'Kirim Ulang / Perbarui Tugas' : 'Kirim Berkas Tugas Praktik' }}
                            </button>
                        </form>
                    </div>

                <!-- 3. ACTIVE VIEW: VIDEO & TEXT LESSON MATERIAL -->
                @elseif($activeType === 'materi' && isset($activeMateri))
                    <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6 bg-white border border-slate-200 shadow-sm">
                        <!-- Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                            <div class="space-y-1">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ strtoupper($activeMateri->tipe ?? 'TEKS') }} LESSON
                                </span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $activeMateri->judul }}</h2>
                            </div>

                            <!-- Mark Complete Form -->
                            <form method="POST" action="{{ route('student.materi.complete', $activeMateri->id) }}">
                                @csrf
                                @if(in_array($activeMateri->id, $completedMateriIds))
                                    <button type="button" disabled class="px-4 py-2.5 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Sudah Selesai
                                    </button>
                                @else
                                    <button type="submit" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 hover:bg-slate-800 transition-all">
                                        <i class="fa-solid fa-check text-emerald-400"></i> Tandai Selesai
                                    </button>
                                @endif
                            </form>
                        </div>

                        <!-- Video Player Embed -->
                        @php
                            $videoUrl = $activeMateri->video_url ?? $activeMateri->url_media ?? null;
                            $embedUrl = null;
                            if ($videoUrl) {
                                if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
                                    $embedUrl = str_replace('watch?v=', 'embed/', $videoUrl);
                                } elseif (str_contains($videoUrl, 'youtu.be/')) {
                                    $embedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $videoUrl);
                                } else {
                                    $embedUrl = $videoUrl;
                                }
                            }
                            $isiMateri = $activeMateri->isi ?? $activeMateri->konten ?? '';
                        @endphp

                        @if($activeMateri->tipe === 'video' && $embedUrl)
                            <div class="aspect-video rounded-2xl overflow-hidden bg-slate-950 shadow-xl border border-slate-800">
                                <iframe class="w-full h-full" src="{{ $embedUrl }}" title="{{ $activeMateri->judul }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @endif

                        <!-- Text Prose Content -->
                        @if(!empty($isiMateri))
                            <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-4 pt-2 font-medium">
                                {!! nl2br(e($isiMateri)) !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- In-Page Student Notes Pad (Auto-Saved) -->
                <div class="clean-card p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-xs text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-notebook text-slate-700"></i> Catatan Siswa Pribadi (Tersimpan Otomatis)
                        </span>
                        <span x-show="notesSaved" x-transition class="text-[10px] text-emerald-600 font-bold">
                            <i class="fa-solid fa-check"></i> Tersimpan
                        </span>
                    </div>
                    <textarea x-model="studentNotes" @input="saveNotes()" rows="3" 
                              placeholder="Tuliskan catatan penting materi, rumus eksposur kamera, atau ide yang terlintas saat belajar..."
                              class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:outline-none transition-colors leading-relaxed"></textarea>
                </div>

                <!-- Bottom Navigation: Prev & Next Lesson -->
                <div class="flex items-center justify-between gap-4 pt-2">
                    @if($prevItem)
                        <a href="{{ $prevItem['url'] }}" class="btn-outline px-5 py-3 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-chevron-left"></i>
                            <span class="truncate max-w-[150px] sm:max-w-xs">{{ $prevItem['title'] }}</span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextItem)
                        <a href="{{ $nextItem['url'] }}" class="btn-dark px-6 py-3 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-md hover:scale-[1.02] active:scale-95 transition-all">
                            <span class="truncate max-w-[150px] sm:max-w-xs">{{ $nextItem['title'] }}</span>
                            <i class="fa-solid fa-chevron-right text-emerald-400"></i>
                        </a>
                    @endif
                </div>

            </div>

            <!-- RIGHT: Curriculum & Syllabus Sidebar (4 Cols, Hidden in Theater Mode) -->
            <div x-show="!theaterMode" class="lg:col-span-4 space-y-4">
                <div class="clean-card p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-slate-500"></i> Silabus & Modul
                        </h3>
                        <span class="text-[10px] font-extrabold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                            {{ $kursus->moduls->count() }} Modul
                        </span>
                    </div>

                    <div class="space-y-4 text-xs" x-data="{ activeAccordion: 0 }">
                        @foreach($kursus->moduls as $index => $mod)
                            @php
                                $cleanTitle = preg_replace('/^Modul\s*\d+\s*:\s*/i', '', $mod->judul);
                                $isCurrentModul = (
                                    (isset($activeMateri) && $activeMateri->modul_id === $mod->id) ||
                                    (isset($activeQuiz) && $activeQuiz->modul_id === $mod->id) ||
                                    (isset($activeTugas) && $activeTugas->modul_id === $mod->id)
                                );
                            @endphp

                            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                <button type="button" 
                                        @click="activeAccordion = (activeAccordion === {{ $index }} ? -1 : {{ $index }})"
                                        class="w-full p-3.5 bg-slate-100/80 hover:bg-slate-100 text-left font-extrabold text-slate-900 flex items-center justify-between gap-2 transition-colors">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="w-5 h-5 rounded bg-slate-900 text-white text-[10px] font-black inline-flex items-center justify-center shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="truncate text-xs">{{ $cleanTitle }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': activeAccordion === {{ $index }} }"></i>
                                    </div>
                                </button>

                                <div x-show="activeAccordion === {{ $index }} || {{ $isCurrentModul ? 'true' : 'false' }}" class="p-2 space-y-1.5 bg-white border-t border-slate-100">
                                    @foreach($mod->materis as $mat)
                                        @php
                                            $isActive = (isset($activeMateri) && $activeMateri->id === $mat->id && $activeType === 'materi');
                                            $isDone = in_array($mat->id, $completedMateriIds);
                                        @endphp
                                        <a href="{{ route('student.learn', [$kursus->id, 'materi_id' => $mat->id]) }}"
                                            class="flex items-center justify-between p-2.5 rounded-xl font-semibold transition-all {{ $isActive ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-50' }}">
                                            <div class="flex items-center gap-2.5 truncate">
                                                <i class="fa-solid {{ $isActive ? 'fa-circle-play text-white' : ($mat->tipe === 'video' ? 'fa-circle-play text-rose-500' : 'fa-file-lines text-slate-400') }} text-xs shrink-0"></i>
                                                <span class="truncate text-xs">{{ $mat->judul }}</span>
                                            </div>
                                            @if($isDone)
                                                <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0 ml-2"></i>
                                            @endif
                                        </a>
                                    @endforeach

                                    @foreach($mod->quizs as $qz)
                                        @php
                                            $isQuizActive = (isset($activeQuiz) && $activeQuiz->id === $qz->id && $activeType === 'quiz');
                                            $attempt = $qz->bestAttemptOf(Auth::id());
                                        @endphp
                                        <a href="{{ route('student.learn', [$kursus->id, 'quiz_id' => $qz->id]) }}"
                                            class="flex items-center justify-between p-2.5 rounded-xl font-bold transition-all border {{ $isQuizActive ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-blue-50/70 border-blue-200 text-blue-900 hover:bg-blue-100' }}">
                                            <div class="flex items-center gap-2 truncate">
                                                <i class="fa-solid fa-list-check {{ $isQuizActive ? 'text-white' : 'text-blue-600' }} text-xs shrink-0"></i>
                                                <span class="truncate text-xs">Kuis: {{ $qz->judul }}</span>
                                            </div>
                                            @if($attempt && $attempt->is_passed)
                                                <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-600 text-white shrink-0 ml-2">
                                                    {{ $attempt->nilai }}%
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach

                                    @foreach($mod->tugas as $tug)
                                        @php
                                            $isTugasActive = (isset($activeTugas) && $activeTugas->id === $tug->id && $activeType === 'tugas');
                                            $sub = $tug->submissionOf(Auth::id());
                                        @endphp
                                        <a href="{{ route('student.learn', [$kursus->id, 'tugas_id' => $tug->id]) }}"
                                            class="flex items-center justify-between p-2.5 rounded-xl font-bold transition-all border {{ $isTugasActive ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-amber-50/70 border-amber-200 text-amber-900 hover:bg-amber-100' }}">
                                            <div class="flex items-center gap-2 truncate">
                                                <i class="fa-solid fa-pen-ruler {{ $isTugasActive ? 'text-white' : 'text-amber-600' }} text-xs shrink-0"></i>
                                                <span class="truncate text-xs">Tugas: {{ $tug->judul }}</span>
                                            </div>
                                            @if($sub)
                                                <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-600 text-white shrink-0 ml-2">
                                                    Terkirim
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
