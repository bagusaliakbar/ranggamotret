<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Reservasi (ICONIX Robustness: Entity Reservasi)
 * 
 * Merepresentasikan Entity Object 'Reservasi' pada Robustness Diagram.
 * Terhubung dengan tabel database 'booking'.
 */
class Reservasi extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'nomor_booking',
        'user_id',
        'layanan_id',
        'paket_id',
        'tanggal',
        'waktu',
        'lokasi',
        'kebutuhan_khusus',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Katalog::class, 'layanan_id');
    }

    public function katalog()
    {
        return $this->belongsTo(Katalog::class, 'layanan_id');
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'booking_id');
    }

    public function galeriFotos()
    {
        return $this->hasMany(GaleriFoto::class, 'booking_id');
    }

    public function testimoni()
    {
        return $this->hasOne(Testimoni::class, 'booking_id');
    }

    public function scopeAktif($query)
    {
        return $query->whereIn('status', [
            'Menunggu Konfirmasi',
            'Disetujui',
            'Menunggu Pembayaran',
            'Dibayar',
            'Terjadwal'
        ]);
    }
}
