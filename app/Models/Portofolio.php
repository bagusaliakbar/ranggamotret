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

    /**
     * Accessor URL Foto (mendukung external link Unsplash dan file upload lokal)
     */
    public function getFotoUrlAttribute(): string
    {
        if (empty($this->foto)) {
            return asset('images/placeholder.jpg');
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        if (str_starts_with($this->foto, 'storage/') || str_starts_with($this->foto, '/storage/')) {
            return asset(ltrim($this->foto, '/'));
        }

        return asset('storage/' . ltrim($this->foto, '/'));
    }
}
