<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\Kursus;
use App\Models\Invoice;
use App\Models\Enrollment;
use App\Models\Notifikasi;
use App\Services\MidtransService;
use Carbon\Carbon;

/**
 * Controller Manajemen Transaksi & Pembayaran (ICONIX Robustness: PembayaranController)
 * 
 * Bertanggung jawab atas:
 * - Checkout Midtrans Snap Payment Gateway
 * - Penyerahan bukti transfer bank manual oleh pelanggan
 * - Penerimaan webhook callback otomatis dari Midtrans
 * - Verifikasi pembayaran manual dan penerbitan invoice resmi oleh Admin
 */
class PembayaranController extends Controller
{
    /**
     * Riwayat pembayaran pelanggan
     */
    public function index()
    {
        $userId = Auth::id();

        // Auto-sinkronisasi jika reservasi sudah lunas namun status pembayaran belum terupdate
        $unpaidBookings = Pembayaran::where('user_id', $userId)
            ->where('status', '!=', 'Dibayar')
            ->whereNotNull('booking_id')
            ->with('booking')
            ->get();

        foreach ($unpaidBookings as $p) {
            if ($p->booking && in_array($p->booking->status, ['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai'])) {
                $p->status = 'Dibayar';
                $p->tanggal_pembayaran = $p->tanggal_pembayaran ?: Carbon::now();
                $p->save();

                Invoice::firstOrCreate(
                    ['pembayaran_id' => $p->id],
                    [
                        'nomor_invoice' => 'INV-' . date('Ymd') . '-' . str_pad($p->booking_id, 4, '0', STR_PAD_LEFT),
                        'user_id' => $userId,
                        'booking_id' => $p->booking_id,
                        'subtotal' => $p->jumlah,
                        'diskon' => 0,
                        'total' => $p->jumlah,
                        'status_pembayaran' => 'Lunas',
                    ]
                );
            }
        }

        $pembayarans = Pembayaran::where('user_id', $userId)
            ->with(['booking.layanan', 'kursus', 'invoice'])
            ->latest()
            ->get();

        return view('pembayaran.index', compact('pembayarans'));
    }

    /**
     * Form checkout pembayaran (Midtrans Snap & Manual Transfer)
     */
    public function create(Request $request, MidtransService $midtransService)
    {
        $bookingId = $request->query('booking_id');
        $kursusId = $request->query('kursus_id');

        $booking = $bookingId ? Reservasi::with(['layanan', 'paket', 'customer'])->where('user_id', Auth::id())->find($bookingId) : null;
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

        return view('pembayaran.create', compact('booking', 'kursus', 'snapToken', 'snapError', 'midtransClientKey', 'midtransSnapUrl'));
    }

    /**
     * Unggah bukti transfer manual oleh pelanggan
     */
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

        if (!empty($validated['booking_id'])) {
            $booking = Reservasi::find($validated['booking_id']);
            $booking->status = 'Menunggu Pembayaran';
            $booking->save();
        }

        Notifikasi::create([
            'user_id' => 1,
            'judul' => 'Konfirmasi Pembayaran Masuk',
            'pesan' => 'Pembayaran ' . $trxNumber . ' sebesar Rp ' . number_format($pembayaran->jumlah, 0, ',', '.') . ' menunggu verifikasi.',
            'link' => '/admin/payments',
        ]);

