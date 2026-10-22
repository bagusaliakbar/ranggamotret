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

/**
 * Sub-entity MateriProgress: pencatatan status selesai membaca/menonton materi
 */
class MateriProgress extends Model
{
    protected $table = 'materi_progress';

    protected $fillable = [
        'user_id',
        'materi_id',
        'is_completed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}

/**
 * Sub-entity Tugas: tugas praktik modul pembelajaran kursus
 */
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

/**
 * Sub-entity TugasSubmission: pengumpulan file tugas praktik dan penilaian
 */
class TugasSubmission extends Model
{
    protected $table = 'tugas_submission';

    protected $fillable = [
        'tugas_id',
        'user_id',
        'file_path',
        'catatan',
        'tanggal_submit',
        'nilai',
        'feedback',
        'status',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

/**
 * Sub-entity Quiz: tes evaluasi pemahaman modul kursus
 */
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

/**
 * Sub-entity QuizSoal: butir-butir pertanyaan pilihan ganda pada kuis
 */
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

/**
 * Sub-entity QuizAttempt: riwayat pengerjaan kuis siswa dan skor evaluasi
 */
class QuizAttempt extends Model
{
    protected $table = 'quiz_attempt';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'nilai',
        'is_passed',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

