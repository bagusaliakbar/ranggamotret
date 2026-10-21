<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use App\Models\Booking;
use App\Models\Kursus;
use App\Models\Invoice;
use App\Models\Enrollment;
use App\Models\Notifikasi;
use App\Services\MidtransService;
use Carbon\Carbon;

class PaymentController extends Controller
{
    // Show Payment Form (Supports both Midtrans Snap & Manual Transfer)
    public function create(Request $request, MidtransService $midtransService)
    {
        $bookingId = $request->query('booking_id');
        $kursusId = $request->query('kursus_id');

        $booking = $bookingId ? Booking::with(['layanan', 'paket', 'customer'])->where('user_id', Auth::id())->find($bookingId) : null;
        $kursus = $kursusId ? Kursus::find($kursusId) : null;

        $snapToken = null;
        $snapError = null;

        if ($booking) {
            try {
                $snapToken = $midtransService->getSnapTokenForBooking($booking);
            } catch (\Exception $e) {
                $snapError = $e->getMessage();
            }
        } elseif ($kursus) {
            try {
                $snapToken = $midtransService->getSnapTokenForKursus($kursus, Auth::user());
            } catch (\Exception $e) {
                $snapError = $e->getMessage();
            }
        }

        $midtransClientKey = config('midtrans.client_key');
        $midtransSnapUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';

        return view('payments.create', compact('booking', 'kursus', 'snapToken', 'snapError', 'midtransClientKey', 'midtransSnapUrl'));
    }

    // Midtrans Webhook Notification Callback (No CSRF)
    public function handleCallback(Request $request, MidtransService $midtransService)
    {
        $payload = $request->all();
        $result = $midtransService->handleNotification($payload);

        return response()->json($result);
    }