        return redirect()->route('payments.index')->with('success', 'Bukti pembayaran Anda berhasil dikirim dan sedang diverifikasi oleh admin.');
    }

    /**
     * Webhook notification callback Midtrans
     */
    public function handleCallback(Request $request, MidtransService $midtransService)
    {
        $payload = $request->all();
        $result = $midtransService->handleNotification($payload);

        return response()->json($result);
    }

    /**
     * URL kembalian setelah transaksi Snap Midtrans
     */
    public function paymentFinish(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionStatus = $request->query('transaction_status');

        if ($orderId) {
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

                        $pembayaran = Pembayaran::where('user_id', $userId)
                            ->where('kursus_id', $kursusId)
                            ->latest()
                            ->first();
                        if ($pembayaran) {
                            $pembayaran->status = 'Dibayar';
                            $pembayaran->tanggal_pembayaran = Carbon::now();
                            $pembayaran->save();

                            Invoice::firstOrCreate(
                                ['pembayaran_id' => $pembayaran->id],
                                [
                                    'nomor_invoice' => 'INV-KRS-' . date('Ymd') . '-' . str_pad($pembayaran->id, 4, '0', STR_PAD_LEFT),
                                    'user_id' => $userId,
                                    'subtotal' => $pembayaran->jumlah,
                                    'diskon' => 0,
                                    'total' => $pembayaran->jumlah,
                                    'status_pembayaran' => 'Lunas',
                                ]
                            );
                        }
                    }
                    return redirect()->route('student.dashboard')
                        ->with('success', 'Pembayaran kursus berhasil! Selamat belajar di ruang kelas akademi RanggaMotret.');
                }
                return redirect()->route('student.dashboard')
                    ->with('info', 'Status transaksi kursus Anda sedang diproses.');
            }

            $baseBookingNumber = explode('-T', $orderId)[0];
            $booking = Reservasi::where('nomor_booking', $orderId)
                ->orWhere('nomor_booking', $baseBookingNumber)
                ->first();
            if ($booking) {
                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    $booking->status = 'Dibayar';
                    $booking->save();

                    $pembayaran = Pembayaran::where('booking_id', $booking->id)->latest()->first();
                    if ($pembayaran) {
                        $pembayaran->status = 'Dibayar';
                        $pembayaran->tanggal_pembayaran = Carbon::now();
                        $pembayaran->save();

                        Invoice::firstOrCreate(
                            ['pembayaran_id' => $pembayaran->id],
                            [
                                'nomor_invoice' => 'INV-' . date('Ymd') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                                'user_id' => $booking->user_id,
                                'booking_id' => $booking->id,
                                'subtotal' => $pembayaran->jumlah,
                                'diskon' => 0,
                                'total' => $pembayaran->jumlah,
                                'status_pembayaran' => 'Lunas',
                            ]
                        );
                    }

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

    /**
     * Sinkronkan status pembayaran secara mandiri dari API Midtrans
     */
    public function syncStatus($id, MidtransService $midtransService)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        $orderId = $pembayaran->booking ? $pembayaran->booking->nomor_booking : null;
        if (!$orderId && $pembayaran->kursus_id) {
            $orderId = 'KRS-' . $pembayaran->kursus_id . '-' . $pembayaran->user_id;
        }

        // Cek juga jika ada suffix transaksi
        if ($pembayaran->nomor_transaksi) {
            $trxClean = str_replace('TRX-', 'BK-', $pembayaran->nomor_transaksi);
            $result = $midtransService->syncStatusFromMidtrans($trxClean);
            if ($result && ($result['is_paid'] ?? false)) {
                return back()->with('success', 'Status pembayaran berhasil disinkronkan: DIBAYAR.');
            }
        }

        if ($orderId) {
            $result = $midtransService->syncStatusFromMidtrans($orderId);
            if ($result && ($result['is_paid'] ?? false)) {
                return back()->with('success', 'Status pembayaran berhasil disinkronkan: DIBAYAR.');
            }
        }

        // Auto-heal fallback: jika booking sudah dibayar
        if ($pembayaran->booking && in_array($pembayaran->booking->status, ['Dibayar', 'Disetujui', 'Terjadwal', 'Selesai'])) {
            $pembayaran->status = 'Dibayar';
            $pembayaran->tanggal_pembayaran = $pembayaran->tanggal_pembayaran ?: Carbon::now();
            $pembayaran->save();

            Invoice::firstOrCreate(
                ['pembayaran_id' => $pembayaran->id],
                [
                    'nomor_invoice' => 'INV-' . date('Ymd') . '-' . str_pad($pembayaran->booking_id, 4, '0', STR_PAD_LEFT),
                    'user_id' => $pembayaran->user_id,
                    'booking_id' => $pembayaran->booking_id,
                    'subtotal' => $pembayaran->jumlah,
                    'diskon' => 0,
                    'total' => $pembayaran->jumlah,
                    'status_pembayaran' => 'Lunas',
                ]
            );

            return back()->with('success', 'Pembayaran berhasil diverifikasi lunas!');
        }

        return back()->with('info', 'Transaksi belum terbayar atau masih menunggu verifikasi.');
    }

    /**
     * Daftar pembayaran di panel Admin
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $query = Pembayaran::with(['user', 'booking.layanan', 'kursus']);

        if ($status) {
            $query->where('status', $status);
        }

        $pembayarans = $query->latest()->paginate(15);
        return view('admin.pembayaran.index', compact('pembayarans', 'status'));
    }

    /**
     * Admin memverifikasi pembayaran manual
     */
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

            if ($pembayaran->booking_id) {
                $booking = Reservasi::find($pembayaran->booking_id);
                $booking->status = 'Dibayar';
                $booking->save();
            }

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

        Notifikasi::create([
            'user_id' => $pembayaran->user_id,
            'judul' => 'Status Pembayaran ' . $pembayaran->nomor_transaksi,
            'pesan' => 'Status pembayaran Anda kini: ' . $pembayaran->status,
            'link' => '/pembayaran',
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Menampilkan invoice resmi cetak/PDF
     */
    public function showInvoice($id)
    {
        $invoice = Invoice::with(['pembayaran', 'user', 'booking.layanan', 'booking.paket', 'kursus'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && Auth::id() !== $invoice->user_id) {
            abort(403);
        }

        return view('pembayaran.invoice', compact('invoice'));
    }
}
