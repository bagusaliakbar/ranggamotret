@extends('layouts.app')

@section('title', $layanan->nama . ' — RanggaMotret.id')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    
    <!-- Breadcrumb & Back Navigation -->
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('public.services') }}" class="hover:text-slate-900 transition-colors">Katalog Layanan</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-900 font-bold truncate">{{ $layanan->nama }}</span>
    </div>

    <!-- Service Hero Card -->
    <div class="clean-card p-6 sm:p-10 rounded-3xl space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-5">
                <div class="relative w-full h-80 rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                    <img src="{{ $layanan->thumbnail }}" alt="{{ $layanan->nama }}" class="w-full h-full object-cover">
                    <div class="absolute top-3 left-3">
                        <span class="px-3 py-1 rounded-xl bg-slate-900/80 backdrop-blur-md text-white font-extrabold text-[10px] uppercase tracking-wider">
                            {{ $layanan->kategori }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-4">
                <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    {{ $layanan->kategori }} Photography
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">{{ $layanan->nama }}</h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $layanan->deskripsi }}</p>
                
                <div class="pt-2 flex items-center gap-4 flex-wrap">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="text-[11px] text-slate-500 font-medium">Harga Mulai Dari:</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-0.5">
                            Rp {{ number_format($layanan->harga_mulai, 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <a href="{{ route('public.consultation') }}" class="btn-outline px-5 py-3.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2">
                        <i class="fa-solid fa-comments text-slate-500"></i> Konsultasi Konsep Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Package List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">Pilihan Paket Sesi Fotografi</h2>
                <p class="text-xs text-slate-500 mt-1">Pilih varian paket yang sesuai dengan durasi dan kebutuhan hasil dokumentasi Anda.</p>
            </div>
        </div>

        @php
            $activePakets = $layanan->pakets->where('status', 'aktif');
        @endphp

        @if($activePakets->isEmpty())
            <div class="clean-card p-10 rounded-3xl text-center space-y-3">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Paket Sedang Dipersiapkan</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Varian paket untuk layanan ini sedang dalam tahap pembaruan. Silakan hubungi tim kami untuk reservasi khusus.</p>
                <a href="{{ route('public.consultation') }}" class="inline-block btn-dark px-5 py-2.5 rounded-xl text-xs font-bold mt-2">
                    Hubungi Konsultasi
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activePakets as $p)
                    <div class="clean-card p-6 sm:p-7 rounded-3xl space-y-5 flex flex-col justify-between hover:shadow-lg transition-all border border-slate-200 text-xs">
                        <div class="space-y-4">
                            <!-- Package Header -->
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg leading-tight">{{ $p->nama_paket }}</h3>
                                    <div class="text-2xl font-black text-slate-900 mt-1.5">
                                        Rp {{ number_format($p->harga, 0, ',', '.') }}
                                    </div>
                                </div>
                                @if($p->durasi)
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200 shrink-0">
                                        {{ $p->durasi }}
                                    </span>
                                @endif
                            </div>

                            @if($p->deskripsi)
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $p->deskripsi }}</p>
                            @endif
                            
                            <!-- Specifications -->
                            <div class="space-y-2.5 pt-3 border-t border-slate-100 text-xs text-slate-700 font-medium">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                    <span>{{ $p->jumlah_fotografer }} Fotografer Profesional</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                    <span>Minimal {{ $p->jumlah_foto }} Foto Retouched</span>
                                </div>
                                @if($p->fasilitas)
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-[11px] text-slate-600 leading-relaxed mt-2">
                                        <div class="font-bold text-[10px] text-slate-400 uppercase mb-1">Fasilitas & Output:</div>
                                        {{ $p->fasilitas }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Booking Button -->
                        <div class="pt-3 border-t border-slate-100">
                            <a href="{{ route('customer.bookings.create', ['layanan_id' => $layanan->id, 'paket_id' => $p->id]) }}" 
                               class="w-full btn-dark py-3.5 rounded-xl text-center text-xs font-bold block shadow-md hover:shadow-lg transition-all">
                                Pilih Paket & Reservasi Sesi
                            </a>
                            @guest
                                <div class="text-[10px] text-slate-400 text-center mt-2 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-lock text-[9px]"></i> Wajib masuk / daftar akun untuk reservasi
                                </div>
                            @endguest
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
