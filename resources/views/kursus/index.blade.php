@extends('layouts.app')

@section('title', 'Katalog Kursus Fotografi — RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Akademi Edukasi</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900">Program Belajar Fotografi</h1>
        <p class="text-xs sm:text-sm text-slate-600">Kurikulum komprehensif dari praktisi untuk menguasai aspek teknis dan seni fotografi.</p>
    </div>

    <!-- Level Filter -->
    <div class="flex items-center justify-center gap-2 text-xs font-bold">
        <a href="{{ route('public.courses') }}" class="px-5 py-2.5 rounded-xl transition-all {{ !$level ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Semua Level
        </a>
        <a href="{{ route('public.courses', ['level' => 'Pemula']) }}" class="px-5 py-2.5 rounded-xl transition-all {{ $level === 'Pemula' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Pemula
        </a>
        <a href="{{ route('public.courses', ['level' => 'Menengah']) }}" class="px-5 py-2.5 rounded-xl transition-all {{ $level === 'Menengah' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Menengah
        </a>
        <a href="{{ route('public.courses', ['level' => 'Mahir']) }}" class="px-5 py-2.5 rounded-xl transition-all {{ $level === 'Mahir' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Mahir
        </a>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($kursus as $c)
            <div class="clean-card rounded-2xl overflow-hidden flex flex-col justify-between group">
                <div>
                    <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                        <img src="{{ $c->thumbnail }}" alt="{{ $c->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                            Level: {{ $c->level }}
                        </div>
                    </div>

                    <div class="p-6 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                            <span><i class="fa-regular fa-clock mr-1 text-slate-700"></i> {{ $c->durasi }}</span>
                            <span><i class="fa-solid fa-user-tie mr-1 text-slate-700"></i> {{ $c->instruktur->name }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-slate-700 transition-colors">{{ $c->nama }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">{{ $c->deskripsi }}</p>
                    </div>
                </div>

                <div class="p-6 pt-0 space-y-4">
                    <div class="flex justify-between items-baseline pt-4 border-t border-slate-100 text-xs">
                        <span class="text-slate-500 font-medium">Investasi Pelatihan</span>
                        <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format($c->harga, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('public.courses.detail', $c->id) }}" class="btn-dark w-full py-2.5 rounded-xl text-center text-xs font-bold block">
                        Lihat Materi & Reservasi
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
