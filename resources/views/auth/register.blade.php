@extends('layouts.auth')

@section('title', 'Daftar Akun Baru — RanggaMotret.id')

@section('content')
<div class="clean-card p-8 rounded-2xl space-y-6">
    <div class="text-center space-y-1">
        <h2 class="text-2xl font-extrabold text-slate-900">Buat Akun Baru</h2>
        <p class="text-xs text-slate-500 font-medium">Satu akun untuk reservasi layanan fotografi & akses materi kursus</p>
    </div>

    @if(session('error'))
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-600"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-start gap-2">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block text-slate-700 font-bold mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"
                placeholder="Nama Lengkap">
        </div>

        <div>
            <label class="block text-slate-700 font-bold mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"
                placeholder="nama@email.com">
        </div>

        <div>
            <label class="block text-slate-700 font-bold mb-1.5">Nomor WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789"
                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"
                    placeholder="••••••••">
            </div>
            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Konfirmasi</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"
                    placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="w-full btn-dark py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider mt-2 shadow-md">
            <i class="fa-solid fa-user-plus mr-2"></i> Daftar Akun Sekarang
        </button>
    </form>

    <div class="text-center text-xs text-slate-600 pt-2 font-medium">
        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-slate-900 font-bold hover:underline">Masuk Ke Akun</a>
    </div>
</div>
@endsection
