<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $table = 'testimoni';

    protected $fillable = [
        'user_id',
        'booking_id',
        'kursus_id',
        'rating',
        'komentar',
        'foto',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function booking()
    {
        return $this->belongsTo(Reservasi::class, 'booking_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'booking_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }
}
