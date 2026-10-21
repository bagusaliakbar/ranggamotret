<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Quiz;
use App\Models\QuizSoal;
use App\Models\QuizAttempt;
use App\Models\Notifikasi;

class QuizController extends Controller
{
    // Take Quiz (Redirects to in-page LMS learn player)
    public function show($quizId)
    {
        $quiz = Quiz::with(['soals', 'modul.kursus'])->findOrFail($quizId);
        return redirect()->route('student.learn', [$quiz->modul->kursus_id, 'quiz_id' => $quiz->id]);
    }

    // Submit Quiz Attempt
    public function submit(Request $request, $quizId)
    {
        $quiz = Quiz::with(['soals', 'modul'])->findOrFail($quizId);
        $userAnswers = $request->input('answers', []); // [soal_id => 'A', ...]

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

        $attempt = QuizAttempt::create([
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
}
