<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Enrollment;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Notifikasi;

/**
 * Controller Pengelolaan Materi, Kuis, & Tugas Modul
 * 
 * Merepresentasikan Control Object 'MateriController' pada Robustness Diagram.
 * Terhubung dengan boundary 'materi' dan entity 'Materi' serta 'Kursus'.
 */
class MateriController extends Controller
{
    /**
     * Tandai materi telah selesai dipelajari
     */
    public function complete($materiId)
    {
        $user = Auth::user();
        $materi = Materi::with('modul.kursus')->findOrFail($materiId);
        $kursus = $materi->modul->kursus;

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursus->id)
            ->firstOrFail();

        MateriProgress::firstOrCreate([
            'user_id' => $user->id,
            'materi_id' => $materi->id,
        ], [
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $enrollment->hitungProgress();

        return back()->with('success', 'Materi berhasil diselesaikan!');
    }

    /**
     * Pengumpulan tugas praktik siswa
     */
    public function submitTugas(Request $request, $tugasId)
    {
        $request->validate([
            'file_tugas' => 'required|file|mimes:jpg,jpeg,png,pdf,zip|max:20480',
            'catatan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $tugas = Tugas::with('modul.kursus')->findOrFail($tugasId);

        $path = $request->file('file_tugas')->store('tugas-submissions', 'public');

        TugasSubmission::updateOrCreate([
            'user_id' => $user->id,
            'tugas_id' => $tugasId,
        ], [
            'file_path' => $path,
            'catatan' => $request->catatan,
            'submitted_at' => now(),
        ]);

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $tugas->modul->kursus_id)
            ->first();
        if ($enrollment) {
            $enrollment->hitungProgress();
        }

        return back()->with('success', 'Tugas praktik berhasil dikumpulkan!');
    }

    /**
     * Evaluasi dan penyerahan kuis evaluasi materi
     */
    public function submitQuiz(Request $request, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::with(['soals', 'modul.kursus'])->findOrFail($quizId);
        $answers = $request->input('answers', []);

        $correct = 0;
        $total = $quiz->soals->count();

        foreach ($quiz->soals as $soal) {
            if (isset($answers[$soal->id]) && strtoupper($answers[$soal->id]) === strtoupper($soal->kunci_jawaban)) {
                $correct++;
            }
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $isPassed = $score >= ($quiz->passing_grade ?? 70);

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'nilai' => $score,
            'is_passed' => $isPassed,
            'detail_jawaban' => $answers,
        ]);

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $quiz->modul->kursus_id)
            ->first();
        if ($enrollment) {
            $enrollment->hitungProgress();
        }

        return back()->with('success', 'Kuis selesai dikerjakan! Nilai Anda: ' . $score . ' (' . ($isPassed ? 'LULUS' : 'REMEDIAL') . ')');
    }
}
