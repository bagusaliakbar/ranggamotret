<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KategoriLayanan extends Model
{
    use HasFactory;

    protected $table = 'kategori_layanan';

    protected $fillable = [
        'nama',
        'slug',
        'icon',
        'deskripsi',
        'status',
    ];

    /**
     * Relationship to Katalog (by category name).
     */
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
                $category->slug = Str::slug($category->nama);
            }
        });
    }
}
