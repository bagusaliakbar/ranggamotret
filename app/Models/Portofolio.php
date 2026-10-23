<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Relasi ke foto-foto di dalam album
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(PortofolioFoto::class, 'portofolio_id')->orderBy('urutan')->orderBy('id');
    }

    /**
     * Accessor URL Foto Sampul (Cover Image)
     */
    public function getFotoUrlAttribute(): string
    {
        if (empty($this->foto)) {
            // Jika foto cover belum diatur, coba ambil foto pertama dari galeri album
            $firstFoto = $this->fotos->first();
            if ($firstFoto) {
                return $firstFoto->foto_url;
            }
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
