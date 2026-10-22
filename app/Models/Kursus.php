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

    public function owner()
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

    public function testimoni()
    {
        return $this->hasMany(Testimoni::class, 'kursus_id');
    }

    public function testimonis()
    {
        return $this->hasMany(Testimoni::class, 'kursus_id');
    }
}

/**
 * Sub-entity Modul: struktur kurikulum/bab materi kursus
 */
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

/**
 * Sub-entity Enrollment: data pendaftaran dan progres belajar siswa
 */
class Enrollment extends Model
{
    protected $table = 'enrollment';

    protected $fillable = [
        'user_id',
        'kursus_id',
        'tanggal_daftar',
        'status',
    ];

    protected $appends = [
        'progress_persen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'kursus_id', 'kursus_id')
            ->where('user_id', $this->user_id);
    }

    public function getProgressPersenAttribute(): int
    {
        if ($this->status === 'selesai' || $this->sertifikat()->exists()) {
            return 100;
        }

        $kursus = $this->kursus;
        if (!$kursus) {
            return 0;
        }

        $totalMateris = 0;
        foreach ($kursus->moduls as $modul) {
            $totalMateris += $modul->materis->count();
        }

        if ($totalMateris === 0) {
            return 0;
        }

        $completedMateris = MateriProgress::where('user_id', $this->user_id)
            ->whereIn('materi_id', function ($q) {
                $q->select('materi.id')
                    ->from('materi')
                    ->join('modul', 'materi.modul_id', '=', 'modul.id')
                    ->where('modul.kursus_id', $this->kursus_id);
            })
            ->where('is_completed', true)
            ->count();

        return (int) round(($completedMateris / $totalMateris) * 100);
    }
}

/**
 * Sub-entity Sertifikat: sertifikat kelulusan digital akademi
 */
class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'user_id',
        'kursus_id',
        'nomor_sertifikat',
        'kode_verifikasi',
        'tanggal_terbit',
        'nilai_akhir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }
}

