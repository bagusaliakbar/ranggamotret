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

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'kursus_id')->where('tipe', 'kursus');
    }

    public function enrollments()
    {
        return $this->reservasis();
    }

    public function sertifikats()
    {
        return $this->hasMany(Sertifikat::class, 'kursus_id');
    }

    public function isEnrolledBy($userId): bool
    {
        return $this->reservasis()
            ->where('user_id', $userId)
            ->whereIn('status', ['Dibayar', 'Selesai', 'aktif'])
            ->exists();
    }

    public function progressOfUser($userId): int
    {
        $hasCert = Sertifikat::where('user_id', $userId)->where('kursus_id', $this->id)->exists();
        if ($hasCert) {
            return 100;
        }

        $totalMateris = 0;
        foreach ($this->moduls as $modul) {
            $totalMateris += $modul->materis->count();
        }

        if ($totalMateris === 0) {
            return 0;
        }

        $completedMateris = MateriProgress::where('user_id', $userId)
            ->whereIn('materi_id', function ($q) {
                $q->select('materi.id')
                    ->from('materi')
                    ->join('modul', 'materi.modul_id', '=', 'modul.id')
                    ->where('modul.kursus_id', $this->id);
            })
            ->where('is_completed', true)
            ->count();

        return (int) round(($completedMateris / $totalMateris) * 100);
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

    public function quizs()
    {
        return $this->hasMany(Quiz::class, 'modul_id');
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
