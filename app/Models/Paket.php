<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'layanan_id',
        'nama_paket',
        'harga',
        'durasi',
        'jumlah_fotografer',
        'jumlah_foto',
        'fasilitas',
        'deskripsi',
        'status',
    ];

    public function layanan()
    {
        return $this->belongsTo(Katalog::class, 'layanan_id');
    }

    public function katalog()
    {
        return $this->belongsTo(Katalog::class, 'layanan_id');
    }
}
