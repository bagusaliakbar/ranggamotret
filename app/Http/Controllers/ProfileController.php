<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

/**
 * Controller Pengelolaan Profil Pengguna
 * 
 * Merepresentasikan Control Object 'ProfileController' pada Robustness Diagram.
 * Terhubung dengan boundary 'profile' dan entity 'User'.
 */
class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna
     */
    public function index()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Memperbarui informasi profil dan kredensial pengguna
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Selaraskan entitas Customer terkait
        Customer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]
        );

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
