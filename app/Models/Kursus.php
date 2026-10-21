<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kursus extends Model
{
    protected $table = 'kursus';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'durasi',
        'level',
        'instruktur_id',
        'thumbnail',
        'status',
    ];

    public function instruktur()
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function moduls()
    {
        return $this->hasMany(Modul::class, 'kursus_id')->orderBy('urutan');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'kursus_id');
    }

    public function sertifikats()
    {
        return $this->hasMany(Sertifikat::class, 'kursus_id');
    }
}
