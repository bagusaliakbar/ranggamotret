<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'phone',
        'jenis_layanan',
        'konsep',
        'lokasi',
        'tanggal',
        'budget',
        'kebutuhan',
        'referensi_foto',
        'tanggapan_admin',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
