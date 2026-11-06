<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Services\MidtransService;
use Illuminate\Console\Command;

class RefreshChannelPembayaran extends Command
{
    protected $signature = 'pembayaran:refresh-channel {--all : Proses ulang semua transaksi, bukan hanya yang channel-nya kosong}';

    protected $description = 'Ambil channel pembayaran (VA bank, QRIS, e-wallet, dll.) dari API Midtrans untuk transaksi yang belum tercatat.';

    public function handle(MidtransService $midtrans): int
    {
        $query = Pembayaran::query();
        if (!$this->option('all')) {
            $query->whereNull('payment_type');
        }

        $total = 0;
        $updated = 0;

        $query->orderBy('id')->each(function (Pembayaran $p) use ($midtrans, &$total, &$updated) {
            $total++;
            $ok = $midtrans->refreshChannel($p);
            $updated += $ok ? 1 : 0;
            $this->line(sprintf('%s  %s', $ok ? '[OK]  ' : '[SKIP]', $p->nomor_transaksi));
        });

        $this->info("Selesai: {$updated} dari {$total} transaksi diperbarui.");

        return self::SUCCESS;
    }
}
