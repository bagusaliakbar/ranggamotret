<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\Layanan;
use App\Models\KategoriLayanan;
use App\Models\Portofolio;

/**
 * Controller Katalog Layanan & Paket Fotografi
 * 
 * Merepresentasikan Control Object 'KatalogController' pada Robustness Diagram.
 * Terhubung dengan boundary 'katalog' dan entity 'Katalog' (Layanan & Paket).
 */
class KatalogController extends Controller
{
    /**
     * Menampilkan daftar katalog layanan fotografi
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = Katalog::aktif()->with(['pakets' => fn($q) => $q->where('status', 'aktif')]);

        if ($category) {
            $query->where('kategori', $category);
        }

        $layanan = $query->get();
        $categories = KategoriLayanan::where('status', 'aktif')->pluck('nama');
        if ($categories->isEmpty()) {
            $categories = Katalog::aktif()->select('kategori')->distinct()->pluck('kategori');
        }

        return view('public.services', compact('layanan', 'categories', 'category'));
    }

    /**
     * Menampilkan rincian paket katalog layanan fotografi
     */
    public function detail($id)
    {
        $layanan = Katalog::aktif()
            ->with(['pakets' => fn($q) => $q->where('status', 'aktif')])
            ->findOrFail($id);

        $otherServices = Katalog::aktif()
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

        return view('public.service-detail', compact('layanan', 'otherServices', 'relatedPortofolio'));
    }
}
