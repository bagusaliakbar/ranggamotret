@extends('layouts.auth')

@section('title', 'Masuk Akun — RanggaMotret.id')

@section('content')
<div class="clean-card p-8 rounded-2xl space-y-6">
    <div class="text-center space-y-1">
        <h2 class="text-2xl font-extrabold text-slate-900">Selamat Datang Kembali</h2>
        <p class="text-xs text-slate-500 font-medium">Masukkan email dan password untuk melanjutkan</p>
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

    <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block text-slate-700 font-bold mb-1.5">Email</label>
            <div class="relative">
                <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400"></i>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"
                    placeholder="nama@email.com">
            </div>
        </div>

        <div>
            <label class="block text-slate-700 font-bold mb-1.5">Password</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400"></i>
                <input type="password" name="password" required
                    class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium"
                    placeholder="••••••••">
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
            <label class="flex items-center gap-2 cursor-pointer font-medium">
                <input type="checkbox" name="remember" class="rounded bg-slate-100 border-slate-300 text-slate-900 focus:ring-0">
                <span>Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="w-full btn-dark py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md">
            <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk Sekarang
        </button>
    </form>

    <div class="text-center text-xs text-slate-600 pt-2 font-medium">
        Belum memiliki akun? <a href="{{ route('register') }}" class="text-slate-900 font-bold hover:underline">Daftar Akun Baru</a>
    </div>
</div>
@endsection
