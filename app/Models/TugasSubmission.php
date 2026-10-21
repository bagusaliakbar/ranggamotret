<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
