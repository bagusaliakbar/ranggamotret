<?php

namespace App\Models;

/**
 * Model Reservasi (Entitas Reservasi Pemotretan & Kursus)
 * 
 * Merepresentasikan Entity Object 'Reservasi' pada Robustness Diagram.
 * Terhubung dengan boundary 'reservasi_foto' & 'reservasi_kursus'
 * melalui ReservasiController.
 */
class Reservasi extends Booking
{
    protected $table = 'booking';

    /**
     * Scope untuk reservasi aktif
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', [
            'Menunggu Konfirmasi',
            'Disetujui',
            'Menunggu Pembayaran',
            'Dibayar',
            'Terjadwal'
        ]);
    }
}
