<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Paket;
use App\Models\KategoriLayanan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    /**
     * Display a listing of the photography services with filtering and metrics.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategori = $request->query('kategori');
        $status = $request->query('status');

        $query = Layanan::with(['pakets'])->withCount('bookings');

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

        // Statistical Metrics
        $totalLayanan = Layanan::count();
        $layananAktif = Layanan::where('status', 'aktif')->count();
        $layananNonaktif = Layanan::where('status', 'nonaktif')->count();
        $totalPaket = Paket::count();
        $paketAktif = Paket::where('status', 'aktif')->count();

        // Categories from KategoriLayanan table + distinct existing
        $dbCategories = KategoriLayanan::orderBy('nama')->pluck('nama');
        $existingCategories = Layanan::select('kategori')->distinct()->pluck('kategori');
        $categories = $dbCategories->merge($existingCategories)->unique()->filter()->sort()->values();

        return view('admin.layanan.index', compact(
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
     * Store a newly created service in storage.
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

        // Auto-register in KategoriLayanan if new
        if (!empty($finalKategori)) {
            KategoriLayanan::firstOrCreate(
                ['nama' => $finalKategori],
                ['slug' => Str::slug($finalKategori), 'icon' => 'fa-camera', 'status' => 'aktif']
            );
        }

        $thumbnailPath = null;

        // Handle uploaded image file
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
            // Default placeholder
            $thumbnailPath = 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80';
        }

        $layanan = Layanan::create([
            'nama' => $validated['nama'],
            'kategori' => $finalKategori,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga_mulai' => $validated['harga_mulai'],
            'thumbnail' => $thumbnailPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan fotografi "' . $layanan->nama . '" berhasil ditambahkan.');
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

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

        // Auto-register in KategoriLayanan if new
        if (!empty($finalKategori)) {
            KategoriLayanan::firstOrCreate(
                ['nama' => $finalKategori],
                ['slug' => Str::slug($finalKategori), 'icon' => 'fa-camera', 'status' => 'aktif']
            );
        }

        $thumbnailPath = $layanan->thumbnail;

        // Handle uploaded image file replacement
        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $uploadDir = public_path('uploads/layanan');
            
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            // Remove old uploaded file if it exists in local storage
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
            'kategori' => $finalKategori,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga_mulai' => $validated['harga_mulai'],
            'thumbnail' => $thumbnailPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan fotografi "' . $layanan->nama . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy($id)
    {
        $layanan = Layanan::withCount('bookings')->findOrFail($id);

        $nama = $layanan->nama;
        $totalBookings = $layanan->bookings_count;

        // Delete local uploaded thumbnail file if exists
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
     * Toggle active/inactive status of a service.
     */
    public function toggleStatus($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->status = $layanan->status === 'aktif' ? 'nonaktif' : 'aktif';
        $layanan->save();

        $statusLabel = $layanan->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.layanan.index')->with('success', 'Status layanan "' . $layanan->nama . '" berhasil ' . $statusLabel . '.');
    }

    // ==========================================
    // PAKET (SERVICE PACKAGES) MANAGEMENT
    // ==========================================

    /**
     * Store a newly created package for a service.
     */
    public function storePaket(Request $request, $layananId)
    {
        $layanan = Layanan::findOrFail($layananId);

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

        // Auto update minimum price of the service if this package is cheaper or if harga_mulai was 0
        if ($layanan->harga_mulai == 0 || $paket->harga < $layanan->harga_mulai) {
            $layanan->update(['harga_mulai' => $paket->harga]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Paket "' . $paket->nama_paket . '" berhasil ditambahkan ke layanan ' . $layanan->nama . '.');
    }

    /**
     * Update the specified package in storage.
     */
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

        // Re-evaluate minimum starting price of parent service
        $minPrice = Paket::where('layanan_id', $paket->layanan_id)->where('status', 'aktif')->min('harga');
        if ($minPrice !== null && $paket->layanan) {
            $paket->layanan->update(['harga_mulai' => $minPrice]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Paket "' . $paket->nama_paket . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroyPaket($id)
    {
        $paket = Paket::with('layanan')->findOrFail($id);
        $namaPaket = $paket->nama_paket;
        $layananId = $paket->layanan_id;
        $layanan = $paket->layanan;

        $paket->delete();

        // Update minimum price of parent service after package deletion
        if ($layanan) {
            $minPrice = Paket::where('layanan_id', $layananId)->where('status', 'aktif')->min('harga');
            $layanan->update(['harga_mulai' => $minPrice ?? 0]);
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Paket "' . $namaPaket . '" berhasil dihapus.');
    }

    /**
     * Toggle active/inactive status of a package.
     */
    public function togglePaketStatus($id)
    {
        $paket = Paket::with('layanan')->findOrFail($id);
        $paket->status = $paket->status === 'aktif' ? 'nonaktif' : 'aktif';
        $paket->save();

        // Re-calculate starting price based on active packages
        if ($paket->layanan) {
            $minPrice = Paket::where('layanan_id', $paket->layanan_id)->where('status', 'aktif')->min('harga');
            $paket->layanan->update(['harga_mulai' => $minPrice ?? 0]);
        }

        $statusLabel = $paket->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.layanan.index')->with('success', 'Status paket "' . $paket->nama_paket . '" berhasil ' . $statusLabel . '.');
    }
}
