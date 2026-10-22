<?php

namespace App\Models;

/**
 * Model Katalog (Entitas Katalog Layanan Fotografi & Paket)
 * 
 * Merepresentasikan Entity Object 'Katalog' pada Robustness Diagram.
 * Terhubung dengan boundary 'katalog' melalui KatalogController.
 */
class Katalog extends Layanan
{
    protected $table = 'layanan';

    /**
     * Scope untuk mengambil katalog layanan yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
