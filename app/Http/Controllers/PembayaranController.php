<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller Transaksi Pembayaran & Verifikasi Tagihan
 * 
 * Merepresentasikan Control Object 'PembayaranController' pada Robustness Diagram.
 * Terhubung dengan boundary 'pembayaran' dan entity 'Pembayaran', 'Reservasi', serta 'User'.
 */
class PembayaranController extends PaymentController
{
    // Inherits index(), create(), store(), paymentFinish(), showInvoice(), adminIndex(), adminVerify(), handleCallback()
}
