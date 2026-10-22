<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriFoto extends Model
{
    protected $table = 'galeri_foto';

    protected $fillable = [
        'booking_id',
        'foto_path',
        'is_selected',
        'catatan',
    ];

    public function booking()
    {
        return $this->belongsTo(Reservasi::class, 'booking_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'booking_id');
    }
}
