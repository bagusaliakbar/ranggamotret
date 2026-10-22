<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Notifikasi;
use Carbon\Carbon;

/**
 * Controller Manajemen Materi, Tugas, & Kuis Pembelajaran (ICONIX Robustness: MateriController)
 * 
 * Bertanggung jawab atas:
 * - Pencatatan progres kelulusan materi bacaan & video
 * - Penyerahan berkas tugas praktik siswa
 * - Pelaksanaan dan penilaian otomatis kuis modul
 * - Penilaian dan pemberian feedback oleh Instruktur
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
     * Penyerahan tugas praktik oleh siswa
     */
    public function submitTugas(Request $request, $tugasId)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string',
            'file_submission' => 'nullable|file|mimes:jpg,jpeg,png,zip,pdf,rar|max:20480',
            'link_submission' => 'nullable|string|max:500',
        ]);

        $tugas = Tugas::with('modul')->findOrFail($tugasId);
        $filePath = null;

        if ($request->hasFile('file_submission')) {
            $file = $request->file('file_submission');
            $filename = time() . '_' . Auth::id() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/tugas'), $filename);
            $filePath = 'uploads/tugas/' . $filename;
        } elseif (!empty($validated['link_submission'])) {
            $filePath = $validated['link_submission'];
        }

        if (!$filePath && empty($validated['catatan'])) {
            return back()->with('error', 'Silakan unggah berkas tugas atau sertakan tautan / catatan.');
        }

        TugasSubmission::updateOrCreate(
            ['tugas_id' => $tugasId, 'user_id' => Auth::id()],
            [
                'file_path' => $filePath ?? 'Submitted via text notes',
                'catatan' => $validated['catatan'] ?? null,
                'tanggal_submit' => Carbon::now(),
                'status' => 'submitted',
            ]
        );

        return redirect()->route('student.learn', [$tugas->modul->kursus_id, 'tugas_id' => $tugasId])
            ->with('success', 'Tugas praktik Anda berhasil dikirim!');
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

    /**
     * Instruktur melihat daftar pengumpulan tugas siswa
     */
    public function instructorAssignmentsIndex()
    {
        $submissions = TugasSubmission::with(['tugas.modul.kursus', 'user'])
            ->latest()
            ->paginate(20);

        return view('instructor.assignments.index', compact('submissions'));
    }

    /**
     * Instruktur memberikan nilai dan ulasan pada tugas siswa
     */
    public function gradeAssignment(Request $request, $submissionId)
    {
        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
            'status' => 'required|in:graded,revision_requested',
        ]);

        $submission = TugasSubmission::findOrFail($submissionId);
        $submission->nilai = $validated['nilai'];
        $submission->feedback = $validated['feedback'];
        $submission->status = $validated['status'];
        $submission->save();

        Notifikasi::create([
            'user_id' => $submission->user_id,
            'judul' => 'Nilai Tugas Diberikan',
            'pesan' => 'Tugas "' . $submission->tugas->judul . '" telah dinilai: ' . $submission->nilai . '/100.',
            'link' => '/student/courses/' . $submission->tugas->modul->kursus_id . '/learn',
        ]);

        return back()->with('success', 'Nilai dan feedback berhasil disimpan.');
    }

    // Method aliases for backward-compatibility
    public function submit(Request $request, $tugasId)
    {
        return $this->submitTugas($request, $tugasId);
    }

    public function show($quizId)
    {
        return $this->showQuiz($quizId);
    }

    public function instructorIndex()
    {
        return $this->instructorAssignmentsIndex();
    }

    public function grade(Request $request, $submissionId)
    {
        return $this->gradeAssignment($request, $submissionId);
    }
}
