<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\Kursus;
use App\Models\User;
use App\Models\Konsultasi;
use App\Models\Testimoni;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalBooking = Booking::count();
        $bookingPending = Booking::where('status', 'Menunggu Konfirmasi')->count();
        $totalPendapatan = Pembayaran::where('status', 'Dibayar')->sum('jumlah');
        $pembayaranPending = Pembayaran::where('status', 'Menunggu Verifikasi')->count();
        $totalKursus = Kursus::count();
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingConsultations = Konsultasi::where('status', 'pending')->count();
        $pendingTestimonials = Testimoni::where('status', 'pending')->count();

        $recentBookings = Booking::with(['customer', 'layanan'])->latest()->take(5)->get();
        $recentPayments = Pembayaran::with('user')->where('status', 'Menunggu Verifikasi')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBooking', 'bookingPending', 'totalPendapatan', 'pembayaranPending',
            'totalKursus', 'totalPeserta', 'totalCustomers', 'pendingConsultations',
            'pendingTestimonials', 'recentBookings', 'recentPayments'
        ));
    }
}
