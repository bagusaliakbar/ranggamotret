<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Katalog (ICONIX Robustness: Entity Katalog)
 * 
 * Merepresentasikan Entity Object 'Katalog' pada Robustness Diagram.
 * Terhubung dengan tabel database 'layanan'.
 */
class Katalog extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'harga_mulai',
        'thumbnail',
        'status',
    ];

    public function pakets()
    {
        return $this->hasMany(Paket::class, 'layanan_id');
    }

    public function activePakets()
    {
        return $this->hasMany(Paket::class, 'layanan_id')->where('status', 'aktif');
    }

    public function bookings()
    {
        return $this->hasMany(Reservasi::class, 'layanan_id');
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'layanan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}

/**
 * Sub-entity Paket: paket harga dan rincian fasilitas fotografi
 */
class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'layanan_id',
        'nama_paket',
        'harga',
        'durasi',
        'jumlah_fotografer',
        'jumlah_foto',
        'fasilitas',
        'deskripsi',
        'status',
    ];

    public function layanan()
    {
        return $this->belongsTo(Katalog::class, 'layanan_id');
    }

    public function katalog()
    {
        return $this->belongsTo(Katalog::class, 'layanan_id');
    }
}

/**
 * Sub-entity KategoriLayanan: kategori pengelompokan katalog fotografi
 */
class KategoriLayanan extends Model
{
    protected $table = 'kategori_layanan';

    protected $fillable = [
        'nama',
        'slug',
        'icon',
        'deskripsi',
        'status',
    ];

    public function katalog()
    {
        return $this->hasMany(Katalog::class, 'kategori', 'nama');
    }

    public function layanan()
    {
        return $this->hasMany(Katalog::class, 'kategori', 'nama');
    }

    public function activeKatalog()
    {
        return $this->hasMany(Katalog::class, 'kategori', 'nama')->where('status', 'aktif');
    }

    public function activeLayanan()
    {
        return $this->hasMany(Katalog::class, 'kategori', 'nama')->where('status', 'aktif');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->nama);
            }
        });
    }
}

