<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Konsultasi;
use App\Models\Layanan;
use App\Models\Notifikasi;

/**
 * Controller Layanan Konsultasi Fotografer
 * 
 * Merepresentasikan Control Object 'KonsultasiController' pada Robustness Diagram.
 * Terhubung dengan boundary 'konsultasi' dan entity 'Konsultasi'.
 */
class KonsultasiController extends ConsultationController
{
    /**
     * Menampilkan form konsultasi konsep foto
     */
    public function index()
    {
        $layananList = Layanan::where('status', 'aktif')->pluck('nama');
        return view('public.consultation', compact('layananList'));
    }

    /**
     * Menyimpan formulir konsultasi dari calon klien
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'jenis_layanan' => 'required|string',
            'estimasi_tanggal' => 'nullable|date',
            'budget' => 'nullable|string',
            'pesan' => 'required|string',
        ]);

        $konsultasi = Konsultasi::create([
            'user_id' => Auth::id(),
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'jenis_layanan' => $validated['jenis_layanan'],
            'estimasi_tanggal' => $validated['estimasi_tanggal'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'pesan' => $validated['pesan'],
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke Admin
        Notifikasi::create([
            'user_id' => 1,
            'judul' => 'Konsultasi Klien Baru',
            'pesan' => 'Pertanyaan baru dari ' . $validated['nama'] . ' untuk layanan ' . $validated['jenis_layanan'],
            'link' => '/admin/consultations',
        ]);

        return back()->with('success', 'Pesan konsultasi Anda telah berhasil dikirim! Tim kami akan segera menghubungi via WhatsApp/Email.');
    }
}
