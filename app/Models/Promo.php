<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    protected $fillable = [
        'kode_promo',
        'deskripsi',
        'diskon_persen',
        'diskon_nominal',
        'berlaku_dari',
        'berlaku_sampai',
        'status',
    ];
}
