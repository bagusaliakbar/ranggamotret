<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konsultasi;
use App\Models\Notifikasi;

class ConsultationController extends Controller
{
    // Admin list consultations
    public function adminIndex()
    {
        $consultations = Konsultasi::latest()->paginate(15);
        return view('admin.consultations.index', compact('consultations'));
    }

    // Admin reply to consultation
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
