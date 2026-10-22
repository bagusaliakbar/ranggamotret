<?php

namespace App\Services;

use App\Models\Reservasi;
use App\Models\Kursus;
use App\Models\Pembayaran;
use App\Models\Invoice;
use App\Models\Notifikasi;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $this->initMidtrans();
    }

    protected function initMidtrans(): void
    {
        if (!class_exists(\Midtrans\Config::class)) {
            $midtransFile = base_path('vendor/midtrans/midtrans-php/Midtrans.php');
            if (file_exists($midtransFile)) {
                require_once $midtransFile;
            }
        }

        if (class_exists(\Midtrans\Config::class)) {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', true);
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds', true);
        }
    }

    /**
     * Generate Midtrans Snap Token for a Booking session.
     */
    public function getSnapTokenForBooking(Reservasi $booking): string
    {
        $this->initMidtrans();

        $grossAmount = (int) round($booking->paket->harga ?? 0);
        $orderId = $booking->nomor_booking;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $booking->customer->name ?? 'Customer',
                'email' => $booking->customer->email ?? 'customer@ranggamotret.id',
                'phone' => $booking->customer->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'LAYANAN-' . $booking->layanan_id . '-PAKET-' . $booking->paket_id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => mb_substr($booking->layanan->nama . ' - ' . $booking->paket->nama_paket, 0, 50),
                ]
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            // If order_id was already used in Midtrans, auto-retry with unique transaction attempt suffix
            if (str_contains(strtolower($e->getMessage()), 'taken') || str_contains(strtolower($e->getMessage()), 'sudah digunakan')) {
                $orderId = $booking->nomor_booking . '-T' . time();
                $params['transaction_details']['order_id'] = $orderId;
                $snapToken = \Midtrans\Snap::getSnapToken($params);
            } else {
                Log::error('Midtrans Snap Error: ' . $e->getMessage(), ['params' => $params]);
                throw $e;
            }
        }

        // Record or update pending pembayaran entry
        Pembayaran::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'nomor_transaksi' => 'TRX-' . str_replace('BK-', '', $orderId),
                'user_id' => $booking->user_id,
                'jumlah' => $grossAmount,
                'metode_pembayaran' => 'Midtrans Payment Gateway (Snap)',
                'snap_token' => $snapToken,
                'status' => 'Belum Dibayar',
            ]
        );

        return $snapToken;
    }

    /**
     * Generate Midtrans Snap Token for a Course registration.
     */
    public function getSnapTokenForKursus(Kursus $kursus, User $user): string
    {
        $this->initMidtrans();

        $grossAmount = (int) round($kursus->harga ?? 0);
        $orderId = 'KRS-' . $kursus->id . '-' . $user->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'KURSUS-' . $kursus->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => mb_substr($kursus->nama, 0, 50),
                ]
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            Pembayaran::create([
                'nomor_transaksi' => 'TRX-' . $orderId,
                'user_id' => $user->id,
                'kursus_id' => $kursus->id,
                'jumlah' => $grossAmount,
                'metode_pembayaran' => 'Midtrans Payment Gateway (Snap)',
                'snap_token' => $snapToken,
                'status' => 'Belum Dibayar',
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Kursus Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process Webhook / HTTP Notification from Midtrans.
     */
    public function handleNotification(array $payload): array
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $serverKey = config('midtrans.server_key');
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'midtrans';

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return ['status' => 'error', 'message' => 'Invalid notification payload'];
        }

        // Verify SHA512 Signature Key
        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== $mySignature) {
            Log::warning('Midtrans Signature Mismatch', [
                'expected' => $mySignature,
                'received' => $signatureKey,
                'order_id' => $orderId
            ]);
            return ['status' => 'error', 'message' => 'Signature verification failed'];
        }

        // Determine final transaction status
        $isPaid = false;
        $isPending = false;
        $isFailed = false;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $isPending = true;
            } elseif ($fraudStatus === 'accept') {
                $isPaid = true;
            }
        } elseif ($transactionStatus === 'settlement') {
            $isPaid = true;
        } elseif ($transactionStatus === 'pending') {
            $isPending = true;
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $isFailed = true;
        }

        // Find associated Booking (strip potential retry timestamp suffix)
        $baseBookingNumber = explode('-T', $orderId)[0];
        $booking = Reservasi::where('nomor_booking', $orderId)
            ->orWhere('nomor_booking', $baseBookingNumber)
            ->first();
        if ($booking) {
            $pembayaran = Pembayaran::firstOrNew(['booking_id' => $booking->id]);
            $pembayaran->nomor_transaksi = 'TRX-' . str_replace('BK-', '', $orderId);
            $pembayaran->user_id = $booking->user_id;
            $pembayaran->jumlah = (float) $grossAmount;
            $pembayaran->metode_pembayaran = 'Midtrans (' . strtoupper($paymentType) . ')';
            $pembayaran->payment_type = $paymentType;
            $pembayaran->payload_response = $payload;

            if ($isPaid) {
                $pembayaran->status = 'Dibayar';
                $pembayaran->tanggal_pembayaran = Carbon::now();
                $pembayaran->save();

                $booking->status = 'Dibayar';
                $booking->save();

                // Create official Invoice if not exists
                $invoice = Invoice::firstOrCreate(
                    ['pembayaran_id' => $pembayaran->id],
                    [
                        'nomor_invoice' => 'INV-' . date('Ymd') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                        'user_id' => $booking->user_id,
                        'booking_id' => $booking->id,
                        'subtotal' => $grossAmount,
                        'diskon' => 0,
                        'total' => $grossAmount,
                        'status_pembayaran' => 'Lunas',
                    ]
                );

                // Notify Admin
                Notifikasi::create([
                    'user_id' => 1,
                    'judul' => 'Pembayaran Booking Berhasil!',
                    'pesan' => 'Pembayaran ' . $orderId . ' sebesar Rp ' . number_format($grossAmount, 0, ',', '.') . ' telah lunas via Midtrans (' . $paymentType . ').',
                    'link' => '/admin/bookings/' . $booking->id,
                ]);

                // Notify Customer
                Notifikasi::create([
                    'user_id' => $booking->user_id,
                    'judul' => 'Pembayaran Berhasil Dikonfirmasi!',
                    'pesan' => 'Pembayaran sesi pemotretan ' . $booking->layanan->nama . ' telah berhasil diverifikasi. Jadwal sesi Anda telah terkonfirmasi.',
                    'link' => '/customer/bookings/' . $booking->id,
                ]);

                return ['status' => 'success', 'message' => 'Payment settled and booking confirmed'];
            } elseif ($isPending) {
                $pembayaran->status = 'Menunggu Verifikasi';
                $pembayaran->save();
                return ['status' => 'pending', 'message' => 'Payment is pending'];
            } elseif ($isFailed) {
                $pembayaran->status = 'Ditolak';
                $pembayaran->save();
                $booking->status = 'Dibatalkan';
                $booking->save();
                return ['status' => 'failed', 'message' => 'Payment expired or cancelled'];
            }
        }

        // Handle Course Payment (Order ID starts with KRS-)
        if (str_starts_with($orderId, 'KRS-')) {
            $parts = explode('-', $orderId);
            $kursusId = $parts[1] ?? null;
            $userId = $parts[2] ?? null;

            if ($kursusId && $userId) {
                $kursus = Kursus::find($kursusId);
                $pembayaran = Pembayaran::firstOrNew(['nomor_transaksi' => 'TRX-' . $orderId]);
                $pembayaran->user_id = $userId;
                $pembayaran->kursus_id = $kursusId;
                $pembayaran->jumlah = (float) $grossAmount;
                $pembayaran->metode_pembayaran = 'Midtrans (' . strtoupper($paymentType) . ')';
                $pembayaran->payment_type = $paymentType;
                $pembayaran->payload_response = $payload;

                if ($isPaid) {
                    $pembayaran->status = 'Dibayar';
                    $pembayaran->tanggal_pembayaran = Carbon::now();
                    $pembayaran->save();

                    // Enroll student
                    Enrollment::firstOrCreate(
                        ['user_id' => $userId, 'kursus_id' => $kursusId],
                        ['status' => 'aktif', 'progress_persen' => 0]
                    );

                    // Create Invoice
                    Invoice::firstOrCreate(
                        ['pembayaran_id' => $pembayaran->id],
                        [
                            'nomor_invoice' => 'INV-KRS-' . date('Ymd') . '-' . str_pad($pembayaran->id, 4, '0', STR_PAD_LEFT),
                            'user_id' => $userId,
                            'subtotal' => $grossAmount,
                            'diskon' => 0,
                            'total' => $grossAmount,
                            'status_pembayaran' => 'Lunas',
                        ]
                    );

                    // Notify Customer
                    Notifikasi::create([
                        'user_id' => $userId,
                        'judul' => 'Pendaftaran Kursus Berhasil!',
                        'pesan' => 'Pembayaran kursus ' . ($kursus->nama ?? '') . ' lunas. Akses ruang belajar Anda telah aktif!',
                        'link' => '/student/dashboard',
                    ]);

                    return ['status' => 'success', 'message' => 'Course payment settled and student enrolled'];
                } elseif ($isPending) {
                    $pembayaran->status = 'Menunggu Verifikasi';
                    $pembayaran->save();
                    return ['status' => 'pending', 'message' => 'Course payment is pending'];
                } elseif ($isFailed) {
                    $pembayaran->status = 'Ditolak';
                    $pembayaran->save();
                    return ['status' => 'failed', 'message' => 'Course payment expired or cancelled'];
                }
            }
        }

        return ['status' => 'ignored', 'message' => 'Order not associated with an active transaction'];
    }

    /**
     * Query Midtrans Transaction Status directly via API and sync local database.
     */
    public function syncStatusFromMidtrans(string $orderId): array
    {
        $this->initMidtrans();

        try {
            $statusObj = \Midtrans\Transaction::status($orderId);
            $payload = json_decode(json_encode($statusObj), true);

            $transactionStatus = $payload['transaction_status'] ?? null;
            $fraudStatus = $payload['fraud_status'] ?? null;
            $grossAmount = $payload['gross_amount'] ?? 0;
            $paymentType = $payload['payment_type'] ?? 'midtrans';

            $isPaid = false;
            if ($transactionStatus === 'capture') {
                $isPaid = ($fraudStatus === 'accept');
            } elseif ($transactionStatus === 'settlement') {
                $isPaid = true;
            }

            $baseBookingNumber = explode('-T', $orderId)[0];
            $booking = Reservasi::where('nomor_booking', $orderId)
                ->orWhere('nomor_booking', $baseBookingNumber)
                ->first();

            if ($booking) {
                $pembayaran = Pembayaran::firstOrNew(['booking_id' => $booking->id]);
                $pembayaran->nomor_transaksi = 'TRX-' . str_replace('BK-', '', $orderId);
                $pembayaran->user_id = $booking->user_id;
                $pembayaran->jumlah = (float) ($grossAmount ?: $booking->paket->harga);
                $pembayaran->metode_pembayaran = 'Midtrans (' . strtoupper($paymentType) . ')';
                $pembayaran->payment_type = $paymentType;
                $pembayaran->payload_response = $payload;

                if ($isPaid) {
                    $pembayaran->status = 'Dibayar';
                    $pembayaran->tanggal_pembayaran = Carbon::now();
                    $pembayaran->save();

                    $booking->status = 'Dibayar';
                    $booking->save();

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

                    return ['status' => 'success', 'is_paid' => true, 'message' => 'Status pembayaran berhasil disinkronkan: DIBAYAR'];
                } else {
                    $pembayaran->status = ($transactionStatus === 'pending') ? 'Menunggu Verifikasi' : 'Belum Dibayar';
                    $pembayaran->save();
                    return ['status' => 'info', 'is_paid' => false, 'message' => 'Status transaksi Midtrans: ' . $transactionStatus];
                }
            }

            return ['status' => 'warning', 'is_paid' => false, 'message' => 'Reservasi tidak ditemukan untuk order ID tersebut.'];
        } catch (\Exception $e) {
            Log::warning('Midtrans syncStatusFromMidtrans failed: ' . $e->getMessage(), ['order_id' => $orderId]);
            return ['status' => 'error', 'is_paid' => false, 'message' => 'Gagal menghubungi server Midtrans: ' . $e->getMessage()];
        }
    }
}

