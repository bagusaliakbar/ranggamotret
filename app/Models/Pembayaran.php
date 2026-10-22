<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'booking_id',
        'kursus_id',
        'jumlah',
        'metode_pembayaran',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'catatan',
        'status',
        'snap_token',
        'payment_type',
        'payload_response',
    ];

    protected $casts = [
        'payload_response' => 'array',
        'tanggal_pembayaran' => 'datetime',
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

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'pembayaran_id');
    }
}
