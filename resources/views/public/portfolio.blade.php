@extends('layouts.app')

@section('title', 'Portofolio Karya — RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Galeri Hasil Pemotretan</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900">Portofolio Fotografi</h1>
        <p class="text-xs sm:text-sm text-slate-600">Dokumentasi momen nyata karya RanggaMotret dari berbagai jenis spesialisasi.</p>
    </div>

    <!-- Category Filters -->
    <div class="flex items-center justify-center gap-2 flex-wrap text-xs font-bold">
        <a href="{{ route('public.portfolio') }}" class="px-5 py-2.5 rounded-xl transition-all {{ !$category ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Semua Kategori
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('public.portfolio', ['category' => $cat]) }}" class="px-5 py-2.5 rounded-xl transition-all {{ $category === $cat ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($portofolio as $p)
            <div class="clean-card rounded-2xl overflow-hidden group relative h-96">
                <img src="{{ $p->foto }}" alt="{{ $p->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end text-white">
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ $p->kategori }}</span>
                    <h3 class="text-xl font-bold text-white mt-1">{{ $p->judul }}</h3>
                    <p class="text-xs text-slate-200 mt-1 line-clamp-3 leading-relaxed">{{ $p->deskripsi }}</p>
                    <div class="text-[10px] text-slate-400 mt-2 font-medium"><i class="fa-regular fa-calendar mr-1"></i> {{ date('d M Y', strtotime($p->tanggal)) }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
