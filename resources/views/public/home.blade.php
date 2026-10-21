@extends('layouts.app')

@section('title', 'RanggaMotret.id — Layanan Fotografi & Akademi Kursus')

@section('content')
<!-- Hero Section (Clean Dominant White + Dark Grey Accents) -->
<section class="bg-white border-b border-slate-200 py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Headline & Copy -->
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-slate-900"></span> Studio Fotografi & Akademi Terintegrasi
                </div>

                <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Abadikan Momen Autentik & Tingkatkan <span class="text-slate-600">Keahlian Visual.</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed">
                    Sistem informasi terpadu layanan dokumentasi fotografi profesional (Wedding, Prewedding, Portrait, Commercial) dan platform akademi kursus berbasis materi interaktif.
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                    <a href="{{ route('public.services') }}" class="btn-dark px-7 py-3.5 rounded-xl text-xs uppercase tracking-wider font-bold text-center flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-calendar-check"></i> Reservasi Layanan Foto
                    </a>
                    <a href="{{ route('public.courses') }}" class="btn-outline px-7 py-3.5 rounded-xl text-xs uppercase tracking-wider font-bold text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-slate-700"></i> Lihat Katalog Kursus
                    </a>
                </div>

                <!-- Minimalist Stat Counters -->
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-200 max-w-lg">
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900">500+</div>
                        <div class="text-xs font-medium text-slate-500 mt-0.5">Sesi Foto Sukses</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900">1,200+</div>
                        <div class="text-xs font-medium text-slate-500 mt-0.5">Peserta Kursus</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900">99.8%</div>
                        <div class="text-xs font-medium text-slate-500 mt-0.5">Kepuasan Klien</div>
                    </div>
                </div>
            </div>

            <!-- Right Featured Image Cover -->
            <div class="lg:col-span-5">
                <div class="aspect-[4/5] rounded-3xl overflow-hidden clean-card relative group shadow-lg">
                    <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1000&q=80" alt="RanggaMotret Studio" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 p-4 rounded-2xl bg-white/90 backdrop-blur-md border border-slate-200 text-xs shadow-md">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold block">Signature Quality</span>
                        <span class="text-sm font-bold text-slate-900 block mt-0.5">RanggaMotret Studio & Photography</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 1: Services Grid (Clean White Background & Cards) -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Katalog Utama</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Layanan Fotografi Spesialis</h2>
            </div>
            <a href="{{ route('public.services') }}" class="text-xs font-bold text-slate-900 hover:text-slate-700 transition-colors">
                Lihat Semua Paket Layanan &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($layanan as $item)
                <div class="clean-card rounded-2xl overflow-hidden flex flex-col justify-between group">
                    <div>
                        <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                            <img src="{{ $item->thumbnail }}" alt="{{ $item->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                                {{ $item->kategori }}
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-slate-700 transition-colors">{{ $item->nama }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $item->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 space-y-4">
                        <div class="flex justify-between items-baseline pt-4 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-medium">Mulai Dari</span>
                            <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format($item->harga_mulai, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('public.services.detail', $item->id) }}" class="btn-dark w-full py-2.5 rounded-xl text-center text-xs font-bold block">
                            Pilih Paket & Reservasi
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Section 2: Portfolio Editorial Grid (Clean White Style) -->
<section class="py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Galeri Portofolio</span>
            <h2 class="text-3xl font-extrabold text-slate-900">Hasil Dokumentasi Pemotretan</h2>
            <p class="text-xs text-slate-600">Dokumentasi momen berharga dengan estetika pencahayaan dan komposisi profesional.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($portofolio as $foto)
                <div class="clean-card rounded-2xl overflow-hidden group relative h-80">
                    <img src="{{ $foto->foto }}" alt="{{ $foto->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end text-white">
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ $foto->kategori }}</span>
                        <h4 class="text-lg font-bold text-white mt-0.5">{{ $foto->judul }}</h4>
                        <p class="text-xs text-slate-200 mt-1 line-clamp-2">{{ $foto->deskripsi }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('public.portfolio') }}" class="btn-outline inline-flex items-center gap-2 px-6 py-3 rounded-xl text-xs uppercase tracking-wider font-bold">
                Jelajahi Galeri Portofolio Lengkap &rarr;
            </a>
        </div>
    </div>
</section>

<!-- Section 3: Course Highlights (Clean Light Cards) -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Akademi Kursus</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Program Belajar & Pelatihan</h2>
            </div>
            <a href="{{ route('public.courses') }}" class="text-xs font-bold text-slate-900 hover:text-slate-700 transition-colors">
                Lihat Semua Kursus &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $c->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 space-y-4">
                        <div class="flex justify-between items-baseline pt-4 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-medium">Investasi Kursus</span>
                            <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format($c->harga, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('public.courses.detail', $c->id) }}" class="btn-dark w-full py-2.5 rounded-xl text-center text-xs font-bold block">
                            Detail Kurikulum & Pendaftaran
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Section 4: Testimonials (Clean White) -->
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Ulasan Pengguna</span>
            <h2 class="text-3xl font-extrabold text-slate-900">Testimoni Klien & Peserta</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimoni as $t)
                <div class="clean-card p-6 rounded-2xl space-y-4">
                    <div class="flex text-amber-500 text-xs gap-1">
                        @for($i=1; $i<=5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $t->rating ? '' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                    <p class="text-xs text-slate-700 italic leading-relaxed">"{{ $t->komentar }}"</p>
                    <div class="flex items-center gap-3 pt-3 border-t border-slate-100 text-xs">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($t->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">{{ $t->user->name }}</div>
                            <div class="text-[10px] text-slate-500 font-medium">Ulasan Terverifikasi</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
