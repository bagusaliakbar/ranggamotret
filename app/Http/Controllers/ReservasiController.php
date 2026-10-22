<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservasi;
use App\Models\Booking;
use App\Models\Layanan;
use App\Models\Paket;

/**
 * Controller Pengelolaan Reservasi Pemotretan & Kursus
 * 
 * Merepresentasikan Control Object 'ReservasiController' pada Robustness Diagram.
 * Terhubung dengan boundary 'reservasi_foto' & 'reservasi_kursus'
 * dan entity 'Reservasi', 'Jadwal', 'Kursus', 'Katalog', serta 'User'.
 */
class ReservasiController extends BookingController
{
    /**
     * Menampilkan daftar reservasi aktif pelanggan
     */
    public function index()
    {
        return $this->customerIndex();
    }

    /**
     * Menampilkan rincian reservasi
     */
    public function show($id)
    {
        return $this->customerShow($id);
    }
}
