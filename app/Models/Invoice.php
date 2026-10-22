<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';

    protected $fillable = [
        'nomor_invoice',
        'pembayaran_id',
        'user_id',
        'booking_id',
        'kursus_id',
        'subtotal',
        'diskon',
        'total',
        'status_pembayaran',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

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
