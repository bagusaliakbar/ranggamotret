<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortofolioFoto extends Model
{
    protected $table = 'portofolio_foto';

    protected $fillable = [
        'portofolio_id',
        'foto',
        'caption',
        'urutan',
    ];

    public function portofolio(): BelongsTo
    {
        return $this->belongsTo(Portofolio::class, 'portofolio_id');
    }

    /**
     * Accessor URL Foto (mendukung external link Unsplash dan file upload lokal storage)
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
