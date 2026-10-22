<?php

namespace App\Models;

/**
 * Model Jadwal (ICONIX Robustness: Entity Jadwal)
 * 
 * Merepresentasikan Entity Object 'Jadwal' pada Robustness Diagram.
 * Terhubung dengan boundary 'jadwal' melalui JadwalController.
 */
class Jadwal extends Reservasi
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
