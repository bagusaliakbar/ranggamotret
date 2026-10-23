<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portofolio;
use App\Models\PortofolioFoto;
use Illuminate\Support\Facades\Storage;

/**
 * Controller Galeri Portofolio & Album Hasil Karya Foto
 * 
 * Merepresentasikan Control Object 'PortofolioController' pada Robustness Diagram.
 * Terhubung dengan boundary 'portofolio' dan entity 'Portofolio'.
 */
class PortofolioController extends Controller
{
    /**
     * Menampilkan daftar album kurasi portofolio fotografi untuk publik
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = Portofolio::with(['fotos'])->where('status_publikasi', 'published');

        if ($category) {
            $query->where('kategori', $category);
        }

        $portofolio = $query->latest('tanggal')->latest('id')->get();
        $categories = Portofolio::where('status_publikasi', 'published')->select('kategori')->distinct()->pluck('kategori');

        return view('portofolio.index', compact('portofolio', 'categories', 'category'));
    }

    /**
     * Menampilkan detail isi album portofolio beserta seluruh fotonya (Publik)
     */
    public function show($id)
    {
        $album = Portofolio::with(['fotos'])->where('status_publikasi', 'published')->findOrFail($id);
        $relatedAlbums = Portofolio::with(['fotos'])
            ->where('status_publikasi', 'published')
            ->where('id', '!=', $album->id)
            ->where('kategori', $album->kategori)
            ->latest('tanggal')
            ->take(3)
            ->get();

        return view('portofolio.show', compact('album', 'relatedAlbums'));
    }

    /**
     * Menampilkan daftar album portofolio untuk Owner/Admin
     */
    public function adminIndex(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');

        $query = Portofolio::withCount('fotos');

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

        // Metrik statistik
        $totalAlbums = Portofolio::count();
        $publishedAlbums = Portofolio::where('status_publikasi', 'published')->count();
        $draftAlbums = Portofolio::where('status_publikasi', 'draft')->count();
        $totalPhotos = PortofolioFoto::count() + Portofolio::whereNotNull('foto')->count();

        return view('portofolio.admin', compact(
            'portofolios', 
            'allCategories', 
            'search', 
            'category', 
            'status',
            'totalAlbums',
            'publishedAlbums',
            'draftAlbums',
            'totalPhotos'
        ));
    }

    /**
     * Menampilkan kelola detail foto di dalam album untuk Owner/Admin
     */
    public function adminShow($id)
    {
        $portofolio = Portofolio::with(['fotos'])->findOrFail($id);
        $album = $portofolio;
        return view('portofolio.admin-show', compact('album', 'portofolio'));
    }

    /**
     * Menyimpan album portofolio baru (bisa menyertakan foto sampul & multiple foto)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'status_publikasi' => 'required|in:published,draft,archived',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_url' => 'nullable|url|max:1000',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto_file')) {
            $fotoPath = $request->file('foto_file')->store('portofolio', 'public');
        } elseif (!empty($validated['foto_url'])) {
            $fotoPath = $validated['foto_url'];
        }

        // Jika cover tidak diunggah tapi ada file galeri, jadikan foto galeri pertama sebagai cover
        if (!$fotoPath && $request->hasFile('gallery_files') && count($request->file('gallery_files')) > 0) {
            $fotoPath = $request->file('gallery_files')[0]->store('portofolio', 'public');
        }

        if (!$fotoPath) {
            return back()->withInput()->with('error', 'Silakan pilih foto sampul album atau unggah foto galeri.');
        }

        $portofolio = Portofolio::create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'],
            'status_publikasi' => $validated['status_publikasi'],
            'foto' => $fotoPath,
        ]);

        // Simpan multiple foto galeri jika ada
        if ($request->hasFile('gallery_files')) {
            $urutan = 1;
            foreach ($request->file('gallery_files') as $file) {
                $path = $file->store('portofolio/album_' . $portofolio->id, 'public');
                PortofolioFoto::create([
                    'portofolio_id' => $portofolio->id,
                    'foto' => $path,
                    'urutan' => $urutan++,
                ]);
            }
        }

        return redirect()->route('admin.portofolio.show', $portofolio->id)->with('success', 'Album portofolio berhasil dibuat. Anda dapat menambah atau mengelola foto di dalamnya.');
    }

    /**
     * Memperbarui informasi album portofolio
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
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_url' => 'nullable|url|max:1000',
        ]);

        $fotoPath = $portofolio->foto;

        if ($request->hasFile('foto_file')) {
            if ($fotoPath && !str_starts_with($fotoPath, 'http') && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
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

        return back()->with('success', 'Informasi album portofolio berhasil diperbarui.');
    }

    /**
     * Unggah foto-foto tambahan ke dalam album
     */
    public function uploadPhotos(Request $request, $id)
    {
        $portofolio = Portofolio::findOrFail($id);

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        $currentMaxUrutan = $portofolio->fotos()->max('urutan') ?? 0;

        foreach ($request->file('photos') as $file) {
            $currentMaxUrutan++;
            $path = $file->store('portofolio/album_' . $portofolio->id, 'public');
            PortofolioFoto::create([
                'portofolio_id' => $portofolio->id,
                'foto' => $path,
                'caption' => $request->caption,
                'urutan' => $currentMaxUrutan,
            ]);
        }

        return back()->with('success', 'Foto-foto berhasil diunggah ke album.');
    }

    /**
     * Menghapus salah satu foto dari album
     */
    public function destroyPhoto($photoId)
    {
        $foto = PortofolioFoto::findOrFail($photoId);

        if ($foto->foto && !str_starts_with($foto->foto, 'http') && Storage::disk('public')->exists($foto->foto)) {
            Storage::disk('public')->delete($foto->foto);
        }

        $foto->delete();

        return back()->with('success', 'Foto berhasil dihapus dari album.');
    }

    /**
     * Menghapus album portofolio beserta seluruh foto di dalamnya
     */
    public function destroy($id)
    {
        $portofolio = Portofolio::with('fotos')->findOrFail($id);

        // Hapus file cover
        if ($portofolio->foto && !str_starts_with($portofolio->foto, 'http') && Storage::disk('public')->exists($portofolio->foto)) {
            Storage::disk('public')->delete($portofolio->foto);
        }

        // Hapus seluruh file foto galeri album
        foreach ($portofolio->fotos as $f) {
            if ($f->foto && !str_starts_with($f->foto, 'http') && Storage::disk('public')->exists($f->foto)) {
                Storage::disk('public')->delete($f->foto);
            }
        }

        $portofolio->delete();

        return redirect()->route('admin.portofolio.index')->with('success', 'Album portofolio beserta seluruh fotonya berhasil dihapus.');
    }

    /**
     * Toggle status publikasi cepat (published <-> draft)
     */
    public function toggleStatus($id)
    {
        $portofolio = Portofolio::findOrFail($id);
        $newStatus = $portofolio->status_publikasi === 'published' ? 'draft' : 'published';
        $portofolio->update(['status_publikasi' => $newStatus]);

        return back()->with('success', "Status album portofolio berhasil diubah menjadi: {$newStatus}");
    }
}

