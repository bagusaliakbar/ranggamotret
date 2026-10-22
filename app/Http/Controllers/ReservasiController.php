<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservasi;
use App\Models\Katalog;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Enrollment;
use App\Models\Notifikasi;
use App\Models\GaleriFoto;
use App\Models\Testimoni;
use Carbon\Carbon;

/**
 * Controller Pengelolaan Reservasi Pemotretan & Kursus (ICONIX Robustness: ReservasiController)
 * 
 * Bertanggung jawab atas seluruh siklus reservasi:
 * - Pemesanan sesi foto oleh pelanggan
 * - Kalender & validasi jadwal pemotretan
 * - Moderasi & update status reservasi oleh Admin
 * - Galeri proofing hasil foto
 * - Ulasan & testimoni pelanggan
 */
class ReservasiController extends Controller
{
    // ==========================================
    // 1. CUSTOMER RESERVASI (Boundary: reservasi_foto)
    // ==========================================

    /**
     * Menampilkan daftar reservasi pemotretan pelanggan
     */
    public function index()
    {
        return $this->customerIndex();
    }

    public function customerIndex()
    {
        $bookings = Reservasi::where('user_id', Auth::id())
            ->with(['layanan', 'paket', 'pembayaran', 'galeriFotos'])
            ->latest()
            ->get();

        return view('reservasi.index', compact('bookings'));
    }

    /**
     * Menampilkan daftar layanan fotografi yang telah dipesan oleh pelanggan
     */
    public function customerFotografi(Request $request)
    {
        $userId = Auth::id();
        
        $bookings = Reservasi::where('user_id', $userId)
            ->with(['layanan', 'paket', 'pembayaran', 'galeriFotos'])
            ->latest()
            ->get();

        $activeBookings = $bookings->filter(fn($b) => in_array($b->status, ['Menunggu Konfirmasi', 'Disetujui', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal']));
        $completedBookings = $bookings->filter(fn($b) => $b->status === 'Selesai');
        $totalPhotosAvailable = $bookings->sum(fn($b) => $b->galeriFotos->count());

        return view('reservasi.fotografi', compact(
            'bookings',
            'activeBookings',
            'completedBookings',
            'totalPhotosAvailable'
        ));
    }

    /**
     * Mengambil daftar slot dan rentang jam pemotretan yang telah direservasi untuk tanggal tertentu
     */
    private function getBookedScheduleData($tanggal)
    {
        $popularSlots = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:30', '16:30', '18:30', '19:30'];
        
        $bookings = Reservasi::with(['paket', 'layanan'])
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['Menunggu Konfirmasi', 'Menunggu Pembayaran', 'Disetujui', 'Dibayar', 'Terjadwal'])
            ->get();

        $bookedRanges = [];
        $occupiedSlots = [];

        foreach ($bookings as $b) {
            $startTime = substr($b->waktu, 0, 5);
            $durasiStr = $b->paket->durasi ?? '2';
            $durationHours = (int) filter_var($durasiStr, FILTER_SANITIZE_NUMBER_INT) ?: 2;
            
            try {
                $startCarbon = Carbon::createFromFormat('H:i', $startTime);
                $endCarbon = (clone $startCarbon)->addHours($durationHours);
            } catch (\Exception $e) {
                $startCarbon = Carbon::parse($startTime);
                $endCarbon = (clone $startCarbon)->addHours($durationHours);
            }

            $bookedRanges[] = [
                'booking_id' => $b->id,
                'start' => $startTime,
                'end' => $endCarbon->format('H:i'),
                'layanan' => $b->layanan->nama ?? 'Sesi Foto',
                'paket' => $b->paket->nama_paket ?? 'Paket',
                'durasi' => $durationHours . ' Jam',
            ];
        }

        foreach ($popularSlots as $slot) {
            try {
                $slotCarbon = Carbon::createFromFormat('H:i', $slot);
                foreach ($bookedRanges as $range) {
                    $rangeStart = Carbon::createFromFormat('H:i', $range['start']);
                    $rangeEnd = Carbon::createFromFormat('H:i', $range['end']);
                    if ($slotCarbon >= $rangeStart && $slotCarbon < $rangeEnd) {
                        $occupiedSlots[] = $slot;
                        break;
                    }
                }
            } catch (\Exception $e) {
                // ignore format issue
            }
        }

        return [
            'bookedSlots' => array_values(array_unique($occupiedSlots)),
            'bookedRanges' => $bookedRanges,
        ];
    }

    /**
     * AJAX endpoint: cek ketersediaan slot jadwal pemotretan berdasarkan tanggal
     */
    public function checkJadwal(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));
        $scheduleData = $this->getBookedScheduleData($tanggal);

        return response()->json([
            'tanggal' => $tanggal,
            'booked_slots' => $scheduleData['bookedSlots'],
            'booked_ranges' => $scheduleData['bookedRanges'],
        ]);
    }

