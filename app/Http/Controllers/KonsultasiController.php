<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Konsultasi;
use App\Models\Notifikasi;
use App\Models\Katalog;
use App\Models\User;

/**
 * Controller Layanan Konsultasi Konsep Fotografi (ICONIX Robustness: KonsultasiController)
 * 
 * Bertanggung jawab atas:
 * - Pengajuan konsultasi konsep foto oleh calon klien/pelanggan
 * - Manajemen dan pemberian tanggapan resmi oleh fotografer/Admin
 */
class KonsultasiController extends Controller
{
    /**
     * Menampilkan halaman konsultasi publik
     */
    public function index()
    {
        $layananList = Katalog::where('status', 'aktif')->pluck('nama');
        if ($layananList->isEmpty()) {
            $layananList = collect([
                'Wedding & Prewedding',
                'Wisuda / Graduation',
                'Studio Portrait & Group',
                'Event & Gathering',
                'Produk & Komersial'
            ]);
        }

        $konsultasiList = Auth::check()
            ? Konsultasi::where('user_id', Auth::id())->latest()->get()
            : collect([]);

        return view('konsultasi.index', compact('konsultasiList', 'layananList'));
    }

    /**
     * Mengirim pesan konsultasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:25',
            'no_hp' => 'nullable|string|max:25',
            'jenis_layanan' => 'required|string|max:100',
            'konsep' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'kebutuhan' => 'nullable|string',
            'pesan' => 'nullable|string',
        ]);

        $phone = $request->input('phone') ?? $request->input('no_hp');
        if (empty($phone)) {
            return back()->withErrors(['phone' => 'Nomor WhatsApp / Telepon wajib diisi.'])->withInput();
        }

        $kebutuhan = $request->input('kebutuhan') ?? $request->input('pesan') ?? 'Konsultasi konsep pemotretan ' . $validated['jenis_layanan'];

        $konsultasi = Konsultasi::create([
            'user_id' => Auth::id(),
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'phone' => $phone,
            'jenis_layanan' => $validated['jenis_layanan'],
            'konsep' => $request->input('konsep'),
            'lokasi' => $request->input('lokasi'),
            'tanggal' => $request->input('tanggal'),
            'budget' => $request->input('budget'),
            'kebutuhan' => $kebutuhan,
            'percakapan' => [
                [
                    'sender' => 'pelanggan',
                    'nama' => $validated['nama'],
                    'pesan' => $kebutuhan,
                    'waktu' => now()->format('d M Y, H:i'),
                ]
            ],
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke Owner jika terdaftar
        $owner = User::where('role', 'owner')->first();
        if ($owner) {
            Notifikasi::create([
                'user_id' => $owner->id,
                'judul' => 'Konsultasi Baru Masuk',
                'pesan' => 'Konsultasi konsep foto dari ' . $konsultasi->nama . ' (' . $konsultasi->jenis_layanan . ').',
                'link' => '/admin/consultations',
            ]);
        }

        return back()->with('success', 'Permintaan konsultasi Anda berhasil dikirim! Tim RanggaMotret akan segera menghubungi Anda.');
    }

    /**
     * Daftar seluruh konsultasi di panel Admin
     */
    public function adminIndex(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Konsultasi::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('jenis_layanan', 'like', "%{$search}%")
                  ->orWhere('konsep', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $consultations = $query->latest()->paginate(15)->withQueryString();

        // Metrik statistik konsultasi
        $totalConsultations = Konsultasi::count();
        $countPending = Konsultasi::where('status', 'pending')->count();
        $countReplied = Konsultasi::where('status', 'replied')->count();

        return view('konsultasi.admin', compact(
            'consultations',
            'search',
            'status',
            'totalConsultations',
            'countPending',
            'countReplied'
        ));
    }

    /**
     * Owner memberikan tanggapan / balasan ke dalam percakapan konsultasi
     */
    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'tanggapan_admin' => 'nullable|string',
            'pesan' => 'nullable|string',
        ]);

        $replyMessage = $request->input('pesan') ?: $request->input('tanggapan_admin');
        if (empty(trim((string)$replyMessage))) {
            return back()->withErrors(['pesan' => 'Pesan tanggapan wajib diisi.'])->withInput();
        }

        $konsultasi = Konsultasi::findOrFail($id);
        $konsultasi->tanggapan_admin = $replyMessage;
        $konsultasi->addMessage('owner', 'Owner RanggaMotret', $replyMessage);
        $konsultasi->status = 'replied';
        $konsultasi->save();

        if ($konsultasi->user_id) {
            Notifikasi::create([
                'user_id' => $konsultasi->user_id,
                'judul' => 'Tanggapan Konsultasi RanggaMotret',
                'pesan' => 'Owner RanggaMotret mengirimkan tanggapan baru mengenai ' . $konsultasi->jenis_layanan . '.',
                'link' => '/customer/consultations',
            ]);
        }

        return back()->with('success', 'Tanggapan konsultasi berhasil dikirim.');
    }

    /**
     * Pelanggan mengirimkan balasan ke dalam percakapan konsultasi
     */
    public function customerReply(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string|max:2000',
        ]);

        $konsultasi = Konsultasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $konsultasi->addMessage('pelanggan', Auth::user()->name, $request->pesan);
        $konsultasi->status = 'pending';
        $konsultasi->save();

        // Notifikasi ke Owner
        $owner = User::where('role', 'owner')->first();
        if ($owner) {
            Notifikasi::create([
                'user_id' => $owner->id,
                'judul' => 'Balasan Konsultasi dari Pelanggan',
                'pesan' => Auth::user()->name . ' membalas pesan konsultasi ' . $konsultasi->jenis_layanan . '.',
                'link' => '/admin/consultations',
            ]);
        }

        return back()->with('success', 'Balasan Anda berhasil dikirim ke Owner.');
    }

    /**
     * Tampilan Riwayat Konsultasi untuk Pelanggan di Dashboard Portal
     */
    public function customerIndex()
    {
        $consultations = Konsultasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $layananList = Katalog::where('status', 'aktif')->pluck('nama');
        if ($layananList->isEmpty()) {
            $layananList = collect([
                'Wedding & Prewedding',
                'Wisuda / Graduation',
                'Studio Portrait & Group',
                'Event & Gathering',
                'Produk & Komersial'
            ]);
        }

        $stats = [
            'total' => Konsultasi::where('user_id', Auth::id())->count(),
            'pending' => Konsultasi::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'replied' => Konsultasi::where('user_id', Auth::id())->where('status', 'replied')->count(),
        ];

        return view('konsultasi.customer', compact('consultations', 'layananList', 'stats'));
    }
}
