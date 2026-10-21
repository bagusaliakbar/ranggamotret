<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriLayananController extends Controller
{
    /**
     * Display a listing of service categories.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $query = KategoriLayanan::withCount('layanan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $categories = $query->orderBy('nama', 'asc')->get();

        // Metrics
        $totalKategori = KategoriLayanan::count();
        $kategoriAktif = KategoriLayanan::where('status', 'aktif')->count();
        $totalLayananConnected = Layanan::count();

        // Standard FontAwesome Icon Presets for UI picker
        $iconPresets = [
            'fa-ring' => 'Cincin / Wedding',
            'fa-heart' => 'Hati / Romance',
            'fa-gem' => 'Permata / Engagement',
            'fa-graduation-cap' => 'Toga / Wisuda',
            'fa-box-open' => 'Produk / Commercial',
            'fa-user' => 'Portrait / Individu',
            'fa-users' => 'Grup / Family',
            'fa-calendar-check' => 'Event / Acara',
            'fa-building' => 'Building / Architecture',
            'fa-cake-candles' => 'Ulang Tahun / Party',
            'fa-baby' => 'Maternity & Bayi',
            'fa-camera-retro' => 'Klasik / Seni',
            'fa-vest-patches' => 'Fashion / Model',
            'fa-utensils' => 'Kuliner / Food',
            'fa-car' => 'Otomotif / Vehicle',
            'fa-plane' => 'Travel / Wisata',
        ];

        return view('admin.kategori-layanan.index', compact(
            'categories',
            'search',
            'status',
            'totalKategori',
            'kategoriAktif',
            'totalLayananConnected',
            'iconPresets'
        ));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_layanan,nama',
            'slug' => 'nullable|string|max:120|unique:kategori_layanan,slug',
            'icon' => 'nullable|string|max:60',
            'deskripsi' => 'nullable|string|max:500',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah terdaftar.',
            'slug.unique' => 'Slug kategori sudah digunakan.',
        ]);

        $slug = $request->filled('slug') 
            ? Str::slug($request->input('slug')) 
            : Str::slug($request->input('nama'));

        KategoriLayanan::create([
            'nama' => trim($request->input('nama')),
            'slug' => $slug,
            'icon' => $request->input('icon', 'fa-camera'),
            'deskripsi' => $request->input('deskripsi'),
            'status' => $request->input('status', 'aktif'),
        ]);

        return redirect()->route('admin.kategori-layanan.index')
            ->with('success', "Kategori '{$request->input('nama')}' berhasil ditambahkan!");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = KategoriLayanan::findOrFail($id);

        $request->validate([
            'nama' => "required|string|max:100|unique:kategori_layanan,nama,{$id}",
            'slug' => "nullable|string|max:120|unique:kategori_layanan,slug,{$id}",
            'icon' => 'nullable|string|max:60',
            'deskripsi' => 'nullable|string|max:500',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah terdaftar.',
            'slug.unique' => 'Slug kategori sudah digunakan.',
        ]);

        $oldName = $category->nama;
        $newName = trim($request->input('nama'));

        $slug = $request->filled('slug') 
            ? Str::slug($request->input('slug')) 
            : Str::slug($newName);

        $category->update([
            'nama' => $newName,
            'slug' => $slug,
            'icon' => $request->input('icon', 'fa-camera'),
            'deskripsi' => $request->input('deskripsi'),
            'status' => $request->input('status', 'aktif'),
        ]);

        // Cascade update in Layanan table if category name changed
        if ($oldName !== $newName) {
            Layanan::where('kategori', $oldName)->update(['kategori' => $newName]);
        }

        return redirect()->route('admin.kategori-layanan.index')
            ->with('success', "Kategori '{$newName}' berhasil diperbarui!");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $category = KategoriLayanan::findOrFail($id);

        // Check if any services are currently attached
        $layananCount = $category->layanan()->count();
        if ($layananCount > 0) {
            return redirect()->route('admin.kategori-layanan.index')
                ->with('error', "Kategori '{$category->nama}' tidak dapat dihapus karena masih digunakan oleh {$layananCount} layanan. Silakan ubah atau hapus layanan terkait terlebih dahulu.");
        }

        $nama = $category->nama;
        $category->delete();

        return redirect()->route('admin.kategori-layanan.index')
            ->with('success', "Kategori '{$nama}' berhasil dihapus!");
    }

    /**
     * Toggle active/inactive status of a category.
     */
    public function toggleStatus($id)
    {
        $category = KategoriLayanan::findOrFail($id);
        $newStatus = ($category->status === 'aktif') ? 'nonaktif' : 'aktif';
        $category->update(['status' => $newStatus]);

        return redirect()->route('admin.kategori-layanan.index')
            ->with('success', "Status kategori '{$category->nama}' diubah menjadi " . strtoupper($newStatus) . ".");
    }
}
