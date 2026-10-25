<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // owner, pelanggan
        'status', // active, inactive
        'phone',
        'address',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    public function isAdmin(): bool
    {
        return $this->isOwner();
    }

    public function isPelanggan(): bool
    {
        return in_array($this->role, ['pelanggan', 'customer', 'peserta']);
    }

    public function isCustomer(): bool
    {
        return $this->isPelanggan();
    }

    public function isPeserta(): bool
    {
        return $this->isPelanggan();
    }

    public function bookings()
    {
        return $this->hasMany(Reservasi::class, 'user_id');
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'user_id');
    }

    public function reservasiFoto()
    {
        return $this->hasMany(Reservasi::class, 'user_id')->where('tipe', 'fotografi');
    }

    public function reservasiKursus()
    {
        return $this->hasMany(Reservasi::class, 'user_id')->where('tipe', 'kursus');
    }

    public function enrollments()
    {
        return $this->reservasiKursus();
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'user_id');
    }

    public function sertifikats()
    {
        return $this->hasMany(Sertifikat::class, 'user_id');
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function unreadNotificationsCount()
    {
        return $this->notifikasis()->where('is_read', false)->count();
    }
}

/**
 * Sub-entity Customer: profil tambahan pelanggan (telepon, alamat, bio)
 */
class Customer extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'customers';

    protected $fillable = ['user_id', 'phone', 'address', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

/**
 * Sub-entity Notifikasi: notifikasi transaksi, jadwal, dan sistem
 */
class Notifikasi extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'link',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

