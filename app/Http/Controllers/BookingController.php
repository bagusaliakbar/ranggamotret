<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Layanan;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Enrollment;
use App\Models\Notifikasi;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Customer Dashboard Portal
    public function customerDashboard()
    {
        $user = Auth::user();

        $totalBookings = Booking::where('user_id', $user->id)->count();
        $activeBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['Menunggu Konfirmasi', 'Disetujui', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal'])
            ->count();
        $completedBookings = Booking::where('user_id', $user->id)
            ->where('status', 'Selesai')
            ->count();
        $enrolledCourses = Enrollment::where('user_id', $user->id)->count();
        $pendingPayments = Pembayaran::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $recentBookings = Booking::where('user_id', $user->id)
            ->with(['layanan', 'paket', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        $enrolledList = Enrollment::where('user_id', $user->id)
            ->with(['kursus.instruktur'])
            ->latest()
            ->take(3)
            ->get();

        return view('customer.dashboard', compact(
            'totalBookings',
            'activeBookings',
            'completedBookings',
            'enrolledCourses',
            'pendingPayments',
            'recentBookings',
            'enrolledList'
        ));
    }

    // Customer Bookings List
    public function customerIndex()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['layanan', 'paket', 'pembayaran'])
            ->latest()
            ->get();

        return view('customer.bookings.index', compact('bookings'));
    }

    // Booking Creation Form
    public function create(Request $request)
    {
        $layananId = $request->query('layanan_id');
        $paketId = $request->query('paket_id');

        $layananList = Layanan::where('status', 'aktif')->with(['pakets' => fn($q) => $q->where('status', 'aktif')])->get();
        $selectedPaket = $paketId ? Paket::where('status', 'aktif')->find($paketId) : null;

        return view('customer.bookings.create', compact('layananList', 'layananId', 'selectedPaket'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'layanan_id' => 'required|exists:layanan,id,status,aktif',
            'paket_id' => 'required|exists:paket,id,status,aktif',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required|date_format:H:i',
            'lokasi' => 'required|string|max:255',
            'kebutuhan_khusus' => 'nullable|string',
        ], [
            'tanggal.after_or_equal' => 'Tanggal pemotretan tidak boleh tanggal yang sudah terlewat.',
            'waktu.required' => 'Waktu / jam sesi wajib diisi.',
            'waktu.date_format' => 'Format waktu tidak valid (gunakan format JJ:MM).',
        ]);

        // Validate that combined date and time is strictly in the future
        try {
            $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $validated['tanggal'] . ' ' . $validated['waktu']);
            if ($bookingDateTime->isPast()) {
                return back()->withInput()->withErrors([
                    'waktu' => 'Waktu sesi (' . $validated['waktu'] . ') pada tanggal ' . Carbon::parse($validated['tanggal'])->format('d/m/Y') . ' sudah terlewat. Silakan pilih waktu yang akan datang.'
                ]);
            }
        } catch (\Exception $e) {
            // Fallback parse
            $bookingDateTime = Carbon::parse($validated['tanggal'] . ' ' . $validated['waktu']);
            if ($bookingDateTime->isPast()) {
                return back()->withInput()->withErrors([
                    'waktu' => 'Waktu sesi yang dipilih sudah terlewat. Silakan pilih waktu yang akan datang.'
                ]);
            }
        }

        $bookingNumber = 'BK-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $booking = Booking::create([
            'nomor_booking' => $bookingNumber,
            'user_id' => Auth::id(),
            'layanan_id' => $validated['layanan_id'],
            'paket_id' => $validated['paket_id'],
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'lokasi' => $validated['lokasi'],
            'kebutuhan_khusus' => $validated['kebutuhan_khusus'],
            'status' => 'Menunggu Konfirmasi',
        ]);

        // Send notification to Admin
        Notifikasi::create([
            'user_id' => 1, // Admin
            'judul' => 'Booking Fotografi Baru',
            'pesan' => 'Booking baru ' . $bookingNumber . ' dari ' . Auth::user()->name,
            'link' => '/admin/bookings/' . $booking->id,
        ]);

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'Booking berhasil dikirim! Menunggu konfirmasi dari tim RanggaMotret.');
    }

    public function customerShow($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['layanan', 'paket', 'pembayaran.invoice', 'galeriFotos', 'testimoni'])
            ->findOrFail($id);

        return view('customer.bookings.show', compact('booking'));
    }

    // Interactive Calendar Schedule
    public function calendar()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $bookings = Booking::with(['customer', 'layanan'])->get();
        } else {
            $bookings = Booking::where('user_id', $user->id)->with('layanan')->get();
        }

        $events = $bookings->map(function ($b) {
            $color = match ($b->status) {
                'Disetujui', 'Terjadwal' => '#3b82f6',
                'Dibayar' => '#10b981',
                'Selesai' => '#6b7280',
                'Dibatalkan', 'Ditolak' => '#ef4444',
                default => '#f59e0b',
            };

            return [
                'id' => $b->id,
                'title' => $b->nomor_booking . ' - ' . $b->layanan->nama . ' (' . $b->status . ')',
                'start' => $b->tanggal . 'T' . $b->waktu,
                'color' => $color,
                'url' => Auth::user()->isAdmin() ? route('admin.bookings.show', $b->id) : route('customer.bookings.show', $b->id),
            ];
        });

        return view('calendar.index', compact('events'));
    }

    // Admin Booking Management
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $query = Booking::with(['customer', 'layanan', 'paket', 'pembayaran']);

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate(15);
        $statuses = ['Menunggu Konfirmasi', 'Disetujui', 'Ditolak', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal', 'Selesai', 'Dibatalkan'];

        return view('admin.bookings.index', compact('bookings', 'statuses', 'status'));
    }

    public function adminShow($id)
    {
        $booking = Booking::with(['customer', 'layanan', 'paket', 'pembayaran.invoice', 'galeriFotos'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        // Send Notification to Customer
        Notifikasi::create([
            'user_id' => $booking->user_id,
            'judul' => 'Status Booking Diperbarui',
            'pesan' => 'Status booking ' . $booking->nomor_booking . ' diubah menjadi: ' . $booking->status,
            'link' => '/customer/bookings/' . $booking->id,
        ]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
