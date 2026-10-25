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
        if ($user->isOwner()) {
            $bookings = Jadwal::with(['customer', 'layanan', 'paket'])->get();
        } else {
            $bookings = Jadwal::where('user_id', $user->id)->with(['layanan', 'paket'])->get();
        }

        $events = $bookings->map(function ($b) {
            $color = match ($b->status) {
                'Disetujui', 'Terjadwal' => '#2563eb',
                'Dibayar' => '#059669',
                'Selesai' => '#475569',
                'Dibatalkan', 'Ditolak' => '#e11d48',
                default => '#d97706',
            };

            return [
                'id' => $b->id,
                'title' => ($b->layanan->nama ?? 'Sesi Foto') . ' — ' . ($b->customer->name ?? 'Pelanggan'),
                'start' => $b->tanggal . 'T' . ($b->waktu ? substr($b->waktu, 0, 5) : '09:00'),
                'color' => $color,
                'extendedProps' => [
                    'booking_id' => $b->id,
                    'nomor_booking' => $b->nomor_booking,
                    'customer_name' => $b->customer->name ?? 'Tamu',
                    'customer_phone' => $b->customer->phone ?? '-',
                    'layanan_nama' => $b->layanan->nama ?? 'Sesi Foto',
                    'paket_nama' => $b->paket->nama_paket ?? '-',
                    'tanggal' => $b->tanggal,
                    'waktu' => $b->waktu ? substr($b->waktu, 0, 5) : '09:00',
                    'lokasi' => $b->lokasi ?? '-',
                    'status' => $b->status,
                    'detail_url' => Auth::user()->isOwner() ? route('admin.bookings.show', $b->id) : route('customer.bookings.show', $b->id),
                ],
            ];
        });

        // Rekapitulasi status sesi foto
        $totalSesi = $bookings->count();
        $sesiTerjadwal = $bookings->whereIn('status', ['Terjadwal', 'Disetujui', 'Dibayar'])->count();
        $sesiPending = $bookings->where('status', 'Menunggu Konfirmasi')->count();
        $sesiSelesai = $bookings->where('status', 'Selesai')->count();

        return view('jadwal.index', compact('events', 'totalSesi', 'sesiTerjadwal', 'sesiPending', 'sesiSelesai'));
    }

    /**
     * Memperbarui tanggal, jam, atau status jadwal sesi pemotretan (Reschedule / Manage)
     */
    public function updateSchedule(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $booking = Reservasi::findOrFail($id);

        $newTanggal = $request->tanggal;
        $newWaktu = substr($request->waktu, 0, 5) . ':00';

        // Cek apakah ada jadwal bentrok di tanggal dan jam yang sama dengan booking lain
        $isConflict = Reservasi::where('id', '!=', $booking->id)
            ->where('tanggal', $newTanggal)
            ->where('waktu', $newWaktu)
            ->whereIn('status', ['Menunggu Konfirmasi', 'Disetujui', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal'])
            ->exists();

        if ($isConflict) {
            return back()->with('error', "Slot jadwal pada tanggal {$newTanggal} pukul {$request->waktu} WIB sudah terisi oleh sesi foto lain. Silakan pilih tanggal atau jam berbeda.");
        }

        $oldTanggal = $booking->tanggal;
        $oldWaktu = $booking->waktu;

        $booking->tanggal = $newTanggal;
        $booking->waktu = $newWaktu;
        if ($request->filled('status')) {
            $booking->status = $request->status;
        }
        $booking->save();

        // Kirim notifikasi ke pelanggan mengenai perubahan jadwal
        \App\Models\Notifikasi::create([
            'user_id' => $booking->user_id,
            'judul' => 'Jadwal Sesi Foto Diperbarui',
            'pesan' => "Jadwal reservasi {$booking->nomor_booking} Anda telah dijadwalkan ulang ke tanggal {$newTanggal} pukul " . substr($newWaktu, 0, 5) . " WIB.",
            'link' => route('customer.bookings.show', $booking->id),
        ]);

        return back()->with('success', "Jadwal sesi foto {$booking->nomor_booking} berhasil diperbarui ke tanggal {$newTanggal} ({$request->waktu} WIB).");
    }
}
