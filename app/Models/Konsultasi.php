<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'phone',
        'jenis_layanan',
        'konsep',
        'lokasi',
        'tanggal',
        'budget',
        'kebutuhan',
        'referensi_foto',
        'tanggapan_admin',
        'percakapan',
        'status',
    ];

    protected $casts = [
        'percakapan' => 'array',
        'tanggal' => 'date',
        'budget' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mengambil daftar pesan percakapan (chat messages).
     * Jika kolom percakapan belum terisi (data lama), otomatis menggabungkan
     * pesan awal kebutuhan (pelanggan) dan tanggapan_admin (owner).
     */
    public function getChatMessagesAttribute(): array
    {
        if (!empty($this->percakapan) && is_array($this->percakapan)) {
            return $this->percakapan;
        }

        $list = [];

        if (!empty($this->kebutuhan)) {
            $list[] = [
                'sender' => 'pelanggan',
                'nama' => $this->nama ?: 'Pelanggan',
                'pesan' => $this->kebutuhan,
                'waktu' => $this->created_at ? $this->created_at->format('d M Y, H:i') : null,
            ];
        }

        if (!empty($this->tanggapan_admin)) {
            $list[] = [
                'sender' => 'owner',
                'nama' => 'Owner RanggaMotret',
                'pesan' => $this->tanggapan_admin,
                'waktu' => $this->updated_at ? $this->updated_at->format('d M Y, H:i') : null,
            ];
        }

        return $list;
    }

    /**
     * Menambahkan pesan baru ke riwayat percakapan.
     */
    public function addMessage(string $sender, string $nama, string $pesan): void
    {
        $current = $this->chat_messages;
        $current[] = [
            'sender' => $sender,
            'nama' => $nama,
            'pesan' => $pesan,
            'waktu' => now()->format('d M Y, H:i'),
        ];

        $this->percakapan = $current;
    }
}
