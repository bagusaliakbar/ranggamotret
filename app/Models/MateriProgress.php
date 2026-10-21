<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
