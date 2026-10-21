<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
    protected $table = 'portofolio';

    protected $fillable = [
        'judul',
        'kategori',
        'foto',
        'deskripsi',
        'tanggal',
        'status_publikasi',
    ];
}
