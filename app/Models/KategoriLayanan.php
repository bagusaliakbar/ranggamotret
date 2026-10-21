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
     * Relationship to Layanan (by category name).
     */
    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'kategori', 'nama');
    }

    /**
     * Relationship to Active Layanan.
     */
    public function activeLayanan()
    {
        return $this->hasMany(Layanan::class, 'kategori', 'nama')->where('status', 'aktif');
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Auto-generate slug before saving if not provided.
     */
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
