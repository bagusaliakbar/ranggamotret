<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portofolio;

/**
 * Controller Galeri Portofolio Hasil Karya Foto
 * 
 * Merepresentasikan Control Object 'PortofolioController' pada Robustness Diagram.
 * Terhubung dengan boundary 'portofolio' dan entity 'Portofolio'.
 */
class PortofolioController extends Controller
{
    /**
     * Menampilkan galeri kurasi portofolio fotografi & modal lightbox
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = Portofolio::where('status_publikasi', 'published');

        if ($category) {
            $query->where('kategori', $category);
        }

        $portofolio = $query->latest()->get();
        $categories = Portofolio::select('kategori')->distinct()->pluck('kategori');

        return view('public.portfolio', compact('portofolio', 'categories', 'category'));
    }
}
