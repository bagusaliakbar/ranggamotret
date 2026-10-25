<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kursus;
use App\Models\Modul;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Reservasi;
use App\Models\Sertifikat;
use App\Models\Quiz;
use App\Models\QuizSoal;
use App\Models\QuizAttempt;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Pembayaran;
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
        return view('kursus.index', compact('kursus', 'level'));
    }

    /**
     * Menampilkan detail kursus dan silabus
     */
    public function detail($id)
    {
        $kursus = Kursus::with(['instruktur', 'moduls.materis'])->findOrFail($id);
        $isEnrolled = Auth::check() && Reservasi::where('user_id', Auth::id())->where('kursus_id', $id)->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])->exists();

        return view('kursus.detail', compact('kursus', 'isEnrolled'));
    }

    // ==========================================
    // 2. STUDENT LMS CLASSROOM (Boundary: ruang belajar siswa)
    // ==========================================

    /**
     * Menampilkan daftar kursus yang diikuti siswa / pelanggan
     */
    public function studentIndex()
    {
        $user = Auth::user();

        // 1. Sinkronisasi Otomatis: Jika ada pembayaran kursus 'Dibayar' tapi belum tercatat di reservasi
        $paidCoursePayments = Pembayaran::where('user_id', $user->id)
            ->whereNotNull('kursus_id')
            ->where('status', 'Dibayar')
            ->get();

        foreach ($paidCoursePayments as $pay) {
            $reservasi = Reservasi::firstOrCreate([
                'user_id' => $user->id,
                'kursus_id' => $pay->kursus_id,
                'tipe' => 'kursus',
            ], [
                'nomor_reservasi' => 'RSV-KRS-' . date('Ymd') . '-' . str_pad($pay->id, 4, '0', STR_PAD_LEFT),
                'tanggal' => date('Y-m-d', strtotime($pay->tanggal_pembayaran ?? now())),
                'waktu' => date('H:i:s', strtotime($pay->tanggal_pembayaran ?? now())),
                'lokasi' => 'Online Classroom LMS',
                'kebutuhan_khusus' => 'Pendaftaran Kursus Akademi',
                'status' => 'Dibayar',
            ]);

            if (empty($pay->reservasi_id)) {
                $pay->reservasi_id = $reservasi->id;
                $pay->save();
            }
        }

        $enrollments = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'kursus')
            ->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])
            ->with(['kursus.instruktur', 'kursus.moduls.materis', 'pembayaran.invoice'])
            ->latest()
            ->get();

        foreach ($enrollments as $enr) {
            $enr->progress_persen = $enr->kursus ? $enr->kursus->progressOfUser($user->id) : 0;
            $enr->sertifikat = Sertifikat::where('user_id', $user->id)->where('kursus_id', $enr->kursus_id)->first();
        }

        $certificates = Sertifikat::where('user_id', $user->id)->with('kursus')->latest()->get();

        $totalEnrolled = $enrollments->count();
        $completedCourses = $enrollments->filter(fn($e) => $e->progress_persen >= 100)->count();
        $inProgressCourses = $enrollments->filter(fn($e) => $e->progress_persen < 100)->count();
        $totalCertificates = $certificates->count();

        // Ambil data transaksi kursus yang pembayarannya belum selesai
        $pendingCoursePayments = Pembayaran::where('user_id', $user->id)
            ->whereNotNull('kursus_id')
            ->whereIn('status', ['Belum Dibayar', 'Menunggu Pembayaran', 'Menunggu Verifikasi', 'pending'])
            ->with(['kursus.instruktur'])
            ->latest()
            ->get();

        return view('dashboard.student', compact(
            'enrollments', 
            'certificates',
            'totalEnrolled',
            'completedCourses',
            'inProgressCourses',
            'totalCertificates',
            'pendingCoursePayments'
        ));
    }

    /**
     * Menampilkan riwayat pemesanan / reservasi kursus oleh pelanggan
     */
    public function customerCourseReservations(Request $request)
    {
        $user = Auth::user();

        // Data reservasi kursus beserta pembayaran & invoice
        $enrollments = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'kursus')
            ->with(['kursus.instruktur', 'pembayaran.invoice'])
            ->latest()
            ->get();

        foreach ($enrollments as $enr) {
            $enr->sertifikat = Sertifikat::where('user_id', $user->id)->where('kursus_id', $enr->kursus_id)->first();
        }

        // Data transaksi pembayaran kursus
        $coursePayments = Pembayaran::where('user_id', $user->id)
            ->whereNotNull('kursus_id')
            ->with(['kursus.instruktur', 'invoice'])
            ->latest()
            ->get();

        $totalReservasi = $enrollments->count();
        $totalInvestasi = $coursePayments->where('status', 'Dibayar')->sum('jumlah');
        if ($totalInvestasi == 0 && $enrollments->count() > 0) {
            $totalInvestasi = $enrollments->sum(fn($e) => $e->kursus->harga ?? 0);
        }

        return view('kursus.reservasi', compact(
            'enrollments',
            'coursePayments',
            'totalReservasi',
            'totalInvestasi'
        ));
    }

    /**
     * Ruang belajar interaktif siswa (LMS Player)
     */
    public function learn($kursusId, Request $request)
    {
        $user = Auth::user();

        // Pastikan pengguna terdaftar melalui reservasi kursus
        $enrollment = Reservasi::where('user_id', $user->id)
            ->where('kursus_id', $kursusId)
            ->where('tipe', 'kursus')
            ->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])
            ->first();

        // Jika admin, izinkan akses preview belajar
        if (!$enrollment && $user->isAdmin()) {
            $enrollment = (object)[
                'status' => 'aktif',
                'created_at' => now(),
            ];
        } elseif (!$enrollment) {
            abort(403, 'Anda belum memiliki akses atau pembayaran belum dikonfirmasi untuk kursus ini.');
        }

        $kursus = Kursus::with([
            'moduls.materis',
            'moduls.quizs.attempts' => fn($q) => $q->where('user_id', $user->id),
        ])->findOrFail($kursusId);

        $materiId = $request->query('materi_id');
        $quizId = $request->query('quiz_id');

        $activeType = 'materi';
        $activeMateri = null;
        $activeQuiz = null;
        $bestAttempt = null;

        if ($quizId) {
            $activeQuiz = Quiz::with(['soals', 'modul'])->findOrFail($quizId);
            $bestAttempt = $activeQuiz->bestAttemptOf($user->id);
            $activeType = 'quiz';
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
        if (strtolower($enrollment->status ?? '') === 'selesai') {
            $progressPercentage = 100;
        }

        $allCompleted = ($progressPercentage >= 100 || ($totalMateris > 0 && $completedMateris >= $totalMateris));
        $sertifikat = Sertifikat::where('user_id', $user->id)->where('kursus_id', $kursusId)->first();

        $completedMateriIds = MateriProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('materi_id')
            ->toArray();

        return view('materi.learn', compact(
            'kursus',
            'activeType',
            'activeMateri',
            'activeQuiz',
            'bestAttempt',
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

        Reservasi::where('user_id', $user->id)
            ->where('kursus_id', $kursusId)
            ->where('tipe', 'kursus')
            ->update(['status' => 'Selesai']);

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

        return view('kursus.sertifikat', compact('sertifikat'));
    }

    // ==========================================
    // 3. ADMIN & INSTRUKTUR MANAGEMENT (Boundary: kursus kelola)
    // ==========================================

    /**
     * Menampilkan daftar seluruh reservasi / pendaftaran kursus bagi Owner
     */
    public function adminReservations(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $kursusId = $request->query('kursus_id');

        $query = Pembayaran::whereNotNull('kursus_id')
            ->with(['user', 'kursus.instruktur', 'invoice']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kursus', function ($kq) use ($search) {
                      $kq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($kursusId) {
            $query->where('kursus_id', $kursusId);
        }

        $reservations = $query->latest('id')->paginate(12)->withQueryString();

        // Metrik statistik pendaftaran kursus
        $totalReservations = Pembayaran::whereNotNull('kursus_id')->count();
        $totalLunas = Pembayaran::whereNotNull('kursus_id')->where('status', 'Dibayar')->count();
        $totalMenunggu = Pembayaran::whereNotNull('kursus_id')->whereIn('status', ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'pending'])->count();
        $totalPendapatanKursus = Pembayaran::whereNotNull('kursus_id')->where('status', 'Dibayar')->sum('jumlah');

        $allCourses = Kursus::orderBy('nama')->get();

        return view('kursus.admin-reservasi', compact(
            'reservations',
            'allCourses',
            'search',
            'status',
            'kursusId',
            'totalReservations',
            'totalLunas',
            'totalMenunggu',
            'totalPendapatanKursus'
        ));
    }

    /**
     * Memperbarui status reservasi / pembayaran kursus oleh Owner
     */
    public function adminUpdateReservationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Dibayar,Menunggu Pembayaran,Menunggu Verifikasi,Belum Dibayar,Ditolak,Dibatalkan',
        ]);

        $pembayaran = Pembayaran::whereNotNull('kursus_id')->findOrFail($id);
        $oldStatus = $pembayaran->status;
        $newStatus = $request->status;

        $pembayaran->status = $newStatus;
        if ($newStatus === 'Dibayar') {
            if (empty($pembayaran->tanggal_pembayaran)) {
                $pembayaran->tanggal_pembayaran = now();
            }

            // 1. Aktivasi Akses Kursus (Buat/Update Reservasi Kursus)
            $reservasiKursus = Reservasi::firstOrCreate([
                'user_id' => $pembayaran->user_id,
                'kursus_id' => $pembayaran->kursus_id,
                'tipe' => 'kursus',
            ], [
                'nomor_reservasi' => 'RSV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5)),
                'status' => 'Dibayar',
                'tanggal' => $pembayaran->tanggal_pembayaran ? Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'lokasi' => 'Online Classroom LMS',
            ]);

            if (empty($pembayaran->reservasi_id)) {
                $pembayaran->reservasi_id = $reservasiKursus->id;
            }

            // 2. Terbitkan Invoice Resmi LUNAS jika belum ada
            $invNumber = 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5));
            \App\Models\Invoice::firstOrCreate(
                ['pembayaran_id' => $pembayaran->id],
                [
                    'nomor_invoice' => $invNumber,
                    'user_id' => $pembayaran->user_id,
                    'reservasi_id' => $reservasiKursus->id,
                    'kursus_id' => $pembayaran->kursus_id,
                    'subtotal' => $pembayaran->jumlah,
                    'diskon' => 0,
                    'total' => $pembayaran->jumlah,
                    'status_pembayaran' => 'LUNAS',
                ]
            );
        }
        $pembayaran->save();

        // Notifikasi ke peserta
        Notifikasi::create([
            'user_id' => $pembayaran->user_id,
            'judul' => 'Status Reservasi Kursus Diperbarui',
            'pesan' => "Status pendaftaran kursus {$pembayaran->kursus->nama} ({$pembayaran->nomor_transaksi}) kini: {$newStatus}.",
            'link' => route('customer.courses.reservations'),
        ]);

        return back()->with('success', "Status reservasi kursus {$pembayaran->nomor_transaksi} berhasil diubah menjadi: {$newStatus}.");
    }

    /**
     * Daftar kursus di panel admin/instruktur
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $level = $request->query('level');
        $status = $request->query('status');

        $query = Kursus::with(['instruktur', 'moduls.materis', 'moduls.quizs.soals', 'reservasis']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($level && $level !== 'all') {
            $query->where('level', $level);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $courses = $query->latest()->get();
        $instructors = User::where('role', 'admin')->get();

        // Metrik statistik kursus
        $totalCourses = Kursus::count();
        $activeCourses = Kursus::where('status', 'aktif')->count();
        $totalEnrollments = Reservasi::where('tipe', 'kursus')->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])->count();
        $totalModules = \App\Models\Modul::count();

        return view('kursus.admin', compact(
            'courses',
            'instructors',
            'search',
            'level',
            'status',
            'totalCourses',
            'activeCourses',
            'totalEnrollments',
            'totalModules'
        ));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'durasi' => 'required|string',
            'level' => 'required|in:Pemula,Menengah,Mahir',
            'instruktur_id' => 'nullable|exists:users,id',
            'thumbnail' => 'nullable|string',
            'status' => 'nullable|in:aktif,draft',
        ]);

        $validated['instruktur_id'] = $validated['instruktur_id'] ?? Auth::id();
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
            'instruktur_id' => 'nullable|exists:users,id',
            'thumbnail' => 'nullable|string',
            'status' => 'required|in:aktif,draft',
        ]);

        $validated['instruktur_id'] = $validated['instruktur_id'] ?? Auth::id();

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

    /**
     * Menyimpan Kuis Evaluasi beserta pertanyaan pilihan ganda (mendukung multiple butir soal)
     */
    public function storeQuiz(Request $request, $modulId)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'passing_grade' => 'required|numeric|min:0|max:100',
            // Mendukung array butir soal (multiple questions)
            'soal' => 'nullable|array|min:1',
            'soal.*.pertanyaan' => 'required_with:soal|string',
            'soal.*.pilihan_a' => 'required_with:soal|string',
            'soal.*.pilihan_b' => 'required_with:soal|string',
            'soal.*.pilihan_c' => 'required_with:soal|string',
            'soal.*.pilihan_d' => 'required_with:soal|string',
            'soal.*.jawaban_benar' => 'required_with:soal|in:A,B,C,D',
            'soal.*.bobot_nilai' => 'nullable|numeric|min:1',
            'soal.*.pembahasan' => 'nullable|string',
            // Fallback backward-compatible untuk single question format lama
            'pertanyaan' => 'nullable|string',
            'pilihan_a' => 'nullable|string',
            'pilihan_b' => 'nullable|string',
            'pilihan_c' => 'nullable|string',
            'pilihan_d' => 'nullable|string',
            'jawaban_benar' => 'nullable|in:A,B,C,D',
            'bobot_nilai' => 'nullable|numeric|min:1',
            'pembahasan' => 'nullable|string',
        ]);

        $quiz = Quiz::create([
            'modul_id' => $modulId,
            'judul' => $validated['judul'],
            'durasi_menit' => $validated['durasi_menit'],
            'passing_grade' => $validated['passing_grade'],
        ]);

        // Simpan multiple soal jika tersedia
        if (!empty($validated['soal']) && is_array($validated['soal'])) {
            foreach ($validated['soal'] as $item) {
                if (!empty($item['pertanyaan'])) {
                    QuizSoal::create([
                        'quiz_id' => $quiz->id,
                        'pertanyaan' => $item['pertanyaan'],
                        'pilihan_a' => $item['pilihan_a'] ?? '',
                        'pilihan_b' => $item['pilihan_b'] ?? '',
                        'pilihan_c' => $item['pilihan_c'] ?? '',
                        'pilihan_d' => $item['pilihan_d'] ?? '',
                        'jawaban_benar' => $item['jawaban_benar'] ?? 'A',
                        'bobot_nilai' => $item['bobot_nilai'] ?? 10,
                        'pembahasan' => $item['pembahasan'] ?? null,
                    ]);
                }
            }
        } elseif (!empty($validated['pertanyaan'])) {
            // Fallback single question
            QuizSoal::create([
                'quiz_id' => $quiz->id,
                'pertanyaan' => $validated['pertanyaan'],
                'pilihan_a' => $validated['pilihan_a'] ?? '',
                'pilihan_b' => $validated['pilihan_b'] ?? '',
                'pilihan_c' => $validated['pilihan_c'] ?? '',
                'pilihan_d' => $validated['pilihan_d'] ?? '',
                'jawaban_benar' => $validated['jawaban_benar'] ?? 'A',
                'bobot_nilai' => $validated['bobot_nilai'] ?? 10,
                'pembahasan' => $validated['pembahasan'] ?? null,
            ]);
        }

        return back()->with('success', 'Kuis evaluasi beserta butir pertanyaan berhasil ditambahkan ke modul silabus.');
    }

    /**
     * Menghapus Kuis Evaluasi beserta butir soalnya
     */
    public function destroyQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->soals()->delete();
        $quiz->attempts()->delete();
        $quiz->delete();

        return back()->with('success', 'Kuis evaluasi berhasil dihapus.');
    }
}
