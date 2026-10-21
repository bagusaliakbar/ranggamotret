@extends('layouts.app')

@section('title', 'Quiz: ' . $quiz->judul . ' — RanggaMotret.id')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="clean-card p-8 sm:p-10 rounded-3xl space-y-6 border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-5">
            <div>
                <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-widest bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                    KUIS EVALUASI MODUL
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ $quiz->judul }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    Durasi: <strong class="text-slate-900">{{ $quiz->durasi_menit }} Menit</strong> | Minimal Nilai Kelulusan: <strong class="text-slate-900">{{ $quiz->passing_grade }}%</strong>
                </p>
            </div>
            
            @if($bestAttempt)
                <div class="text-left sm:text-right clean-card px-4 py-2.5 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <span class="text-slate-500 block text-[10px] font-bold">Nilai Terbaik Anda:</span>
                    <span class="font-extrabold text-lg {{ $bestAttempt->is_passed ? 'text-emerald-700' : 'text-amber-700' }}">{{ $bestAttempt->nilai }}%</span>
                </div>
            @endif
        </div>

        @if(session('quiz_result'))
            @php $res = session('quiz_result'); @endphp
            <div class="p-6 rounded-2xl {{ $res['passed'] ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-amber-50 border border-amber-200 text-amber-900' }} text-center space-y-2">
                <div class="text-3xl font-extrabold">{{ $res['score'] }} / 100</div>
                <div class="font-bold text-sm">
                    {{ $res['passed'] ? '🎉 Selamat! Anda Lulus Quiz Ini!' : '⚠️ Nilai Anda Belum Memenuhi Passing Grade (' . $res['passing_grade'] . '%).' }}
                </div>
                <p class="text-xs text-slate-600 font-medium">Anda dapat mencoba ulang quiz untuk memperbaiki nilai akhir.</p>
            </div>
        @endif

        <!-- Quiz Question Player Form -->
        <form method="POST" action="{{ route('student.quiz.submit', $quiz->id) }}" class="space-y-6 text-xs">
            @csrf

            @foreach($quiz->soals as $index => $soal)
                <div class="clean-card p-6 rounded-2xl border border-slate-200 space-y-4 bg-white shadow-sm">
                    <div class="font-bold text-sm text-slate-900 flex items-start gap-2.5 leading-relaxed">
                        <span class="w-6 h-6 rounded-full bg-slate-900 text-white text-[11px] font-extrabold inline-flex items-center justify-center flex-shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <span class="pt-0.5">{{ $soal->pertanyaan }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pl-0 sm:pl-8">
                        <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-300 transition-colors flex items-center gap-3">
                            <input type="radio" name="answers[{{ $soal->id }}]" value="A" required class="text-slate-900 focus:ring-slate-900">
                            <span class="text-slate-800 font-semibold">A. {{ $soal->pilihan_a }}</span>
                        </label>
                        <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-300 transition-colors flex items-center gap-3">
                            <input type="radio" name="answers[{{ $soal->id }}]" value="B" required class="text-slate-900 focus:ring-slate-900">
                            <span class="text-slate-800 font-semibold">B. {{ $soal->pilihan_b }}</span>
                        </label>
                        <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-300 transition-colors flex items-center gap-3">
                            <input type="radio" name="answers[{{ $soal->id }}]" value="C" required class="text-slate-900 focus:ring-slate-900">
                            <span class="text-slate-800 font-semibold">C. {{ $soal->pilihan_c }}</span>
                        </label>
                        <label class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-300 transition-colors flex items-center gap-3">
                            <input type="radio" name="answers[{{ $soal->id }}]" value="D" required class="text-slate-900 focus:ring-slate-900">
                            <span class="text-slate-800 font-semibold">D. {{ $soal->pilihan_d }}</span>
                        </label>
                    </div>
                </div>
            @endforeach

            <button type="submit" class="w-full btn-dark py-4 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Kirim Jawaban Quiz
            </button>
        </form>
    </div>
</div>
@endsection
