<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
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
        return $this->hasMany(Booking::class, 'layanan_id');
    }
}
