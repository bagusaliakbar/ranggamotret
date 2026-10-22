<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Konsultasi;
use App\Models\Notifikasi;

/**
 * Controller Layanan Konsultasi Konsep Fotografi (ICONIX Robustness: KonsultasiController)
 * 
 * Bertanggung jawab atas:
 * - Pengajuan konsultasi konsep foto oleh calon klien/member
 * - Manajemen dan pemberian tanggapan resmi oleh fotografer/Admin
 */
class KonsultasiController extends Controller
{
    /**
     * Menampilkan halaman konsultasi publik
     */
    public function index()
    {
        $layananList = \App\Models\Katalog::where('status', 'aktif')->pluck('nama');
        if ($layananList->isEmpty()) {
            $layananList = collect(['Wedding & Prewedding', 'Wisuda / Graduation', 'Studio Portrait & Group', 'Event & Gathering', 'Produk & Komersial']);
        }

        $konsultasiList = Auth::check()
            ? Konsultasi::where('user_id', Auth::id())->latest()->get()
            : collect([]);

        return view('public.consultation', compact('konsultasiList', 'layananList'));
    }

    /**
     * Mengirim pesan konsultasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'jenis_layanan' => 'required|string|max:100',
            'pesan' => 'required|string',
        ]);

        $konsultasi = Konsultasi::create([
            'user_id' => Auth::id(),
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'jenis_layanan' => $validated['jenis_layanan'],
            'pesan' => $validated['pesan'],
            'status' => 'pending',
        ]);

        Notifikasi::create([
            'user_id' => 1,
            'judul' => 'Konsultasi Baru Masuk',
            'pesan' => 'Konsultasi konsep foto dari ' . $konsultasi->nama . ' (' . $konsultasi->jenis_layanan . ').',
            'link' => '/admin/consultations',
        ]);

        return back()->with('success', 'Pesan konsultasi Anda telah dikirim! Tim RanggaMotret akan segera menghubungi Anda.');
    }

    /**
     * Daftar seluruh konsultasi di panel Admin
     */
    public function adminIndex()
    {
        $consultations = Konsultasi::latest()->paginate(15);
        return view('admin.consultations.index', compact('consultations'));
    }

    /**
     * Admin memberikan balasan konsultasi
     */
    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'tanggapan_admin' => 'required|string',
        ]);

        $konsultasi = Konsultasi::findOrFail($id);
        $konsultasi->tanggapan_admin = $request->tanggapan_admin;
        $konsultasi->status = 'replied';
        $konsultasi->save();

        if ($konsultasi->user_id) {
            Notifikasi::create([
                'user_id' => $konsultasi->user_id,
                'judul' => 'Tanggapan Konsultasi RanggaMotret',
                'pesan' => 'Konsultasi Anda mengenai layanan ' . $konsultasi->jenis_layanan . ' telah ditanggapi oleh admin.',
                'link' => '/customer/dashboard',
            ]);
        }

        return back()->with('success', 'Tanggapan konsultasi berhasil dikirim.');
    }
}
