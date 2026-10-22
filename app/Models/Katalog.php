<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Katalog (ICONIX Robustness: Entity Katalog)
 * 
 * Merepresentasikan Entity Object 'Katalog' pada Robustness Diagram.
 * Terhubung dengan tabel database 'layanan'.
 */
class Katalog extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'harga_mulai',
        'thumbnail',
        'status',
    ];

    public function pakets()
    {
        return $this->hasMany(Paket::class, 'layanan_id');
    }

    public function activePakets()
    {
        return $this->hasMany(Paket::class, 'layanan_id')->where('status', 'aktif');
    }

    public function bookings()
    {
        return $this->hasMany(Reservasi::class, 'layanan_id');
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'layanan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
