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

        return view('portofolio.index', compact('portofolio', 'categories', 'category'));
    }

    /**
     * Menampilkan daftar portofolio untuk Owner/Admin
     */
    public function adminIndex(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');

        $query = Portofolio::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('kategori', $category);
        }

        if ($status) {
            $query->where('status_publikasi', $status);
        }

        $portofolios = $query->latest('tanggal')->latest('id')->paginate(12)->withQueryString();

        // Kategori terdaftar dari data portofolio & kategori layanan aktif
        $categories = Portofolio::select('kategori')->distinct()->pluck('kategori')->filter()->values();
        $serviceCategories = \App\Models\KategoriLayanan::where('status', 'aktif')->pluck('nama');
        $allCategories = $categories->merge($serviceCategories)->unique()->sort()->values();

        return view('portofolio.admin', compact('portofolios', 'allCategories', 'search', 'category', 'status'));
    }

    /**
     * Menyimpan portofolio baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'status_publikasi' => 'required|in:published,draft,archived',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_url' => 'nullable|url|max:1000',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto_file')) {
            $fotoPath = $request->file('foto_file')->store('portofolio', 'public');
        } elseif (!empty($validated['foto_url'])) {
            $fotoPath = $validated['foto_url'];
        } else {
            return back()->withInput()->with('error', 'Silakan unggah berkas foto atau masukkan tautan URL gambar.');
        }

        Portofolio::create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'],
            'status_publikasi' => $validated['status_publikasi'],
            'foto' => $fotoPath,
        ]);

        return redirect()->route('admin.portofolio.index')->with('success', 'Karya portofolio berhasil ditambahkan dan siap tampil di galeri.');
    }

    /**
     * Memperbarui data portofolio
     */
    public function update(Request $request, $id)
    {
        $portofolio = Portofolio::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'status_publikasi' => 'required|in:published,draft,archived',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_url' => 'nullable|url|max:1000',
        ]);

        $fotoPath = $portofolio->foto;

        if ($request->hasFile('foto_file')) {
            // Hapus foto lama jika tersimpan di disk lokal storage
            if ($fotoPath && !str_starts_with($fotoPath, 'http') && \Illuminate\Support\Facades\Storage::disk('public')->exists($fotoPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_file')->store('portofolio', 'public');
        } elseif (!empty($validated['foto_url'])) {
            $fotoPath = $validated['foto_url'];
        }

        $portofolio->update([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'],
            'status_publikasi' => $validated['status_publikasi'],
            'foto' => $fotoPath,
        ]);

        return redirect()->route('admin.portofolio.index')->with('success', 'Data portofolio berhasil diperbarui.');
    }

    /**
     * Menghapus portofolio
     */
    public function destroy($id)
    {
        $portofolio = Portofolio::findOrFail($id);

        // Hapus file fisik jika ada di storage lokal
        if ($portofolio->foto && !str_starts_with($portofolio->foto, 'http') && \Illuminate\Support\Facades\Storage::disk('public')->exists($portofolio->foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($portofolio->foto);
        }

        $portofolio->delete();

        return redirect()->route('admin.portofolio.index')->with('success', 'Karya portofolio berhasil dihapus dari galeri.');
    }

    /**
     * Toggle status publikasi cepat (published <-> draft)
     */
    public function toggleStatus($id)
    {
        $portofolio = Portofolio::findOrFail($id);
        $newStatus = $portofolio->status_publikasi === 'published' ? 'draft' : 'published';
        $portofolio->update(['status_publikasi' => $newStatus]);

        return back()->with('success', "Status portofolio berhasil diubah menjadi: {$newStatus}");
    }
}
