@extends('layouts.app')

@section('title', 'RanggaMotret.id — Studio Fotografi & Akademi Terpadu')

@section('content')
<!-- Hero Section (Luxury Editorial Modern) -->
<section class="bg-gradient-to-b from-slate-50 via-white to-white border-b border-slate-200 py-16 lg:py-24 relative overflow-hidden" x-data="{ activeTab: 'all' }">
    <!-- Subtle Ambient Background Accents -->
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-amber-100/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-96 h-96 bg-slate-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            
            <!-- Left Headline & Copywriting -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Top Minimalist Pill Badge -->
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-slate-900 text-white text-[10px] sm:text-xs font-bold uppercase tracking-widest shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Studio Fotografi &amp; Akademi Visual Terpadu</span>
                </div>

                <!-- Editorial Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.12]">
                    Abadikan Setiap <span class="font-serif italic font-normal text-slate-700 underline decoration-slate-300 decoration-1 underline-offset-8">Mahakarya</span> &amp; Kuasai Seni Visual.
                </h1>

                <p class="text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed font-medium">
                    Platform ekosistem kreatif satu pintu: dokumentasi fotografi eksklusif (Wedding, Prewedding, Wisuda, Commercial) dengan sistem digital proofing instan, serta akademi kurikulum fotografi praktis berstandar industri langsung di Subang.
                </p>

                <!-- Action CTAs (3 Uniform Full-Width Buttons) -->
                <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 pt-1">
                    <a href="{{ route('public.services') }}" 
                       class="px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-extrabold tracking-wide text-center flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph-duotone ph-camera text-base text-emerald-400"></i>
                        <span>Reservasi Sesi Foto</span>
                    </a>
                    <a href="{{ route('public.courses') }}" 
                       class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-900 border border-slate-300 text-xs font-extrabold tracking-wide text-center flex items-center justify-center gap-2 shadow-2xs hover:border-slate-400 hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph-duotone ph-graduation-cap text-base text-indigo-600"></i>
                        <span>Eksplorasi Kursus</span>
                    </a>
                    <a href="{{ route('public.consultation') }}" 
                       class="px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200/80 text-xs font-extrabold tracking-wide text-center flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph-duotone ph-chats-circle text-base text-amber-600"></i>
                        <span>Konsultasi Gratis</span>
                    </a>
                </div>

                <!-- Luxury Feature Chips (Menyeimbangkan Komposisi) -->
                <div class="pt-6 border-t border-slate-200/80 flex flex-wrap items-center gap-2.5 sm:gap-3 text-[11px] font-semibold text-slate-600">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-800">
                        <i class="ph-duotone ph-sparkle text-amber-500 text-xs"></i>
                        <span>Color Grading Sinematik</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-800">
                        <i class="ph-duotone ph-cloud-arrow-down text-sky-500 text-xs"></i>
                        <span>Kurasi Digital Proofing</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-800">
                        <i class="ph-duotone ph-certificate text-emerald-600 text-xs"></i>
                        <span>Sertifikat Terverifikasi</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-800">
                        <i class="ph-duotone ph-map-pin text-rose-500 text-xs"></i>
                        <span>Studio Subang</span>
                    </div>
                </div>
            </div>

            <!-- Right Featured Image Showcase (Artistic Composite Frame) -->
            <div class="lg:col-span-5 relative">
                <!-- Decorative Outer Viewfinder Frame -->
                <div class="relative p-2.5 sm:p-3 bg-white border border-slate-200 rounded-3xl shadow-xl">
                    <!-- Corner Viewfinder Reticle Marks (Camera Aesthetics) -->
                    <div class="absolute top-4 left-4 w-3.5 h-3.5 border-t-2 border-l-2 border-white/80 z-20 pointer-events-none"></div>
                    <div class="absolute top-4 right-4 w-3.5 h-3.5 border-t-2 border-r-2 border-white/80 z-20 pointer-events-none"></div>
                    <div class="absolute bottom-4 left-4 w-3.5 h-3.5 border-b-2 border-l-2 border-white/80 z-20 pointer-events-none"></div>
                    <div class="absolute bottom-4 right-4 w-3.5 h-3.5 border-b-2 border-r-2 border-white/80 z-20 pointer-events-none"></div>

                    <!-- Top Spec Bar Overlay -->
                    <div class="absolute top-5 left-5 right-5 z-20 flex items-center justify-between pointer-events-none">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/55 backdrop-blur-md text-[10px] font-mono text-white/90 font-bold border border-white/10">
                            <i class="ph-duotone ph-aperture text-amber-400"></i>
                            <span>50mm &bull; f/1.4 &bull; 1/250s &bull; ISO 100</span>
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500 text-slate-950 font-black text-[9px] uppercase tracking-wider">
                            RAW 4K
                        </span>
                    </div>

                    <!-- Main Image Canvas -->
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden relative group bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1000&q=80" 
                             alt="RanggaMotret Studio Production" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        
                        <!-- Soft Bottom Vignette -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div>

                        <!-- Floating Glass Editorial Tag -->
                        <div class="absolute bottom-4 left-4 right-4 p-4 rounded-xl bg-white/90 backdrop-blur-md border border-white/50 text-xs shadow-lg space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] uppercase tracking-widest text-slate-500 font-extrabold flex items-center gap-1">
                                    <i class="ph-duotone ph-camera text-slate-700 text-xs"></i>
                                    <span>Signature Visuals</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9px] font-bold">
                                    Terbuka Sesi Baru
                                </span>
                            </div>
                            <span class="text-sm font-extrabold text-slate-900 block">RanggaMotret Studio &amp; Production</span>
                            <p class="text-[11px] text-slate-500 font-medium line-clamp-1">Perumahan RSS Sidodadi Blok A No 78, Pasirkareumbi, Subang</p>
                        </div>
                    </div>
                </div>

                <!-- Floating Accent Dot Shadow -->
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-slate-900/5 rounded-full blur-2xl -z-10"></div>
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
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Layanan Fotografi</h2>
            </div>
            <a href="{{ route('public.services') }}" class="text-xs font-extrabold text-slate-900 hover:text-slate-700 transition-colors inline-flex items-center gap-1">
                <span>Lihat Semua Paket Layanan</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($layanan as $item)
                <div class="clean-card rounded-2xl overflow-hidden flex flex-col justify-between group shadow-sm hover:shadow-xl transition-all">
                    <div>
                        <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                            <img src="{{ $item->thumbnail }}" alt="{{ $item->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider shadow-md">
                                {{ $item->kategori }}
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-slate-700 transition-colors">{{ $item->nama }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2 font-medium">{{ $item->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 space-y-4">
                        <div class="flex justify-between items-baseline pt-4 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-medium">Mulai Dari</span>
                            <span class="text-lg font-black text-slate-900">Rp {{ number_format($item->harga_mulai, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('public.services.detail', $item->id) }}" class="btn-dark w-full py-3 rounded-xl text-center text-xs font-bold block shadow-md">
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
            <p class="text-xs text-slate-600 font-medium">Dokumentasi momen berharga dengan estetika pencahayaan dan komposisi profesional.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($portofolio as $foto)
                <div class="clean-card rounded-2xl overflow-hidden group relative h-80 shadow-sm hover:shadow-xl transition-all">
                    <img src="{{ $foto->foto_url }}" alt="{{ $foto->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/25 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end text-white">
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ $foto->kategori }}</span>
                        <h4 class="text-lg font-bold text-white mt-0.5">{{ $foto->judul }}</h4>
                        <p class="text-xs text-slate-200 mt-1 line-clamp-2 leading-relaxed">{{ $foto->deskripsi }}</p>
                        <a href="{{ route('public.portfolio', ['category' => $foto->kategori]) }}" class="mt-3 text-[11px] font-bold text-white underline hover:text-slate-200 flex items-center gap-1">
                            Lihat Foto Serupa &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('public.portfolio') }}" class="btn-outline inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl text-xs uppercase tracking-wider font-extrabold shadow-sm">
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
            <a href="{{ route('public.courses') }}" class="text-xs font-extrabold text-slate-900 hover:text-slate-700 transition-colors inline-flex items-center gap-1">
                <span>Lihat Semua Kursus</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($kursus as $c)
                <div class="clean-card rounded-2xl overflow-hidden flex flex-col justify-between group shadow-sm hover:shadow-xl transition-all">
                    <div>
                        <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                            <img src="{{ $c->thumbnail }}" alt="{{ $c->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider shadow-md">
                                Level: {{ $c->level }}
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                                <span><i class="ph-duotone ph-clock mr-1 text-slate-700"></i> {{ $c->durasi }}</span>
                                <span><i class="ph-duotone ph-user mr-1 text-slate-700"></i> Owner: {{ $c->instruktur->name ?? 'RanggaMotret' }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-slate-700 transition-colors">{{ $c->nama }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2 font-medium">{{ $c->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 space-y-4">
                        <div class="flex justify-between items-baseline pt-4 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-medium">Investasi Kursus</span>
                            <span class="text-lg font-black text-slate-900">Rp {{ number_format($c->harga, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('public.courses.detail', $c->id) }}" class="btn-dark w-full py-3 rounded-xl text-center text-xs font-bold block shadow-md">
                            Detail Kurikulum & Pendaftaran
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
