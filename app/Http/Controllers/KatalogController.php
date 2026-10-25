<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\Paket;
use App\Models\KategoriLayanan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Controller Manajemen Katalog Layanan & Paket Fotografi (ICONIX Robustness: KatalogController)
 * 
 * Bertanggung jawab atas:
 * - Eksplorasi katalog layanan fotografi publik
 * - Halaman rincian paket dan spesifikasi pemotretan
 * - Manajemen inventaris layanan, paket harga, dan kategori oleh Admin
 */
class KatalogController extends Controller
{
    // ==========================================
    // 1. PUBLIC KATALOG (Boundary: katalog)
    // ==========================================

    /**
     * Menampilkan katalog layanan fotografi untuk publik
     */
    public function index(Request $request)
    {
        $kategori = $request->query('kategori') ?? $request->query('category');
        $category = $kategori;
        $query = Katalog::with(['pakets' => fn($q) => $q->where('status', 'aktif')])->where('status', 'aktif');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $layanan = $query->latest()->get();
        $categories = Katalog::where('status', 'aktif')->select('kategori')->distinct()->pluck('kategori');

        return view('katalog.index', compact('layanan', 'categories', 'kategori', 'category'));
    }

    /**
     * Menampilkan rincian layanan fotografi publik
     */
    public function detail($id)
    {
        $layanan = Katalog::with(['pakets' => fn($q) => $q->where('status', 'aktif')])
            ->where('status', 'aktif')
            ->findOrFail($id);

        $otherServices = Katalog::where('status', 'aktif')
            ->where('id', '!=', $id)
            ->take(3)
            ->get();

        return view('katalog.detail', compact('layanan', 'otherServices'));
    }

    // ==========================================
    // 2. ADMIN KATALOG LAYANAN (Boundary: katalog kelola)
    // ==========================================

