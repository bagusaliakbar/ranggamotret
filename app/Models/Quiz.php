<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz';

    protected $fillable = [
        'modul_id',
        'judul',
        'durasi_menit',
        'passing_grade',
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function soals()
    {
        return $this->hasMany(QuizSoal::class, 'quiz_id');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    public function bestAttemptOf($userId)
    {
        return $this->attempts()->where('user_id', $userId)->orderByDesc('nilai')->first();
    }
}
