<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Reservasi;

/**
 * Controller Pengelolaan Jadwal & Kalender Pemotretan (ICONIX Robustness: JadwalController)
 * 
 * Merepresentasikan Control Object 'JadwalController' pada Robustness Diagram.
 * Terhubung dengan boundary 'jadwal' dan entity 'Jadwal' serta 'Reservasi'.
 */
class JadwalController extends Controller
{
    /**
     * Menampilkan antarmuka kalender jadwal interaktif
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $bookings = Jadwal::with(['customer', 'layanan'])->get();
        } else {
            $bookings = Jadwal::where('user_id', $user->id)->with('layanan')->get();
        }

        $events = $bookings->map(function ($b) {
            $color = match ($b->status) {
                'Disetujui', 'Terjadwal' => '#3b82f6',
                'Dibayar' => '#10b981',
                'Selesai' => '#6b7280',
                'Dibatalkan', 'Ditolak' => '#ef4444',
                default => '#f59e0b',
            };

            return [
                'id' => $b->id,
                'title' => $b->nomor_booking . ' - ' . ($b->layanan->nama ?? 'Sesi Foto') . ' (' . $b->status . ')',
                'start' => $b->tanggal . 'T' . $b->waktu,
                'color' => $color,
                'url' => Auth::user()->isAdmin() ? route('admin.bookings.show', $b->id) : route('customer.bookings.show', $b->id),
            ];
        });

        return view('calendar.index', compact('events'));
    }
}
