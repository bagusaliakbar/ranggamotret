<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\Kursus;
use App\Models\User;
use App\Models\Konsultasi;

/**
 * Controller Dasbor Terpadu (Unified Adaptive Dashboard)
 * 
 * Merepresentasikan Control Object 'DashboardController' pada arsitektur sistem.
 * Bertindak sebagai pintu masuk tunggal (/dashboard) yang secara dinamis
 * menyajikan ringkasan & widget sesuai hak akses / peran (Role-Based Access Control).
 */
class DashboardController extends Controller
{
    /**
     * Entry point tunggal dasbor (/dashboard)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        }

        return $this->customerDashboard($request);
    }

    /**
     * Tampilan Dasbor Peran Admin
     */
    protected function adminDashboard(Request $request)
    {
        $totalBooking = Reservasi::where('tipe', 'fotografi')->count();
        $bookingPending = Reservasi::where('tipe', 'fotografi')->where('status', 'Menunggu Konfirmasi')->count();
        $totalPendapatan = Pembayaran::where('status', 'Dibayar')->sum('jumlah');
        $pembayaranPending = Pembayaran::whereIn('status', ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'pending', 'Belum Dibayar'])->count();
        $totalKursus = Kursus::count();
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingConsultations = Konsultasi::where('status', 'pending')->count();

        $recentBookings = Reservasi::where('tipe', 'fotografi')->with(['customer', 'layanan'])->latest()->take(5)->get();
        $recentPayments = Pembayaran::with(['user', 'reservasi.layanan', 'kursus'])->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalBooking',
            'bookingPending',
            'totalPendapatan',
            'pembayaranPending',
            'totalKursus',
            'totalPeserta',
            'totalCustomers',
            'pendingConsultations',
            'recentBookings',
            'recentPayments'
        ));
    }


    /**
     * Tampilan Dasbor Peran Pelanggan / Peserta
     */
    protected function customerDashboard(Request $request)
    {
        $user = Auth::user();

        $totalBookings = Reservasi::where('user_id', $user->id)->where('tipe', 'fotografi')->count();
        $activeBookings = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'fotografi')
            ->whereIn('status', ['Menunggu Konfirmasi', 'Disetujui', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal'])
            ->count();
        $completedBookings = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'fotografi')
            ->where('status', 'Selesai')
            ->count();
        $enrolledCourses = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'kursus')
            ->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])
            ->count();
        $pendingPayments = Pembayaran::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $recentBookings = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'fotografi')
            ->with(['layanan', 'paket', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        $enrolledList = Reservasi::where('user_id', $user->id)
            ->where('tipe', 'kursus')
            ->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])
            ->with(['kursus.instruktur'])
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.customer', compact(
            'totalBookings',
            'activeBookings',
            'completedBookings',
            'enrolledCourses',
            'pendingPayments',
            'recentBookings',
            'enrolledList'
        ));
    }
}