    /**
     * Form formulir reservasi baru
     */
    public function create(Request $request)
    {
        $layananId = $request->query('layanan_id');
        $paketId = $request->query('paket_id');
        $initialDate = old('tanggal', date('Y-m-d'));

        $layananList = Katalog::where('status', 'aktif')
            ->with(['pakets' => fn($q) => $q->where('status', 'aktif')])
            ->get();
        $selectedPaket = $paketId ? Paket::where('status', 'aktif')->find($paketId) : null;

        $scheduleData = $this->getBookedScheduleData($initialDate);
        $initialBookedSlots = $scheduleData['bookedSlots'];
        $initialBookedRanges = $scheduleData['bookedRanges'];

        return view('reservasi.create', compact(
            'layananList',
            'layananId',
            'selectedPaket',
            'initialBookedSlots',
            'initialBookedRanges'
        ));
    }

    /**
     * Menyimpan reservasi foto baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'layanan_id' => 'required|exists:layanan,id,status,aktif',
            'paket_id' => 'required|exists:paket,id,status,aktif',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required|date_format:H:i',
            'lokasi' => 'required|string|max:255',
            'kebutuhan_khusus' => 'nullable|string',
        ], [
            'tanggal.after_or_equal' => 'Tanggal pemotretan tidak boleh tanggal yang sudah terlewat.',
            'waktu.required' => 'Waktu / jam sesi wajib diisi.',
            'waktu.date_format' => 'Format waktu tidak valid (gunakan format JJ:MM).',
        ]);

        // Validasi waktu di masa depan
        try {
            $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $validated['tanggal'] . ' ' . $validated['waktu']);
            if ($bookingDateTime->isPast()) {
                return back()->withInput()->withErrors([
                    'waktu' => 'Waktu sesi (' . $validated['waktu'] . ') pada tanggal ' . Carbon::parse($validated['tanggal'])->format('d/m/Y') . ' sudah terlewat. Silakan pilih waktu yang akan datang.'
                ]);
            }
        } catch (\Exception $e) {
            $bookingDateTime = Carbon::parse($validated['tanggal'] . ' ' . $validated['waktu']);
            if ($bookingDateTime->isPast()) {
                return back()->withInput()->withErrors([
                    'waktu' => 'Waktu sesi yang dipilih sudah terlewat. Silakan pilih waktu yang akan datang.'
                ]);
            }
        }

        // Validasi bentrok jadwal sesi foto
        $paket = Paket::find($validated['paket_id']);
        $requestedDurationHours = (int) filter_var($paket->durasi ?? '2', FILTER_SANITIZE_NUMBER_INT) ?: 2;
        
        try {
            $requestedStart = Carbon::createFromFormat('H:i', $validated['waktu']);
        } catch (\Exception $e) {
            $requestedStart = Carbon::parse($validated['waktu']);
        }
        $requestedEnd = (clone $requestedStart)->addHours($requestedDurationHours);

        $existingBookings = Reservasi::with('paket')
            ->where('tanggal', $validated['tanggal'])
            ->whereIn('status', ['Menunggu Konfirmasi', 'Menunggu Pembayaran', 'Disetujui', 'Dibayar', 'Terjadwal'])
            ->get();

        foreach ($existingBookings as $eb) {
            $existingStartTime = substr($eb->waktu, 0, 5);
            $existingDuration = (int) filter_var($eb->paket->durasi ?? '2', FILTER_SANITIZE_NUMBER_INT) ?: 2;
            try {
                $existingStart = Carbon::createFromFormat('H:i', $existingStartTime);
            } catch (\Exception $e) {
                $existingStart = Carbon::parse($existingStartTime);
            }
            $existingEnd = (clone $existingStart)->addHours($existingDuration);

            // Deteksi bentrok: interval A overlap interval B jika (StartA < EndB) && (EndA > StartB)
            if ($requestedStart < $existingEnd && $requestedEnd > $existingStart) {
                return back()->withInput()->withErrors([
                    'waktu' => 'Maaf, jadwal sesi pada jam ' . $validated['waktu'] . ' (estimasi durasi ' . $requestedDurationHours . ' jam) bentrok dengan jadwal reservasi yang sudah ada (' . $existingStart->format('H:i') . ' - ' . $existingEnd->format('H:i') . ' WIB). Silakan pilih jam atau tanggal lain.'
                ]);
            }
        }

        $bookingNumber = 'BK-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $booking = Reservasi::create([
            'nomor_booking' => $bookingNumber,
            'user_id' => Auth::id(),
            'layanan_id' => $validated['layanan_id'],
            'paket_id' => $validated['paket_id'],
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'lokasi' => $validated['lokasi'],
            'kebutuhan_khusus' => $validated['kebutuhan_khusus'],
            'status' => 'Menunggu Konfirmasi',
        ]);

        // Notifikasi ke Admin
        Notifikasi::create([
            'user_id' => 1,
            'judul' => 'Reservasi Fotografi Baru',
            'pesan' => 'Reservasi baru ' . $bookingNumber . ' dari ' . Auth::user()->name,
            'link' => '/admin/bookings/' . $booking->id,
        ]);

        return redirect()->route('payments.create', ['booking_id' => $booking->id])
            ->with('success', 'Reservasi sesi foto ' . $bookingNumber . ' berhasil dibuat! Silakan selesaikan pembayaran untuk mengunci jadwal pemotretan Anda.');
    }

    /**
     * Rincian reservasi pelanggan
     */
    public function show($id)
    {
        return $this->customerShow($id);
    }

