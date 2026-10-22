<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kursus;
use App\Models\Modul;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Enrollment;
use App\Models\Sertifikat;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;

/**
 * Controller Manajemen Kursus & LMS Akademi (ICONIX Robustness: KursusController)
 * 
 * Bertanggung jawab atas:
 * - Eksplorasi katalog kursus publik
 * - Ruang belajar (classroom LMS player) & modul silabus
 * - Penerbitan & verifikasi sertifikat kelulusan digital
 * - Manajemen kursus, modul, & materi oleh Instruktur / Admin
 */
class KursusController extends Controller
{
    // ==========================================
    // 1. PUBLIC COURSES (Boundary: kursus)
    // ==========================================

    /**
     * Menampilkan katalog kursus publik
     */
    public function index(Request $request)
    {
        $level = $request->query('level');
        $query = Kursus::with(['instruktur', 'moduls.materis'])->where('status', 'aktif');

        if ($level) {
            $query->where('level', $level);
        }

        $kursus = $query->latest()->get();
        return view('public.courses', compact('kursus', 'level'));
    }

    /**
     * Menampilkan detail kursus dan silabus
     */
    public function detail($id)
    {
        $kursus = Kursus::with(['instruktur', 'moduls.materis', 'testimoni.user'])->findOrFail($id);
        $isEnrolled = Auth::check() && Enrollment::where('user_id', Auth::id())->where('kursus_id', $id)->exists();

        return view('public.course-detail', compact('kursus', 'isEnrolled'));
    }

    // ==========================================
    // 2. STUDENT LMS CLASSROOM (Boundary: ruang belajar siswa)
    // ==========================================

