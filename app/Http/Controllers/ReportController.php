<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\Layanan;
use App\Models\Kursus;
use App\Models\Enrollment;
use App\Models\Sertifikat;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'services'); // services, financial, courses

        // 1. Photography Services Report
        $totalBookings = Booking::count();
        $totalBookingDone = Booking::where('status', 'Selesai')->count();
        $bookingsByStatus = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $popularServices = Layanan::withCount('bookings')->orderByDesc('bookings_count')->get();

        // 2. Financial Report
        $totalPendapatan = Pembayaran::where('status', 'Dibayar')->sum('jumlah');
        $pendapatanLayanan = Pembayaran::where('status', 'Dibayar')->whereNotNull('booking_id')->sum('jumlah');
        $pendapatanKursus = Pembayaran::where('status', 'Dibayar')->whereNotNull('kursus_id')->sum('jumlah');
        $unpaidAmount = Pembayaran::whereIn('status', ['Belum Dibayar', 'Menunggu Verifikasi'])->sum('jumlah');

        $monthlyRevenue = Pembayaran::where('status', 'Dibayar')
            ->select(DB::raw('DATE_FORMAT(tanggal_pembayaran, "%Y-%m") as month'), DB::raw('SUM(jumlah) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // 3. Courses Report
        $totalEnrollments = Enrollment::count();
        $activeStudents = Enrollment::where('status', 'aktif')->count();
        $totalPesertaLulus = Enrollment::where('status', 'selesai')->count();
        $popularCourses = Kursus::withCount('enrollments')->orderByDesc('enrollments_count')->get();

        $avgQuizScore = QuizAttempt::avg('nilai') ?? 0;
        $passCount = QuizAttempt::where('is_passed', true)->count();
        $totalAttempts = QuizAttempt::count();
        $passRate = $totalAttempts > 0 ? round(($passCount / $totalAttempts) * 100, 1) : 0;

        return view('admin.reports.index', compact(
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
