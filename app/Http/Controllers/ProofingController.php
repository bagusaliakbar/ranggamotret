<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\GaleriFoto;

class ProofingController extends Controller
{
    // Customer Proofing Gallery Page
    public function customerShow($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['layanan', 'paket', 'galeriFotos'])
            ->findOrFail($bookingId);

        return view('customer.proofing.index', compact('booking'));
    }

    // Customer Toggle Selection
    public function toggleSelect(Request $request, $id)
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

    // Admin Upload Booking Deliverable Photos
    public function adminUpload(Request $request, $bookingId)
    {
        $request->validate([
            'fotos.*' => 'required|image|max:10240',
        ]);

        $booking = Booking::findOrFail($bookingId);

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

    // Admin Delete Photo
    public function adminDeletePhoto($id)
    {
        $foto = GaleriFoto::findOrFail($id);
        $foto->delete();
        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
