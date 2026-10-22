@extends('layouts.admin')

@section('title', 'Profil Pengguna — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
        <span>/</span>
        <span class="text-slate-700">Profil & Pengaturan Akun</span>
    </div>

    <div class="clean-card p-8 rounded-2xl space-y-6">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-6">
            <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white font-bold flex items-center justify-center text-2xl shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $user->name }}</h1>
                <p class="text-xs text-slate-500 font-medium capitalize">{{ $user->role }} &bull; Joined {{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 font-bold mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Nomor Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="font-bold text-sm text-slate-900 mb-3">Ubah Password (Opsional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Kosongkan jika tidak diubah"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-dark px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md">
                Simpan Perubahan Profil
            </button>
        </form>
    </div>
</div>
@endsection
