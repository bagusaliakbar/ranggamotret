<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'reservasi_id',
        'kursus_id',
        'jumlah',
        'metode_pembayaran',
        'tanggal_pembayaran',
        'catatan',
        'status',
        'snap_token',
        'payment_type',
        'payload_response',
    ];

    protected $casts = [
        'payload_response' => 'array',
        'tanggal_pembayaran' => 'datetime',
    ];

    public function getBookingIdAttribute()
    {
        return $this->reservasi_id;
    }

    public function setBookingIdAttribute($value)
    {
        $this->attributes['reservasi_id'] = $value;
    }

    /**
     * Detail channel pembayaran Midtrans yang dibaca dari payload_response.
     * Mengembalikan ['label' => ..., 'detail' => ..., 'icon' => ...] atau null
     * jika pelanggan belum memilih channel (belum ada payload / payment_type).
     */
    public function getChannelPembayaranAttribute(): ?array
    {
        $payload = is_array($this->payload_response) ? $this->payload_response : [];
        $type = strtolower((string) ($this->payment_type ?? ($payload['payment_type'] ?? '')));

        if ($type === '' || $type === 'midtrans') {
            return null;
        }

        $bank = fn ($v) => strtoupper((string) $v);
        $name = fn ($v) => ucwords(str_replace('_', ' ', (string) $v));

        switch ($type) {
            case 'bank_transfer':
                if (!empty($payload['permata_va_number'])) {
                    return ['label' => 'VA Bank Permata', 'detail' => $payload['permata_va_number'], 'icon' => 'ph-bank'];
                }
                $va = $payload['va_numbers'][0] ?? null;
                if ($va) {
                    return ['label' => 'VA Bank ' . $bank($va['bank'] ?? ''), 'detail' => $va['va_number'] ?? null, 'icon' => 'ph-bank'];
                }
                return ['label' => 'Transfer Bank (VA)', 'detail' => null, 'icon' => 'ph-bank'];

            case 'echannel':
                return ['label' => 'VA Bank Mandiri', 'detail' => $payload['bill_key'] ?? null, 'icon' => 'ph-bank'];

            case 'qris':
                $acq = $payload['acquirer'] ?? null;
                return ['label' => 'QRIS' . ($acq ? ' (' . $name($acq) . ')' : ''), 'detail' => null, 'icon' => 'ph-qr-code'];

            case 'gopay':
            case 'shopeepay':
            case 'dana':
            case 'ovo':
                return ['label' => 'E-Wallet ' . ($type === 'gopay' ? 'GoPay' : $name($type)), 'detail' => null, 'icon' => 'ph-wallet'];

            case 'credit_card':
                $masked = $payload['masked_card'] ?? null;
                $info = trim(($payload['card_type'] ?? '') . ' ' . ($masked ? '•••• ' . substr($masked, -4) : ''));
                return [
                    'label' => 'Kartu Kredit' . (!empty($payload['bank']) ? ' ' . $bank($payload['bank']) : ''),
                    'detail' => $info !== '' ? ucfirst($info) : null,
                    'icon' => 'ph-credit-card',
                ];

            case 'cstore':
                return ['label' => 'Gerai ' . $name($payload['store'] ?? 'Retail'), 'detail' => $payload['payment_code'] ?? null, 'icon' => 'ph-storefront'];

            default:
                return ['label' => $name($type), 'detail' => null, 'icon' => 'ph-lightning'];
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function booking()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'pembayaran_id');
    }
}
