<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Reservasi (ICONIX Robustness: Entity Reservasi)
 * 
 * Merepresentasikan Entity Object 'Reservasi' pada Robustness Diagram.
 * Terhubung dengan tabel database 'reservasi' yang menaungi reservasi sesi fotografi
 * maupun reservasi pendaftaran kursus akademi.
 */
class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'tipe', // 'fotografi', 'kursus'
        'nomor_reservasi',
        'nomor_booking', // alias fillable for backward compatibility
        'user_id',
        'layanan_id',
        'paket_id',
        'kursus_id',
        'tanggal',
        'waktu',
        'lokasi',
        'kebutuhan_khusus',
        'status',
    ];

    /**
     * Otomatisasi pengisian nomor_reservasi dan tipe
     */
    protected static function booted()
    {
        static::creating(function ($reservasi) {
            if (empty($reservasi->tipe)) {
                $reservasi->tipe = !empty($reservasi->kursus_id) ? 'kursus' : 'fotografi';
            }

            if (empty($reservasi->nomor_reservasi)) {
                if (!empty($reservasi->nomor_booking)) {
                    $reservasi->nomor_reservasi = $reservasi->nomor_booking;
                } else {
                    $prefix = ($reservasi->tipe === 'kursus' || !empty($reservasi->kursus_id)) ? 'RSV-KRS-' : 'RSV-';
                    $reservasi->nomor_reservasi = $prefix . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    /**
     * Backward-compatibility accessor untuk nomor_booking
     */
    public function getNomorBookingAttribute()
    {
        return $this->nomor_reservasi;
    }

    public function setNomorBookingAttribute($value)
    {
        $this->attributes['nomor_reservasi'] = $value;
    }

    public function isFotografi(): bool
    {
        return ($this->tipe ?? 'fotografi') === 'fotografi';
    }

    public function isKursus(): bool
    {
        return $this->tipe === 'kursus';
    }

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

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'reservasi_id');
    }

    public function galeriFotos()
    {
        return $this->hasMany(GaleriFoto::class, 'reservasi_id');
    }

    public function scopeFotografi($query)
    {
        return $query->where('tipe', 'fotografi');
    }

    public function scopeKursus($query)
    {
        return $query->where('tipe', 'kursus');
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

    public function getProgressPersenAttribute(): int
    {
        if (array_key_exists('progress_persen', $this->attributes)) {
            return (int) $this->attributes['progress_persen'];
        }

        if ($this->tipe === 'kursus' && $this->kursus) {
            return $this->kursus->progressOfUser($this->user_id);
        }

        return 0;
    }

    public function getSertifikatAttribute()
    {
        if (array_key_exists('sertifikat', $this->relations)) {
            return $this->relations['sertifikat'];
        }

        if ($this->tipe === 'kursus' && $this->kursus_id) {
            return \App\Models\Sertifikat::where('user_id', $this->user_id)
                ->where('kursus_id', $this->kursus_id)
                ->first();
        }

        return null;
    }
}

/**
 * Sub-entity GaleriFoto: foto hasil sesi dan client photo proofing
 */
class GaleriFoto extends Model
{
    protected $table = 'galeri_foto';

    protected $fillable = [
        'reservasi_id',
        'foto_path',
        'is_selected',
        'catatan',
    ];

    public function getBookingIdAttribute()
    {
        return $this->reservasi_id;
    }

    public function setBookingIdAttribute($value)
    {
        $this->attributes['reservasi_id'] = $value;
    }

    public function booking()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
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
        'reservasi_id',
        'kursus_id',
        'subtotal',
        'diskon',
        'total',
        'status_pembayaran',
    ];

    public function getBookingIdAttribute()
    {
        return $this->reservasi_id;
    }

    public function setBookingIdAttribute($value)
    {
        $this->attributes['reservasi_id'] = $value;
    }

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
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }
}
