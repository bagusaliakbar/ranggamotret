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

/**
 * Sub-entity GaleriFoto: foto hasil sesi dan client photo proofing
 */
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

/**
 * Sub-entity Invoice: faktur tagihan resmi dari pembayaran reservasi/kursus
 */
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

