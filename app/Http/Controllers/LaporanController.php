<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller Laporan Keuangan & Kinerja Bisnis
 * 
 * Merepresentasikan Control Object 'LaporanController' pada Robustness Diagram.
 * Terhubung dengan boundary 'laporan' dan entity 'Reservasi' serta 'User'.
 */
class LaporanController extends ReportController
{
    // Inherits index() for financial, booking, and academy reports
}
