<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login atau daftar akun terlebih dahulu untuk melakukan reservasi layanan.');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->guest(route('login'))->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.');
        }

        $normalizedRoles = [];
        foreach ($roles as $r) {
            $normalizedRoles[] = $r;
            if ($r === 'owner' || $r === 'admin') {
                $normalizedRoles[] = 'owner';
                $normalizedRoles[] = 'admin';
            }
            if ($r === 'pelanggan' || $r === 'customer' || $r === 'peserta') {
                $normalizedRoles[] = 'pelanggan';
                $normalizedRoles[] = 'customer';
                $normalizedRoles[] = 'peserta';
            }
        }
        $normalizedRoles = array_unique($normalizedRoles);

        if (!in_array($user->role, $normalizedRoles)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