    // Midtrans Return URL Handler
    public function paymentFinish(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionStatus = $request->query('transaction_status');

        if ($orderId) {
            // Handle Course return redirect
            if (str_starts_with($orderId, 'KRS-')) {
                $parts = explode('-', $orderId);
                $kursusId = $parts[1] ?? null;
                $userId = $parts[2] ?? Auth::id();

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    if ($kursusId && $userId) {
                        Enrollment::firstOrCreate(
                            ['user_id' => $userId, 'kursus_id' => $kursusId],
                            ['status' => 'aktif', 'progress_persen' => 0]
                        );
                    }
                    return redirect()->route('student.dashboard')
                        ->with('success', 'Pembayaran kursus berhasil! Selamat belajar di ruang kelas akademi RanggaMotret.');
                }
                return redirect()->route('student.dashboard')
                    ->with('info', 'Status transaksi kursus Anda sedang diproses.');
            }

            $baseBookingNumber = explode('-T', $orderId)[0];
            $booking = Booking::where('nomor_booking', $orderId)
                ->orWhere('nomor_booking', $baseBookingNumber)
                ->first();
            if ($booking) {
                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    $booking->status = 'Dibayar';
                    $booking->save();
                    return redirect()->route('customer.bookings.show', $booking->id)
                        ->with('success', 'Pembayaran berhasil dikonfirmasi! Jadwal sesi pemotretan Anda telah terverifikasi.');
                } elseif ($transactionStatus === 'pending') {
                    return redirect()->route('customer.bookings.show', $booking->id)
                        ->with('info', 'Pembayaran Anda sedang menunggu penyelesaian.');
                }
                return redirect()->route('customer.bookings.show', $booking->id);
            }
        }

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Terima kasih, data pembayaran Anda sedang diproses.');
    }

    // Submit Payment Proof
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:booking,id',
            'kursus_id' => 'nullable|exists:kursus,id',
            'jumlah' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $trxNumber = 'TRX-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/pembayaran'), $filename);
            $buktiPath = 'uploads/pembayaran/' . $filename;
        }

        $pembayaran = Pembayaran::create([
            'nomor_transaksi' => $trxNumber,
            'user_id' => Auth::id(),
            'booking_id' => $validated['booking_id'] ?? null,
            'kursus_id' => $validated['kursus_id'] ?? null,
            'jumlah' => $validated['jumlah'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'tanggal_pembayaran' => Carbon::now(),
            'bukti_pembayaran' => $buktiPath,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'Menunggu Verifikasi',
        ]);

        // Update booking status if applicable
        if (!empty($validated['booking_id'])) {
            $booking = Booking::find($validated['booking_id']);
            $booking->status = 'Menunggu Pembayaran';
            $booking->save();
        }

        // Notify Admin
        Notifikasi::create([
            'user_id' => 1,
            'judul' => 'Konfirmasi Pembayaran Masuk',
            'pesan' => 'Pembayaran ' . $trxNumber . ' sebesar Rp ' . number_format($pembayaran->jumlah, 0, ',', '.') . ' menunggu verifikasi.',
            'link' => '/admin/payments',
        ]);

        return redirect()->route('payments.index')->with('success', 'Bukti pembayaran Anda berhasil dikirim dan sedang diverifikasi oleh admin.');
    }

    // Customer Payment History
    public function index()
    {
        $pembayarans = Pembayaran::where('user_id', Auth::id())
            ->with(['booking.layanan', 'kursus', 'invoice'])
            ->latest()
            ->get();

        return view('payments.index', compact('pembayarans'));
    }

    // Admin Payment List
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $query = Pembayaran::with(['user', 'booking.layanan', 'kursus']);

        if ($status) {
            $query->where('status', $status);
        }

        $pembayarans = $query->latest()->paginate(15);
        return view('admin.payments.index', compact('pembayarans', 'status'));
    }

    // Admin Verify Payment
    public function adminVerify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Dibayar,Ditolak,Dikembalikan',
            'catatan' => 'nullable|string',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->status = $request->status;
        if ($request->filled('catatan')) {
            $pembayaran->catatan = $request->catatan;
        }
        $pembayaran->save();

        if ($request->status === 'Dibayar') {
            // Generate Invoice
            $invNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            Invoice::updateOrCreate(
                ['pembayaran_id' => $pembayaran->id],
                [
                    'nomor_invoice' => $invNumber,
                    'user_id' => $pembayaran->user_id,
                    'booking_id' => $pembayaran->booking_id,
                    'kursus_id' => $pembayaran->kursus_id,
                    'subtotal' => $pembayaran->jumlah,
                    'diskon' => 0,
                    'total' => $pembayaran->jumlah,
                    'status_pembayaran' => 'LUNAS',
                ]
            );

            // If Photography Booking, update booking to Dibayar & Terjadwal
            if ($pembayaran->booking_id) {
                $booking = Booking::find($pembayaran->booking_id);
                $booking->status = 'Dibayar';
                $booking->save();
            }

            // If Course Payment, Auto Enroll Peserta!
            if ($pembayaran->kursus_id) {
                Enrollment::firstOrCreate([
                    'user_id' => $pembayaran->user_id,
                    'kursus_id' => $pembayaran->kursus_id,
                ], [
                    'tanggal_daftar' => Carbon::now(),
                    'status' => 'aktif',
                ]);
            }
        }

        // Notify User
        Notifikasi::create([
            'user_id' => $pembayaran->user_id,
            'judul' => 'Status Pembayaran ' . $pembayaran->nomor_transaksi,
            'pesan' => 'Status pembayaran Anda kini: ' . $pembayaran->status,
            'link' => '/payments',
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    // View Invoice (Printable / Download PDF format)
    public function showInvoice($id)
    {
        $invoice = Invoice::with(['pembayaran', 'user', 'booking.layanan', 'booking.paket', 'kursus'])->findOrFail($id);

        // Security check
        if (!Auth::user()->isAdmin() && Auth::id() !== $invoice->user_id) {
            abort(403);
        }

        return view('payments.invoice', compact('invoice'));
    }
}
