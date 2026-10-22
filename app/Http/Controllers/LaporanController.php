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
        return redirect()->route('admin.reports.pendapatan');
    }

    /**
     * Submenu 1: Laporan Pendapatan (Keuangan Studio & Kursus)
     */
    public function pendapatan(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $kategori = $request->query('kategori'); // all, foto, kursus
        $metode = $request->query('metode');

        $query = Pembayaran::with(['user', 'booking.layanan', 'booking.paket', 'kursus', 'invoice'])
            ->where('status', 'Dibayar');

        if ($startDate) {
            $query->whereDate('tanggal_pembayaran', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_pembayaran', '<=', $endDate);
        }

        if ($kategori === 'foto') {
            $query->whereNotNull('booking_id');
        } elseif ($kategori === 'kursus') {
            $query->whereNotNull('kursus_id');
        }

        if ($metode) {
            if ($metode === 'gateway') {
                $query->where(function($q) {
                    $q->where('metode_pembayaran', 'like', '%Midtrans%')
                      ->orWhereNotNull('snap_token');
                });
            } elseif ($metode === 'manual') {
                $query->where(function($q) {
                    $q->where('metode_pembayaran', 'not like', '%Midtrans%')
                      ->whereNull('snap_token');
                });
            }
        }

        $pembayarans = (clone $query)->latest('tanggal_pembayaran')->latest('id')->paginate(15)->withQueryString();

        // Ringkasan Statistik Pendapatan pada periode terpilih
        $baseQuery = Pembayaran::where('status', 'Dibayar')
            ->whereDate('tanggal_pembayaran', '>=', $startDate)
            ->whereDate('tanggal_pembayaran', '<=', $endDate);

        $totalPendapatan = (clone $baseQuery)->sum('jumlah');
        $pendapatanFoto = (clone $baseQuery)->whereNotNull('booking_id')->sum('jumlah');
        $pendapatanKursus = (clone $baseQuery)->whereNotNull('kursus_id')->sum('jumlah');
        $transaksiCount = (clone $baseQuery)->count();

        // Tren Bulanan Pendapatan
        $monthlyRevenue = Pembayaran::where('status', 'Dibayar')
            ->select(DB::raw('DATE_FORMAT(tanggal_pembayaran, "%Y-%m") as month'), DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        return view('laporan.pendapatan', compact(
            'pembayarans',
            'startDate',
            'endDate',
            'kategori',
            'metode',
            'totalPendapatan',
            'pendapatanFoto',
            'pendapatanKursus',
            'transaksiCount',
            'monthlyRevenue'
        ));
    }

    /**
     * Submenu 2: Laporan Reservasi Fotografi dan Kursus
     */
    public function reservasi(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $tipe = $request->query('tipe', 'semua'); // semua, foto, kursus
        $status = $request->query('status');

        // 1. Data Reservasi Fotografi
        $queryFoto = Reservasi::with(['customer', 'layanan', 'paket', 'pembayaran'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            $queryFoto->where('status', $status);
        }

        $fotoBookings = $queryFoto->latest()->get();

        // 2. Data Reservasi Kursus (dari tabel Pembayaran yang memiliki kursus_id)
        $queryKursus = Pembayaran::whereNotNull('kursus_id')
            ->with(['user', 'kursus.instruktur', 'invoice'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            $queryKursus->where('status', $status);
        }

        $kursusBookings = $queryKursus->latest()->get();

        // Gabungkan dalam format koleksi terpadu untuk tabel laporan
        $combined = collect();

        if ($tipe === 'semua' || $tipe === 'foto') {
            foreach ($fotoBookings as $fb) {
                $combined->push((object)[
                    'id' => $fb->id,
                    'kode' => $fb->nomor_booking,
                    'tipe' => 'Fotografi',
                    'nama_pelanggan' => $fb->customer->name ?? 'Tamu',
                    'kontak' => $fb->customer->phone ?? '-',
                    'item' => ($fb->layanan->nama ?? 'Sesi Foto') . ' (' . ($fb->paket->nama_paket ?? '-') . ')',
                    'jadwal_event' => date('d M Y', strtotime($fb->tanggal)) . ' ' . substr($fb->waktu, 0, 5) . ' WIB',
                    'biaya' => $fb->paket->harga ?? ($fb->pembayaran->jumlah ?? 0),
                    'status' => $fb->status,
                    'tanggal_daftar' => $fb->created_at,
                    'detail_url' => route('admin.bookings.show', $fb->id),
                ]);
            }
        }

        if ($tipe === 'semua' || $tipe === 'kursus') {
            foreach ($kursusBookings as $kb) {
                $combined->push((object)[
                    'id' => $kb->id,
                    'kode' => $kb->nomor_transaksi,
                    'tipe' => 'Kursus',
                    'nama_pelanggan' => $kb->user->name ?? 'Siswa',
                    'kontak' => $kb->user->phone ?? ($kb->user->email ?? '-'),
                    'item' => $kb->kursus->nama ?? 'Program Pelatihan',
                    'jadwal_event' => 'Akademi LMS (Online)',
                    'biaya' => $kb->jumlah,
                    'status' => $kb->status,
                    'tanggal_daftar' => $kb->created_at,
                    'detail_url' => route('admin.courses.reservations.index', ['search' => $kb->nomor_transaksi]),
                ]);
            }
        }

        // Urutkan berdasarkan tanggal pendaftaran terbaru
        $allReservations = $combined->sortByDesc('tanggal_daftar')->values();

        // Metrik Statistik
        $totalFotoCount = $fotoBookings->count();
        $totalKursusCount = $kursusBookings->count();
        $totalReservasiCount = $totalFotoCount + $totalKursusCount;

        $totalSelesaiFoto = $fotoBookings->whereIn('status', ['Selesai', 'Terjadwal', 'Dibayar', 'Disetujui'])->count();
        $totalLunasKursus = $kursusBookings->where('status', 'Dibayar')->count();

        return view('laporan.reservasi', compact(
            'allReservations',
            'startDate',
            'endDate',
            'tipe',
            'status',
            'totalReservasiCount',
            'totalFotoCount',
            'totalKursusCount',
            'totalSelesaiFoto',
            'totalLunasKursus'
        ));
    }
}
