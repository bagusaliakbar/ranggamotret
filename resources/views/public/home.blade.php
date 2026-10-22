@extends('layouts.app')

@section('title', 'RanggaMotret.id — Studio Fotografi & Akademi Terpadu')

@section('content')
<!-- Hero Section (Clean Luxury Slate + Cormorant Editorial Accents) -->
<section class="bg-white border-b border-slate-200 py-16 lg:py-24 relative overflow-hidden" x-data="{ activeTab: 'all' }">
    <!-- Subtle Background Glow -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-slate-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-slate-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            
            <!-- Left Headline & Copy -->
            <div class="lg:col-span-7 space-y-7">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-slate-900 text-white text-xs font-bold uppercase tracking-widest shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Studio Fotografi & Akademi Visual Terintegrasi</span>
                </div>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-slate-900 tracking-tight leading-[1.08]">
                    Abadikan Setiap <span class="font-serif italic font-normal text-slate-600">Mahakarya</span> & Kuasai Seni Visual.
                </h1>

                <p class="text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed font-medium">
                    Platform ekosistem kreatif satu pintu: dokumentasi fotografi eksklusif (Wedding, Prewedding, Portrait, Commercial) dengan sistem digital proofing instan, serta akademi kurikulum fotografi praktis berstandar industri.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 pt-1">
                    <a href="{{ route('public.services') }}" class="btn-dark px-8 py-4 rounded-2xl text-xs uppercase tracking-wider font-extrabold text-center flex items-center justify-center gap-2.5 shadow-lg shadow-slate-900/15 hover:shadow-xl transition-all">
                        <i class="fa-solid fa-calendar-check text-emerald-400"></i> Reservasi Layanan Foto
                    </a>
                    <a href="{{ route('public.courses') }}" class="btn-outline px-8 py-4 rounded-2xl text-xs uppercase tracking-wider font-extrabold text-center flex items-center justify-center gap-2.5 hover:bg-slate-100 transition-all">
                        <i class="fa-solid fa-graduation-cap text-slate-700"></i> Eksplorasi Kursus
                    </a>
                    <a href="{{ route('public.consultation') }}" class="px-5 py-4 rounded-2xl text-xs font-bold text-slate-600 hover:text-slate-900 text-center flex items-center justify-center gap-1.5 transition-colors">
                        <i class="fa-regular fa-comments"></i> Konsultasi Gratis &rarr;
                    </a>
                </div>

                <!-- Live Social Proof & Trust Strip -->
                <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2 overflow-hidden">
                            <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Client 1">
                            <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" alt="Client 2">
                            <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=120&q=80" alt="Client 3">
                            <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=120&q=80" alt="Client 4">
                        </div>
                        <div>
                            <div class="flex items-center text-amber-500 text-xs gap-1">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <span class="font-black text-slate-900 ml-1 text-xs">4.9 / 5.0</span>
                            </div>
                            <div class="text-[11px] font-medium text-slate-500">Dari 500+ Klien & Alumni Kursus</div>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <i class="fa-solid fa-award text-slate-900 text-base"></i>
                        <span>Studio Resmi & Terakreditasi</span>
                    </div>
                </div>

                <!-- Minimalist Stat Counters -->
                <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-200 max-w-lg">
                    <div>
                        <div class="text-3xl lg:text-4xl font-black text-slate-900">500+</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">Sesi Foto Sukses</div>
                    </div>
                    <div>
                        <div class="text-3xl lg:text-4xl font-black text-slate-900">1,200+</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">Peserta Kursus</div>
                    </div>
                    <div>
                        <div class="text-3xl lg:text-4xl font-black text-slate-900">99.8%</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">Kepuasan Klien</div>
                    </div>
                </div>
            </div>

            <!-- Right Featured Image Showcase -->
            <div class="lg:col-span-5">
                <div class="relative">
                    <div class="aspect-[4/5] rounded-3xl overflow-hidden clean-card relative group shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1000&q=80" alt="RanggaMotret Studio" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                        
                        <div class="absolute bottom-6 left-6 right-6 p-5 rounded-2xl bg-white/95 backdrop-blur-md border border-slate-200 text-xs shadow-xl space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] uppercase tracking-widest text-slate-500 font-extrabold">Signature Aesthetics</span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9px] font-extrabold uppercase">Tersedia Sesi Foto</span>
                            </div>
                            <span class="text-base font-extrabold text-slate-900 block">RanggaMotret Studio & Production</span>
                            <p class="text-[11px] text-slate-500 font-medium">Kamera Sony Alpha Full-Frame, G-Master Lenses & Pencahayaan Studio Profoto.</p>
                        </div>
                    </div>

                    <!-- Floating Floating Badge -->
                    <div class="absolute -top-4 -left-4 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-xs font-bold shadow-xl border border-slate-800 hidden sm:flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        <span>Proofing Galeri Online 48 Jam</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4 Core Pillars of Excellence -->
<section class="py-12 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-md">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Gear Flagship Standar Industri</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">Sony Alpha Full-Frame & prime lens G-Master menjamin ketajaman visual setiap piksel.</p>
            </div>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-md">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Color Grading Sinematik</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">Palet warna elegan & natural skin tone khas RanggaMotret yang tahan uji waktu.</p>
            </div>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-md">
                    <i class="fa-solid fa-cloud-arrow-down text-emerald-400"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Sistem Digital Proofing</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">Pilih foto favorit dan unduh berkas resolusi tinggi langsung dari portal klien Anda.</p>
            </div>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-md">
                    <i class="fa-solid fa-certificate text-blue-400"></i>
                </div>
                <h3 class="font-extrabold text-sm text-slate-900">Sertifikasi & Praktik Nyata</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">Kurikulum akademi disertai evaluasi tugas praktik dan sertifikat resmi terverifikasi.</p>
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
                    <img src="{{ $foto->foto }}" alt="{{ $foto->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
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
                                <span><i class="fa-regular fa-clock mr-1 text-slate-700"></i> {{ $c->durasi }}</span>
                                <span><i class="fa-solid fa-user-tie mr-1 text-slate-700"></i> {{ $c->instruktur->name }}</span>
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

<!-- Section 4: Testimonials (Clean White) -->
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Ulasan Pengguna</span>
            <h2 class="text-3xl font-extrabold text-slate-900">Testimoni Klien & Peserta</h2>
            <p class="text-xs text-slate-600 font-medium">Ulasan nyata dari pasangan pengantin, keluarga, dan alumni akademi RanggaMotret.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimoni as $t)
                <div class="clean-card p-6 rounded-2xl space-y-4 shadow-sm hover:shadow-md transition-all">
                    <div class="flex text-amber-500 text-xs gap-1">
                        @for($i=1; $i<=5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $t->rating ? '' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                    <p class="text-xs text-slate-700 italic leading-relaxed font-medium">"{{ $t->komentar }}"</p>
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
