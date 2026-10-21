<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'modul_id',
        'judul',
        'tipe', // teks, pdf, foto, video, link
        'file_path',
        'video_url',
        'isi',
        'urutan',
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function progresses()
    {
        return $this->hasMany(MateriProgress::class, 'materi_id');
    }

    public function isCompletedBy($userId): bool
    {
        return $this->progresses()->where('user_id', $userId)->where('is_completed', true)->exists();
    }
}