    public function customerShow($id)
    {
        $booking = Reservasi::where('user_id', Auth::id())
            ->with(['layanan', 'paket', 'pembayaran.invoice', 'galeriFotos', 'testimoni'])
            ->findOrFail($id);

        return view('reservasi.show', compact('booking'));
    }

    // ==========================================
    // 2. ADMIN RESERVASI (Boundary: reservasi_foto kelola)
    // ==========================================

    /**
     * Menampilkan daftar reservasi untuk Admin
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $query = Reservasi::with(['customer', 'layanan', 'paket', 'pembayaran']);

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate(15);
        $statuses = ['Menunggu Konfirmasi', 'Disetujui', 'Ditolak', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal', 'Selesai', 'Dibatalkan'];

        return view('reservasi.admin', compact('bookings', 'statuses', 'status'));
    }

    /**
     * Menampilkan detail reservasi untuk Admin
     */
    public function adminShow($id)
    {
        $booking = Reservasi::with(['customer', 'layanan', 'paket', 'pembayaran.invoice', 'galeriFotos'])->findOrFail($id);
        return view('reservasi.admin-show', compact('booking'));
    }

    /**
     * Memperbarui status reservasi oleh Admin
     */
    public function adminUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $booking = Reservasi::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        Notifikasi::create([
            'user_id' => $booking->user_id,
            'judul' => 'Status Reservasi Diperbarui',
            'pesan' => 'Status reservasi ' . $booking->nomor_booking . ' diubah menjadi: ' . $booking->status,
            'link' => '/customer/bookings/' . $booking->id,
        ]);