    /**
     * Menampilkan daftar kursus yang diikuti siswa
     */
    public function studentIndex()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['kursus.instruktur', 'kursus.moduls.materis'])
            ->get();

        $certificates = Sertifikat::where('user_id', $user->id)->with('kursus')->get();

        return view('student.dashboard', compact('enrollments', 'certificates'));
    }

    /**
     * Ruang belajar interaktif siswa (LMS Player)
     */
    public function learn($kursusId, Request $request)
    {
        $user = Auth::user();

        // Pastikan pengguna terdaftar
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursusId)
            ->firstOrFail();

        $kursus = Kursus::with([
            'moduls.materis',
            'moduls.tugas.submissions' => fn($q) => $q->where('user_id', $user->id),
            'moduls.quizs.attempts' => fn($q) => $q->where('user_id', $user->id),
        ])->findOrFail($kursusId);

        $materiId = $request->query('materi_id');
        $quizId = $request->query('quiz_id');
        $tugasId = $request->query('tugas_id');

        $activeType = 'materi';
        $activeMateri = null;
        $activeQuiz = null;
        $activeTugas = null;
        $bestAttempt = null;
        $tugasSubmission = null;

        if ($quizId) {
            $activeQuiz = Quiz::with(['soals', 'modul'])->findOrFail($quizId);
            $bestAttempt = $activeQuiz->bestAttemptOf($user->id);
            $activeType = 'quiz';
        } elseif ($tugasId) {
            $activeTugas = Tugas::with(['submissions' => fn($q) => $q->where('user_id', $user->id), 'modul'])->findOrFail($tugasId);
            $tugasSubmission = $activeTugas->submissionOf($user->id);
            $activeType = 'tugas';
        } elseif ($materiId) {
            $activeMateri = Materi::findOrFail($materiId);
            $activeType = 'materi';
        } else {
            $firstModul = $kursus->moduls->first();
            if ($firstModul && $firstModul->materis->count() > 0) {
                $activeMateri = $firstModul->materis->first();
                $activeType = 'materi';
            } elseif ($firstModul && $firstModul->quizs->count() > 0) {
                $activeQuiz = $firstModul->quizs->first();
                $bestAttempt = $activeQuiz->bestAttemptOf($user->id);
                $activeType = 'quiz';
            } elseif ($firstModul && $firstModul->tugas->count() > 0) {
                $activeTugas = $firstModul->tugas->first();
                $tugasSubmission = $activeTugas->submissionOf($user->id);
                $activeType = 'tugas';
            }
        }

        // Kalkulasi Progres Belajar
        $totalMateris = 0;
        foreach ($kursus->moduls as $modul) {
            $totalMateris += $modul->materis->count();
        }

        $completedMateris = MateriProgress::where('user_id', $user->id)
            ->whereIn('materi_id', function ($q) use ($kursusId) {
                $q->select('materi.id')
                    ->from('materi')
                    ->join('modul', 'materi.modul_id', '=', 'modul.id')
                    ->where('modul.kursus_id', $kursusId);
            })
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalMateris > 0 ? round(($completedMateris / $totalMateris) * 100) : 0;
        if ($enrollment->status === 'selesai') {
            $progressPercentage = 100;
        }

        $allCompleted = ($progressPercentage >= 100 || ($totalMateris > 0 && $completedMateris >= $totalMateris));
        $sertifikat = Sertifikat::where('user_id', $user->id)->where('kursus_id', $kursusId)->first();

        $completedMateriIds = MateriProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('materi_id')
            ->toArray();

        return view('student.learn', compact(
            'kursus',
            'activeType',
            'activeMateri',
            'activeQuiz',
            'activeTugas',
            'bestAttempt',
            'tugasSubmission',
            'completedMateris',
            'totalMateris',
            'progressPercentage',
            'allCompleted',
            'sertifikat',
            'completedMateriIds'
        ));
    }

    /**
     * Tandai materi sebagai selesai dipelajari
     */
    public function markMaterialComplete(Request $request, $materiId)
    {
        $user = Auth::user();
        MateriProgress::updateOrCreate(
            ['user_id' => $user->id, 'materi_id' => $materiId],
            ['is_completed' => true]
        );

        return back()->with('success', 'Materi berhasil ditandai sebagai selesai!');
    }

    /**
     * Menerbitkan sertifikat kelulusan digital
     */
    public function issueCertificate($kursusId)
    {
        $user = Auth::user();

        $cert = Sertifikat::where('user_id', $user->id)->where('kursus_id', $kursusId)->first();
        if ($cert) {
            return redirect()->route('student.certificate.show', $cert->id);
        }

        $kursus = Kursus::findOrFail($kursusId);
        $certNumber = 'CERT-RM-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $verifyCode = 'VERIFY-RM-' . strtoupper(substr(md5(uniqid()), 0, 10));

        $cert = Sertifikat::create([
            'user_id' => $user->id,
            'kursus_id' => $kursus->id,
            'nomor_sertifikat' => $certNumber,
            'kode_verifikasi' => $verifyCode,
            'tanggal_terbit' => Carbon::now()->format('Y-m-d'),
            'nilai_akhir' => 90.00,
        ]);

        Enrollment::where('user_id', $user->id)->where('kursus_id', $kursusId)->update(['status' => 'selesai']);

        Notifikasi::create([
            'user_id' => $user->id,
            'judul' => 'Sertifikat Kelulusan Terbit!',
            'pesan' => 'Selamat! Sertifikat untuk ' . $kursus->nama . ' telah berhasil diterbitkan.',
            'link' => '/student/certificates/' . $cert->id,
        ]);

        return redirect()->route('student.certificate.show', $cert->id)->with('success', 'Selamat! Sertifikat Anda telah berhasil diterbitkan.');
    }

    /**
     * Menampilkan sertifikat kelulusan digital
     */
    public function showCertificate($id)
    {
        $sertifikat = Sertifikat::with(['user', 'kursus.instruktur'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && Auth::id() !== $sertifikat->user_id) {
            abort(403);
        }

        return view('student.certificate', compact('sertifikat'));
    }

    // ==========================================
    // 3. ADMIN & INSTRUKTUR MANAGEMENT (Boundary: kursus kelola)
    // ==========================================

    /**
     * Daftar kursus di panel admin/instruktur
     */
    public function adminIndex()
    {
        $user = Auth::user();
        if ($user->isInstruktur()) {
            $courses = Kursus::where('instruktur_id', $user->id)->with(['instruktur', 'moduls.materis', 'enrollments'])->latest()->get();
        } else {
            $courses = Kursus::with(['instruktur', 'moduls.materis', 'enrollments'])->latest()->get();
        }

        $instructors = User::whereIn('role', ['admin', 'instruktur'])->get();

        return view('admin.courses.index', compact('courses', 'instructors'));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'durasi' => 'required|string',
            'level' => 'required|in:Pemula,Menengah,Mahir',
            'instruktur_id' => 'required|exists:users,id',
            'thumbnail' => 'nullable|string',
            'status' => 'nullable|in:aktif,draft',
        ]);

        $validated['status'] = $validated['status'] ?? 'aktif';

        Kursus::create($validated);
        return back()->with('success', 'Kursus baru berhasil ditambahkan.');
    }

    public function updateCourse(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'durasi' => 'required|string',
            'level' => 'required|in:Pemula,Menengah,Mahir',
            'instruktur_id' => 'required|exists:users,id',
            'thumbnail' => 'nullable|string',
            'status' => 'required|in:aktif,draft',
        ]);

        $kursus = Kursus::findOrFail($id);
        $kursus->update($validated);

        return back()->with('success', 'Data kursus berhasil diperbarui.');
    }

    public function destroyCourse($id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->delete();

        return back()->with('success', 'Kursus berhasil dihapus beserta seluruh modul dan materinya.');
    }

    public function toggleCourseStatus($id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->status = $kursus->status === 'aktif' ? 'draft' : 'aktif';
        $kursus->save();

        return back()->with('success', 'Status publikasi kursus berhasil diubah menjadi: ' . strtoupper($kursus->status));
    }

    public function storeModule(Request $request, $kursusId)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        $validated['kursus_id'] = $kursusId;
        Modul::create($validated);

        return back()->with('success', 'Modul silabus berhasil ditambahkan.');
    }

    public function updateModule(Request $request, $id)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        $modul = Modul::findOrFail($id);
        $modul->update($validated);

        return back()->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroyModule($id)
    {
        $modul = Modul::findOrFail($id);
        $modul->delete();

        return back()->with('success', 'Modul silabus berhasil dihapus.');
    }

    public function storeMateri(Request $request, $modulId)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:teks,pdf,foto,video,link',
            'isi' => 'nullable|string',
            'video_url' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        $validated['modul_id'] = $modulId;
        Materi::create($validated);

        return back()->with('success', 'Materi pembelajaran berhasil ditambahkan.');
    }

    public function updateMateri(Request $request, $id)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:teks,pdf,foto,video,link',
            'isi' => 'nullable|string',
            'video_url' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        $materi = Materi::findOrFail($id);
        $materi->update($validated);

        return back()->with('success', 'Materi pembelajaran berhasil diperbarui.');
    }

    public function destroyMateri($id)
    {
        $materi = Materi::findOrFail($id);
        $materi->delete();

        return back()->with('success', 'Materi pembelajaran berhasil dihapus.');
    }
}
