<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimoni;

class TestimonialController extends Controller
{
    // Customer submit testimonial
    public function store(Request $request)
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

    // Admin moderation list
    public function adminIndex()
    {
        $testimonis = Testimoni::with(['user', 'booking.layanan', 'kursus'])->latest()->paginate(15);
        return view('admin.testimonials.index', compact('testimonis'));
    }

    public function adminUpdateStatus(Request $request, $id)
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