        return back()->with('success', 'Status reservasi berhasil diperbarui.');
    }

    // ==========================================
    // 3. PROOFING FOTO HASIL SESI
    // ==========================================

    /**
     * Galeri proofing untuk pelanggan memilih foto terpilih
     */
    public function customerProofing($bookingId)
    {
        $booking = Reservasi::where('user_id', Auth::id())
            ->with(['layanan', 'paket', 'galeriFotos'])
            ->findOrFail($bookingId);

        return view('reservasi.proofing', compact('booking'));
    }

    /**
     * Toggle penandaan foto pilihan pelanggan
     */
    public function toggleProofingSelection(Request $request, $id)
    {
        $foto = GaleriFoto::whereHas('booking', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $foto->is_selected = !$foto->is_selected;
        if ($request->has('catatan')) {
            $foto->catatan = $request->catatan;
        }
        $foto->save();

        return response()->json([
            'success' => true,
            'is_selected' => $foto->is_selected,
            'message' => $foto->is_selected ? 'Foto ditandai sebagai pilihan' : 'Tanda foto dihapus',
        ]);
    }

    /**
     * Admin mengunggah foto hasil pemotretan
     */
    public function adminUploadProofing(Request $request, $bookingId)
    {
        $request->validate([
            'fotos.*' => 'required|image|max:10240',
        ]);

        $booking = Reservasi::findOrFail($bookingId);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $file) {
                $filename = time() . '_' . rand(100, 999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/galeri'), $filename);

                GaleriFoto::create([
                    'booking_id' => $booking->id,
                    'foto_path' => 'uploads/galeri/' . $filename,
                    'is_selected' => false,
                ]);
            }
        }

        return back()->with('success', 'Foto hasil pemotretan berhasil diunggah ke galeri proofing pelanggan.');
    }

    /**
     * Admin menghapus foto proofing
     */
    public function adminDeleteProofingPhoto($id)
    {
        $foto = GaleriFoto::findOrFail($id);
        $foto->delete();
        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // Aliases for seamless backward compatibility
    public function toggleSelect(Request $request, $id)
    {
        return $this->toggleProofingSelection($request, $id);
    }

    public function adminUpload(Request $request, $bookingId)
    {
        return $this->adminUploadProofing($request, $bookingId);
    }

    public function adminDeletePhoto($id)
    {
        return $this->adminDeleteProofingPhoto($id);
    }

    public function adminUpdateStatusTestimoni(Request $request, $id)
    {
        return $this->adminUpdateTestimonialStatus($request, $id);
    }

    // ==========================================
    // 4. TESTIMONI & ULASAN RESERVASI
    // ==========================================

    /**
     * Pelanggan mengirimkan ulasan setelah sesi foto
     */
    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:booking,id',
            'kursus_id' => 'nullable|exists:kursus,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:1000',
        ]);

        Testimoni::create([
            'user_id' => Auth::id(),
            'booking_id' => $validated['booking_id'] ?? null,
            'kursus_id' => $validated['kursus_id'] ?? null,
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda! Testimoni Anda sedang ditinjau oleh admin.');
    }

    /**
     * Admin melihat daftar testimoni
     */
    public function adminTestimonialsIndex()
    {
        $testimonis = Testimoni::with(['user', 'booking.layanan', 'kursus'])->latest()->paginate(15);
        return view('reservasi.testimoni', compact('testimonis'));
    }

    /**
     * Admin memoderasi status testimoni
     */
    public function adminUpdateTestimonialStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $testimoni = Testimoni::findOrFail($id);
        $testimoni->status = $request->status;
        $testimoni->save();

        return back()->with('success', 'Status testimoni berhasil diperbarui.');
    }
}
