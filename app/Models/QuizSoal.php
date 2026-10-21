<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSoal extends Model
{
    protected $table = 'quiz_soal';

    protected $fillable = [
        'quiz_id',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'jawaban_benar',
        'pembahasan',
        'bobot_nilai',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}
