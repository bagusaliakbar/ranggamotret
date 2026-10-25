<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Notifikasi;
use Carbon\Carbon;

/**
 * Controller Manajemen Materi & Kuis Pembelajaran (ICONIX Robustness: MateriController)
 * 
 * Bertanggung jawab atas:
 * - Pencatatan progres kelulusan materi bacaan & video
 * - Pelaksanaan dan penilaian otomatis kuis modul
 */
class MateriController extends Controller
{
    /**
     * Menandai penyelesaian materi pembelajaran
     */
    public function complete($materiId)
    {
        $user = Auth::user();
        MateriProgress::updateOrCreate(
            ['user_id' => $user->id, 'materi_id' => $materiId],
            ['is_completed' => true]
        );

        return back()->with('success', 'Materi berhasil ditandai sebagai selesai!');
    }

    /**
     * Membuka halaman kuis dalam ruang belajar
     */
    public function showQuiz($quizId)
    {
        $quiz = Quiz::with(['soals', 'modul.kursus'])->findOrFail($quizId);
        return redirect()->route('student.learn', [$quiz->modul->kursus_id, 'quiz_id' => $quiz->id]);
    }

    /**
     * Menerima dan mengkalkulasi jawaban kuis siswa secara otomatis
     */
    public function submitQuiz(Request $request, $quizId)
    {
        $quiz = Quiz::with(['soals', 'modul'])->findOrFail($quizId);
        $userAnswers = $request->input('answers', []);

        $totalScore = 0;
        $maxPossibleScore = 0;

        foreach ($quiz->soals as $soal) {
            $maxPossibleScore += $soal->bobot_nilai;
            if (isset($userAnswers[$soal->id]) && strtoupper($userAnswers[$soal->id]) === $soal->jawaban_benar) {
                $totalScore += $soal->bobot_nilai;
            }
        }

        $finalPercentage = $maxPossibleScore > 0 ? round(($totalScore / $maxPossibleScore) * 100) : 0;
        $isPassed = $finalPercentage >= $quiz->passing_grade;

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'nilai' => $finalPercentage,
            'is_passed' => $isPassed,
        ]);

        Notifikasi::create([
            'user_id' => Auth::id(),
            'judul' => 'Hasil Quiz: ' . $quiz->judul,
            'pesan' => 'Anda memperoleh nilai ' . $finalPercentage . '% (' . ($isPassed ? 'Lulus' : 'Belum Lulus') . ').',
            'link' => '/student/courses/' . $quiz->modul->kursus_id . '/learn?quiz_id=' . $quiz->id,
        ]);

        return redirect()->route('student.learn', [$quiz->modul->kursus_id, 'quiz_id' => $quiz->id])
            ->with('quiz_result', [
                'score' => $finalPercentage,
                'passed' => $isPassed,
                'passing_grade' => $quiz->passing_grade
            ]);
    }

    // Method aliases for backward-compatibility
    public function show($quizId)
    {
        return $this->showQuiz($quizId);
    }
}
