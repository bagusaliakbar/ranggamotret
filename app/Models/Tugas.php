<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'modul_id',
        'judul',
        'instruksi',
        'deadline',
        'file_pendukung',
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function submissions()
    {
        return $this->hasMany(TugasSubmission::class, 'tugas_id');
    }

    public function submissionOf($userId)
    {
        return $this->submissions()->where('user_id', $userId)->first();
    }
}
