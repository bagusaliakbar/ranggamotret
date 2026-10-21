@extends('layouts.app')

@section('title', 'Katalog Layanan & Paket — RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Pilihan Layanan</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900">Katalog Dokumentasi Fotografi</h1>
        <p class="text-xs sm:text-sm text-slate-600">Pilih jenis dokumentasi yang sesuai dengan kebutuhan momen berharga atau proyek komersial Anda.</p>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center justify-center gap-2 flex-wrap text-xs font-bold">
        <a href="{{ route('public.services') }}" class="px-5 py-2.5 rounded-xl transition-all {{ !$category ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Semua Layanan
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('public.services', ['category' => $cat]) }}" class="px-5 py-2.5 rounded-xl transition-all {{ $category === $cat ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($layanan as $l)
            <div class="clean-card p-6 sm:p-8 rounded-2xl space-y-6 flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row gap-6">
                    <img src="{{ $l->thumbnail }}" alt="{{ $l->nama }}" class="w-full sm:w-48 h-48 rounded-xl object-cover border border-slate-200">
                    <div class="space-y-2 flex-1">
                        <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 font-bold text-[10px] uppercase tracking-wider">
                            {{ $l->kategori }}
                        </span>
                        <h2 class="text-2xl font-extrabold text-slate-900 mt-2">{{ $l->nama }}</h2>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $l->deskripsi }}</p>
                        <div class="text-xs text-slate-500 pt-2 font-medium">
                            Mulai Dari: <span class="font-extrabold text-slate-900 text-lg ml-1">Rp {{ number_format($l->harga_mulai, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Package Pricing Options -->
                <div class="border-t border-slate-100 pt-6 space-y-4">
                    <h4 class="font-bold text-xs text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-box-archive text-slate-700"></i> Opsi Paket Sesi:
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($l->pakets as $p)
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 flex flex-col justify-between text-xs">
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $p->nama_paket }}</div>
                                    <div class="text-slate-900 font-extrabold mt-0.5">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                                    <p class="text-[11px] text-slate-600 mt-2 leading-relaxed">{{ $p->fasilitas }}</p>
                                </div>
                                <a href="{{ route('customer.bookings.create', ['layanan_id' => $l->id, 'paket_id' => $p->id]) }}" class="btn-dark py-2.5 rounded-xl text-center text-xs font-bold block">
                                    Pilih & Reservasi
                                </a>
                                @guest
                                    <div class="text-[9px] text-slate-400 text-center flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-lock text-[8px]"></i> Wajib login
                                    </div>
                                @endguest
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
