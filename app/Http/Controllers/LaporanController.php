<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\Katalog;
use App\Models\Kursus;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

/**
 * Controller Manajemen Laporan Eksekutif (ICONIX Robustness: LaporanController)
 * 
 * Bertanggung jawab atas:
 * - Laporan kinerja operasional sesi pemotretan & status reservasi
 * - Laporan keuangan & omzet pendapatan Midtrans / transfer bank
 * - Laporan progres akademi, kelulusan siswa, & tingkat keberhasilan kuis
 */
class LaporanController extends Controller
{
    /**
     * Menampilkan laporan ringkasan bisnis terpadu
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'services'); // services, financial, courses

        // 1. Laporan Layanan & Reservasi Fotografi
        $totalBookings = Reservasi::count();
        $totalBookingDone = Reservasi::where('status', 'Selesai')->count();
        $bookingsByStatus = Reservasi::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $popularServices = Katalog::withCount('bookings')->orderByDesc('bookings_count')->get();

        // 2. Laporan Keuangan & Omzet
        $totalPendapatan = Pembayaran::where('status', 'Dibayar')->sum('jumlah');
        $pendapatanLayanan = Pembayaran::where('status', 'Dibayar')->whereNotNull('booking_id')->sum('jumlah');
        $pendapatanKursus = Pembayaran::where('status', 'Dibayar')->whereNotNull('kursus_id')->sum('jumlah');
        $unpaidAmount = Pembayaran::whereIn('status', ['Belum Dibayar', 'Menunggu Verifikasi'])->sum('jumlah');

        $monthlyRevenue = Pembayaran::where('status', 'Dibayar')
            ->select(DB::raw('DATE_FORMAT(tanggal_pembayaran, "%Y-%m") as month'), DB::raw('SUM(jumlah) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // 3. Laporan Akademi & Kursus
        $totalEnrollments = Enrollment::count();
        $activeStudents = Enrollment::where('status', 'aktif')->count();
        $totalPesertaLulus = Enrollment::where('status', 'selesai')->count();
        $popularCourses = Kursus::withCount('enrollments')->orderByDesc('enrollments_count')->get();

        $avgQuizScore = QuizAttempt::avg('nilai') ?? 0;
        $passCount = QuizAttempt::where('is_passed', true)->count();
        $totalAttempts = QuizAttempt::count();
        $passRate = $totalAttempts > 0 ? round(($passCount / $totalAttempts) * 100, 1) : 0;

        return view('laporan.index', compact(
            'type',
            'totalBookings',
            'totalBookingDone',
            'bookingsByStatus',
            'popularServices',
            'totalPendapatan',
            'pendapatanLayanan',
            'pendapatanKursus',
            'unpaidAmount',
            'monthlyRevenue',
            'totalEnrollments',
            'activeStudents',
            'totalPesertaLulus',
            'popularCourses',
            'avgQuizScore',
            'passRate'
        ));
    }
}