    /**
     * Daftar seluruh layanan di panel admin dengan metrik dan filter
     */
    public function adminIndex(Request $request)
    {
        $search = $request->query('search');
        $kategori = $request->query('kategori');
        $status = $request->query('status');

        $query = Katalog::with(['pakets'])->withCount('bookings');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($kategori && $kategori !== 'all') {
            $query->where('kategori', $kategori);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $layananList = $query->latest()->get();

        $totalLayanan = Katalog::count();
        $layananAktif = Katalog::where('status', 'aktif')->count();
        $layananNonaktif = Katalog::where('status', 'nonaktif')->count();
        $totalPaket = Paket::count();
        $paketAktif = Paket::where('status', 'aktif')->count();

        $dbCategories = KategoriLayanan::orderBy('nama')->pluck('nama');
        $existingCategories = Katalog::select('kategori')->distinct()->pluck('kategori');
        $categories = $dbCategories->merge($existingCategories)->unique()->filter()->sort()->values();

        return view('katalog.admin', compact(
            'layananList',
            'totalLayanan',
            'layananAktif',
            'layananNonaktif',
            'totalPaket',
            'paketAktif',
            'categories',
            'search',
            'kategori',
            'status'
        ));
    }

    /**
     * Menyimpan layanan fotografi baru oleh Admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'kategori_custom' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga_mulai' => 'required|numeric|min:0',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'thumbnail_url' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $finalKategori = ($validated['kategori'] === 'custom' && !empty($validated['kategori_custom']))
            ? trim($validated['kategori_custom'])
            : $validated['kategori'];

        $kategoriId = null;
        if (!empty($finalKategori)) {
            $catRecord = KategoriLayanan::firstOrCreate(
                ['nama' => $finalKategori],
                ['slug' => Str::slug($finalKategori), 'icon' => 'fa-camera', 'status' => 'aktif']
            );
            $kategoriId = $catRecord->id;
        }

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $uploadDir = public_path('uploads/layanan');
            
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $thumbnailPath = '/uploads/layanan/' . $filename;
        } elseif (!empty($validated['thumbnail_url'])) {
            $thumbnailPath = trim($validated['thumbnail_url']);
        } else {
            $thumbnailPath = 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80';
        }

        $layanan = Katalog::create([
            'nama' => $validated['nama'],
            'kategori_layanan_id' => $kategoriId,
            'kategori' => $finalKategori,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga_mulai' => $validated['harga_mulai'],
            'thumbnail' => $thumbnailPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan fotografi "' . $layanan->nama . '" berhasil ditambahkan.');
    }

    /**
     * Memperbarui data layanan fotografi oleh Admin
     */
    public function update(Request $request, $id)
    {
        $layanan = Katalog::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'kategori_custom' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga_mulai' => 'required|numeric|min:0',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'thumbnail_url' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $finalKategori = ($validated['kategori'] === 'custom' && !empty($validated['kategori_custom']))
            ? trim($validated['kategori_custom'])
            : $validated['kategori'];

        $kategoriId = null;
        if (!empty($finalKategori)) {
            $catRecord = KategoriLayanan::firstOrCreate(
                ['nama' => $finalKategori],
                ['slug' => Str::slug($finalKategori), 'icon' => 'fa-camera', 'status' => 'aktif']
            );
            $kategoriId = $catRecord->id;
        }

        $thumbnailPath = $layanan->thumbnail;

        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $uploadDir = public_path('uploads/layanan');
            
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            if ($layanan->thumbnail && str_starts_with($layanan->thumbnail, '/uploads/layanan/')) {
                $oldFile = public_path(ltrim($layanan->thumbnail, '/'));
                if (File::exists($oldFile)) {
                    File::delete($oldFile);
                }
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $thumbnailPath = '/uploads/layanan/' . $filename;
        } elseif (!empty($validated['thumbnail_url'])) {
            $thumbnailPath = trim($validated['thumbnail_url']);
        }

        $layanan->update([
            'nama' => $validated['nama'],
            'kategori_layanan_id' => $kategoriId,
            'kategori' => $finalKategori,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga_mulai' => $validated['harga_mulai'],
            'thumbnail' => $thumbnailPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan fotografi "' . $layanan->nama . '" berhasil diperbarui.');
    }

    /**
     * Menghapus layanan fotografi
     */
    public function destroy($id)
    {
        $layanan = Katalog::withCount('bookings')->findOrFail($id);
        $nama = $layanan->nama;
        $totalBookings = $layanan->bookings_count;

        if ($layanan->thumbnail && str_starts_with($layanan->thumbnail, '/uploads/layanan/')) {
            $oldFile = public_path(ltrim($layanan->thumbnail, '/'));
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }

        $layanan->delete();

        $message = 'Layanan "' . $nama . '" beserta seluruh paketnya berhasil dihapus.';
        if ($totalBookings > 0) {
            $message .= ' (' . $totalBookings . ' data riwayat booking terkait telah disesuaikan).';
        }

        return redirect()->route('admin.layanan.index')->with('success', $message);
    }

    /**
     * Toggle status aktif/nonaktif layanan
     */
    public function toggleStatus($id)
    {
        $layanan = Katalog::findOrFail($id);
        $layanan->status = $layanan->status === 'aktif' ? 'nonaktif' : 'aktif';
        $layanan->save();

        $statusLabel = $layanan->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.layanan.index')->with('success', 'Status layanan "' . $layanan->nama . '" berhasil ' . $statusLabel . '.');
    }

    // ==========================================
    // 3. PAKET LAYANAN MANAGEMENT
    // ==========================================

    public function storePaket(Request $request, $layananId)
    {
        $layanan = Katalog::findOrFail($layananId);

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|string|max:100',
            'jumlah_fotografer' => 'required|integer|min:1',
            'jumlah_foto' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['layanan_id'] = $layanan->id;
        $paket = Paket::create($validated);

        if ($layanan->harga_mulai == 0 || $paket->harga < $layanan->harga_mulai) {
            $layanan->update(['harga_mulai' => $paket->harga]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Paket "' . $paket->nama_paket . '" berhasil ditambahkan ke layanan ' . $layanan->nama . '.');
    }

    public function updatePaket(Request $request, $id)
    {
        $paket = Paket::with('layanan')->findOrFail($id);

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|string|max:100',
            'jumlah_fotografer' => 'required|integer|min:1',
            'jumlah_foto' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $paket->update($validated);

        $minPrice = Paket::where('layanan_id', $paket->layanan_id)->where('status', 'aktif')->min('harga');
        if ($minPrice !== null && $paket->layanan) {
            $paket->layanan->update(['harga_mulai' => $minPrice]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Paket "' . $paket->nama_paket . '" berhasil diperbarui.');
    }

    public function destroyPaket($id)
    {
        $paket = Paket::with('layanan')->findOrFail($id);
        $namaPaket = $paket->nama_paket;
        $layananId = $paket->layanan_id;
        $layanan = $paket->layanan;

        $paket->delete();

        if ($layanan) {
            $minPrice = Paket::where('layanan_id', $layananId)->where('status', 'aktif')->min('harga');
            $layanan->update(['harga_mulai' => $minPrice ?? 0]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Paket "' . $namaPaket . '" berhasil dihapus.');
    }

    public function togglePaketStatus($id)
    {
        $paket = Paket::with('layanan')->findOrFail($id);
        $paket->status = $paket->status === 'aktif' ? 'nonaktif' : 'aktif';
        $paket->save();

        if ($paket->layanan) {
            $minPrice = Paket::where('layanan_id', $paket->layanan_id)->where('status', 'aktif')->min('harga');
            $paket->layanan->update(['harga_mulai' => $minPrice ?? 0]);
        }

        $statusLabel = $paket->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.layanan.index')->with('success', 'Status paket "' . $paket->nama_paket . '" berhasil ' . $statusLabel . '.');
    }

    // ==========================================
    // 4. KATEGORI LAYANAN MANAGEMENT
    // ==========================================

    public function kategoriIndex(Request $request)
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

        $totalKategori = KategoriLayanan::count();
        $kategoriAktif = KategoriLayanan::where('status', 'aktif')->count();
        $totalLayananConnected = Katalog::count();

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

        return view('katalog.kategori', compact(
            'categories',
            'search',
            'status',
            'totalKategori',
            'kategoriAktif',
            'totalLayananConnected',
            'iconPresets'
        ));
    }

    public function kategoriStore(Request $request)
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

    public function kategoriUpdate(Request $request, $id)
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

        if ($oldName !== $newName) {
            Katalog::where('kategori', $oldName)
                ->orWhere('kategori_layanan_id', $category->id)
                ->update([
                    'kategori' => $newName,
                    'kategori_layanan_id' => $category->id,
                ]);
        }

        return redirect()->route('admin.kategori-layanan.index')
            ->with('success', "Kategori '{$newName}' berhasil diperbarui!");
    }

    public function kategoriDestroy($id)
    {
        $category = KategoriLayanan::findOrFail($id);

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

    public function kategoriToggleStatus($id)
    {
        $category = KategoriLayanan::findOrFail($id);
        $newStatus = ($category->status === 'aktif') ? 'nonaktif' : 'aktif';
        $category->update(['status' => $newStatus]);

        return redirect()->route('admin.kategori-layanan.index')
            ->with('success', "Status kategori '{$category->nama}' diubah menjadi " . strtoupper($newStatus) . ".");
    }
}
