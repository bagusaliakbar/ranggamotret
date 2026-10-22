<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\Paket;
use App\Models\Portofolio;
use App\Models\Kursus;
use App\Models\Testimoni;
use App\Models\Konsultasi;
use App\Models\Sertifikat;

class PublicController extends Controller
{
    public function home()
    {
        $layanan = Katalog::where('status', 'aktif')->take(6)->get();
        $portofolio = Portofolio::where('status_publikasi', 'published')->take(6)->get();
        $kursus = Kursus::where('status', 'aktif')->take(3)->get();
        $testimoni = Testimoni::where('status', 'approved')->with('user')->take(6)->get();

        return view('public.home', compact('layanan', 'portofolio', 'kursus', 'testimoni'));
    }

    public function services(Request $request)
    {
        $category = $request->query('category');
        $query = Katalog::where('status', 'aktif')->with(['pakets' => fn($q) => $q->where('status', 'aktif')]);

        if ($category) {
            $query->where('kategori', $category);
        }

        $layanan = $query->get();
        $categories = \App\Models\KategoriLayanan::where('status', 'aktif')->pluck('nama');
        if ($categories->isEmpty()) {
            $categories = Katalog::where('status', 'aktif')->select('kategori')->distinct()->pluck('kategori');
        }

        return view('katalog.index', compact('layanan', 'categories', 'category'));
    }

    public function serviceDetail($id)
    {
        $layanan = Katalog::where('status', 'aktif')
            ->with(['pakets' => fn($q) => $q->where('status', 'aktif')])
            ->findOrFail($id);

        $otherServices = Katalog::where('status', 'aktif')
            ->where('id', '!=', $layanan->id)
            ->take(3)
            ->get();

        $relatedPortofolio = Portofolio::where('status_publikasi', 'published')
            ->where('kategori', $layanan->kategori)
            ->take(4)
            ->get();

        if ($relatedPortofolio->isEmpty()) {
            $relatedPortofolio = Portofolio::where('status_publikasi', 'published')
                ->take(4)
                ->get();
        }

        return view('katalog.detail', compact('layanan', 'otherServices', 'relatedPortofolio'));
    }

    public function portfolio(Request $request)
    {
        $category = $request->query('category');
        $query = Portofolio::where('status_publikasi', 'published');

        if ($category) {
            $query->where('kategori', $category);
        }

        $portofolio = $query->latest()->get();
        $categories = Portofolio::select('kategori')->distinct()->pluck('kategori');

        return view('portofolio.index', compact('portofolio', 'categories', 'category'));
    }

    public function courses(Request $request)
    {
        $level = $request->query('level');
        $query = Kursus::where('status', 'aktif')->with('instruktur');

        if ($level) {
            $query->where('level', $level);
        }

        $kursus = $query->get();

        return view('kursus.index', compact('kursus', 'level'));
    }

    public function courseDetail($id)
    {
        $kursus = Kursus::where('status', 'aktif')
            ->with(['instruktur', 'moduls.materis', 'moduls.tugas', 'moduls.quizs'])
            ->findOrFail($id);

        return view('kursus.detail', compact('kursus'));
    }

    public function consultation()
    {
        $layananList = Katalog::where('status', 'aktif')->pluck('nama');
        return view('konsultasi.index', compact('layananList'));
    }

    public function storeConsultation(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'jenis_layanan' => 'required|string',
            'konsep' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'kebutuhan' => 'nullable|string',
        ]);

        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        Konsultasi::create($validated);

        return back()->with('success', 'Permintaan konsultasi Anda berhasil dikirim! Tim RanggaMotret akan segera menghubungi Anda.');
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function verifyCertificate($code)
    {
        $sertifikat = Sertifikat::where('kode_verifikasi', $code)
            ->orWhere('nomor_sertifikat', $code)
            ->with(['user', 'kursus.instruktur'])
            ->first();

        return view('public.verify-certificate', compact('sertifikat', 'code'));
    }
}
