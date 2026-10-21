<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $table = 'modul';

    protected $fillable = [
        'kursus_id',
        'judul',
        'urutan',
        'deskripsi',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function materis()
    {
        return $this->hasMany(Materi::class, 'modul_id')->orderBy('urutan');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'modul_id');
    }

    public function quizs()
    {
        return $this->hasMany(Quiz::class, 'modul_id');
    }
}
