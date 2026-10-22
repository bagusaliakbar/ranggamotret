<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.']);
            }

            $defaultRoute = $this->getDashboardRouteByRole();
            return redirect()->intended($defaultRoute)->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]
        );

        Auth::login($user);

        $defaultRoute = $this->getDashboardRouteByRole();
        return redirect()->intended($defaultRoute)->with('success', 'Registrasi berhasil! Selamat datang di RanggaMotret.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah berhasil keluar.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
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

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function getDashboardRouteByRole()
    {
        return route('dashboard');
    }

    private function redirectBasedOnRole()
    {
        return redirect()->route('dashboard');
    }
}
