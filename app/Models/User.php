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
        'role', // admin, instruktur, customer, peserta
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstruktur(): bool
    {
        return $this->role === 'instruktur';
    }

    public function isCustomer(): bool
    {
        return in_array($this->role, ['customer', 'peserta']);
    }

    public function isPeserta(): bool
    {
        return in_array($this->role, ['customer', 'peserta']);
    }

    public function isMember(): bool
    {
        return in_array($this->role, ['customer', 'peserta']);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
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
