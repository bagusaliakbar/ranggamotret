<?php

namespace App\Models;

/**
 * Model Jadwal (Entitas Jadwal Sesi Pemotretan & Mengajar)
 * 
 * Merepresentasikan Entity Object 'Jadwal' pada Robustness Diagram.
 * Terhubung dengan boundary 'jadwal' melalui JadwalController.
 */
class Jadwal extends Booking
{
    protected $table = 'booking';

    /**
     * Scope untuk jadwal sesi terkonfirmasi
     */
    public function scopeTerjadwal($query)
    {
        return $query->whereIn('status', ['Terjadwal', 'Disetujui', 'Dibayar']);
    }
}
