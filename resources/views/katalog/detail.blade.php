@extends('layouts.app')

@section('title', $layanan->nama . ' — RanggaMotret.id Luxury Photography')

@push('head')
<!-- Google Fonts: Cormorant Garamond for Luxury Editorial Serif -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
<style>
    .font-serif-luxury {
        font-family: 'Cormorant Garamond', Georgia, serif;
    }
    .badge-gold {
        background: linear-gradient(135deg, #d4a574 0%, #c49756 100%);
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="space-y-16 lg:space-y-24 pb-20">

    <!-- 1. Breadcrumb Header Section -->
    <section class="bg-white border-b border-slate-200/80 pt-8 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-[11px]"></i> Beranda
                </a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <a href="{{ route('public.services') }}" class="hover:text-slate-900 transition-colors">Katalog Layanan</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <span class="text-slate-800 font-bold px-2 py-0.5 rounded bg-slate-100 text-[11px]">{{ $layanan->kategori }}</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <span class="text-slate-900 font-extrabold truncate">{{ $layanan->nama }}</span>
            </nav>
        </div>
    </section>

    <!-- 2. Hero Section: Cinematic Editorial Showcase -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            <!-- Left Narrative & Booking Hook -->
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 text-white text-[10px] sm:text-xs font-bold uppercase tracking-widest shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Signature {{ $layanan->kategori }} Experience</span>
                </div>

                <div class="space-y-2">
                    <h1 class="font-serif-luxury text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-900 leading-[1.1] tracking-tight">
                        {{ $layanan->nama }}
                    </h1>
                    <p class="text-base sm:text-lg text-slate-500 font-serif-luxury italic">
                        Abadikan keindahan setiap detik dengan narasi visual yang abadi, elegan, dan penuh rasa.
                    </p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl font-medium">
                    {{ $layanan->deskripsi }}
                </p>

                <!-- Value Highlights Metric Pills -->
                <div class="grid grid-cols-3 gap-3 pt-2 max-w-xl">
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-sm text-center">
                        <div class="flex items-center justify-center gap-1 text-amber-500 text-xs mb-1">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="text-[11px] font-black text-slate-900">4.9 / 5.0 Rating</div>
                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">Kepuasan Klien</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-sm text-center">
                        <div class="text-slate-900 text-sm mb-0.5">
                            <i class="fa-solid fa-camera-retro text-slate-700"></i>
                        </div>
                        <div class="text-[11px] font-black text-slate-900">Full-Frame Prime</div>
                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">Cinema Optics</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-sm text-center">
                        <div class="text-emerald-600 text-sm mb-0.5">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="text-[11px] font-black text-slate-900">100% Guaranteed</div>
                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">On-Time Delivery</div>
                    </div>
                </div>

                <!-- Price Anchor & Primary CTA Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <div class="bg-slate-900 text-white p-4 px-6 rounded-2xl shadow-lg border border-slate-800">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Harga Mulai Dari</span>
                        <div class="text-2xl sm:text-3xl font-black text-white mt-0.5 flex items-baseline gap-1">
                            <span>Rp {{ number_format($layanan->harga_mulai, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 grow">
                        <a href="#paket-pilihan" class="btn-dark px-6 py-4 rounded-2xl text-xs font-bold uppercase tracking-wider text-center grow shadow-md hover:shadow-xl transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-calendar-check text-amber-400"></i> Lihat Pilihan Paket
                        </a>
                        <a href="{{ route('public.consultation') }}" class="btn-outline px-5 py-4 rounded-2xl text-xs font-bold text-center border-slate-300 hover:border-slate-900 transition-all flex items-center justify-center gap-2 shrink-0" title="Konsultasi Konsep">
                            <i class="fa-solid fa-comments text-slate-600"></i> Tanya Tim
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Featured Visual Card -->
            <div class="lg:col-span-5">
                <div class="relative group">
                    <!-- Decorative Soft Background Ambient Glow -->
                    <div class="absolute -inset-4 bg-gradient-to-tr from-slate-200 to-amber-100 rounded-3xl blur-2xl opacity-60 group-hover:opacity-100 transition duration-700"></div>

                    <div class="relative aspect-[4/5] rounded-3xl overflow-hidden clean-card shadow-2xl border border-slate-200/80 bg-slate-950">
                        <img src="{{ $layanan->thumbnail }}" alt="{{ $layanan->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                        <!-- Floating Corner Tag -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 rounded-xl bg-white/90 backdrop-blur-md text-slate-900 font-black text-[10px] uppercase tracking-wider shadow-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-award text-amber-600"></i> RanggaMotret Masterclass
                            </span>
                        </div>

                        <!-- Floating Bottom Overlay Card -->
                        <div class="absolute bottom-5 left-5 right-5 p-4 rounded-2xl bg-slate-900/85 backdrop-blur-md border border-white/10 text-white shadow-xl space-y-1">
                            <div class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Standard of Artistry</div>
                            <div class="text-sm font-bold truncate">{{ $layanan->nama }} by Rangga Studio</div>
                            <div class="text-[11px] text-slate-300 flex items-center gap-3 pt-1 border-t border-white/10">
                                <span><i class="fa-solid fa-check text-emerald-400 mr-1"></i> Private Proofing</span>
                                <span><i class="fa-solid fa-check text-emerald-400 mr-1"></i> Cloud Download</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 3. Signature Features / 4 Pillars of Quality -->
    <section class="bg-white border-y border-slate-200/80 py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Keunggulan Layanan</span>
                <h2 class="font-serif-luxury text-3xl sm:text-4xl font-bold text-slate-900">Sentuhan Eksklusif di Setiap Jepretan</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Standar kerja profesional berorientasi pada kenyamanan Anda dan hasil visual bermutu tinggi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pillar 1 -->
                <div class="clean-card p-6 rounded-2xl space-y-3 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Grading Warna Sinematik</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Penyelarasan palet warna khas RanggaMotret dengan nuansa hangat, bersih, dan tak lekang oleh waktu (*timeless*).</p>
                </div>

                <!-- Pillar 2 -->
                <div class="clean-card p-6 rounded-2xl space-y-3 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Optik & Sensor Full-Frame</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Didukung kamera beresolusi tinggi dan lensa prime ber-aperture lebar untuk ketajaman detail dan efek *depth-of-field* alami.</p>
                </div>

                <!-- Pillar 3 -->
                <div class="clean-card p-6 rounded-2xl space-y-3 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Portal Proofing Klien</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pilih foto favorit Anda dari rumah via galeri digital interaktif kami sebelum proses retouch akhir dikerjakan.</p>
                </div>

                <!-- Pillar 4 -->
                <div class="clean-card p-6 rounded-2xl space-y-3 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Akses Cloud Berkecepatan Tinggi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Unduh semua berkas foto original dan editan beresolusi cetak tinggi kapan saja melalui penyimpanan cloud aman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Package Pricing Tiers (Pricing Page + CTA Pattern) -->
    <section id="paket-pilihan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 scroll-mt-24">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Pilihan Investasi Dokumentasi</span>
                <h2 class="font-serif-luxury text-3xl sm:text-4xl font-bold text-slate-900 mt-1">Paket Sesi Fotografi</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Transparan tanpa biaya tersembunyi. Pilih paket yang paling cocok dengan kebutuhan acara Anda.</p>
            </div>
            
            <div class="text-xs text-slate-600 font-medium bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-slate-500"></i>
                <span>Semua paket termasuk koreksi warna & hak cetak penuh</span>
            </div>
        </div>

        @php
            $activePakets = $layanan->pakets->where('status', 'aktif');
            $totalPakets = $activePakets->count();
        @endphp

        @if($activePakets->isEmpty())
            <div class="clean-card p-12 rounded-3xl text-center space-y-4 max-w-xl mx-auto border border-dashed border-slate-300">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-slate-900 text-lg">Paket Sedang Diperbarui</h3>
                    <p class="text-xs text-slate-500">Varian paket untuk layanan ini sedang disesuaikan dengan kurasi terbaru. Silakan hubungi tim kami untuk penawaran khusus langsung.</p>
                </div>
                <a href="{{ route('public.consultation') }}" class="inline-flex items-center gap-2 btn-dark px-6 py-3 rounded-xl text-xs font-bold">
                    <i class="fa-solid fa-comments"></i> Konsultasi Langsung
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
                @foreach($activePakets as $index => $p)
                    @php
                        // Highlight the middle or designated popular package
                        $isPopular = ($totalPakets >= 2 && $index === 1) || ($totalPakets === 1);
                    @endphp

                    <div class="clean-card rounded-3xl flex flex-col justify-between transition-all duration-300 relative text-xs
                        {{ $isPopular ? 'border-2 border-slate-900 shadow-2xl scale-[1.02] bg-white z-10' : 'border border-slate-200 hover:border-slate-400 shadow-sm hover:shadow-xl bg-white' }}">
                        
                        <!-- Most Popular Tag -->
                        @if($isPopular)
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                                <span class="badge-gold px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-md flex items-center gap-1.5">
                                    <i class="fa-solid fa-crown text-[9px]"></i> Paling Direkomendasikan
                                </span>
                            </div>
                        @endif

                        <div class="p-6 sm:p-8 space-y-6">
                            <!-- Package Title & Price Header -->
                            <div class="border-b border-slate-100 pb-5 space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-serif-luxury text-2xl font-bold text-slate-900 tracking-tight">{{ $p->nama_paket }}</h3>
                                    @if($p->durasi)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-[10px] border border-slate-200 shrink-0">
                                            <i class="fa-regular fa-clock mr-1 text-slate-500"></i>{{ $p->durasi }}
                                        </span>
                                    @endif
                                </div>

                                <div class="pt-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Biaya Sesi</span>
                                    <div class="text-3xl font-black text-slate-900 mt-0.5 tracking-tight">
                                        Rp {{ number_format($p->harga, 0, ',', '.') }}
                                    </div>
                                </div>

                                @if($p->deskripsi)
                                    <p class="text-xs text-slate-600 leading-relaxed font-medium pt-1">{{ $p->deskripsi }}</p>
                                @endif
                            </div>

                            <!-- Deliverables & Core Features Checklist -->
                            <div class="space-y-3">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Deliverables & Tim:</span>
                                
                                <div class="flex items-start gap-3 text-slate-800 font-medium">
                                    <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span><strong>{{ $p->jumlah_fotografer }} Fotografer</strong> Profesional Utama</span>
                                </div>

                                <div class="flex items-start gap-3 text-slate-800 font-medium">
                                    <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span>Minimal <strong>{{ $p->jumlah_foto }} Foto</strong> Master High-End Retouch</span>
                                </div>

                                <div class="flex items-start gap-3 text-slate-800 font-medium">
                                    <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span>Seluruh Berkas Mentah (Original) dalam Google Drive</span>
                                </div>

                                <div class="flex items-start gap-3 text-slate-800 font-medium">
                                    <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span>Akses Seleksi Foto via Private Proofing Gallery</span>
                                </div>

                                @if($p->fasilitas)
                                    <div class="mt-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Fasilitas Tambahan:</span>
                                        <div class="text-xs text-slate-700 leading-relaxed font-medium">{{ $p->fasilitas }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Action Button -->
                        <div class="p-6 sm:p-8 pt-0">
                            <a href="{{ route('customer.bookings.create', ['layanan_id' => $layanan->id, 'paket_id' => $p->id]) }}" 
                               class="w-full py-4 rounded-xl text-center text-xs font-bold uppercase tracking-wider block shadow-md hover:shadow-xl transition-all
                               {{ $isPopular ? 'btn-dark text-white' : 'bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-900 border border-slate-200' }}">
                                <i class="fa-solid fa-calendar-plus mr-1.5"></i> Reservasi Paket Ini
                            </a>

                            @guest
                                <p class="text-[10px] text-slate-400 text-center mt-2.5 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-lock text-[9px]"></i> Reservasi aman dengan akun terdaftar
                                </p>
                            @endguest
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- 5. The Workflow Experience (How It Works) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="clean-card p-8 sm:p-12 rounded-3xl space-y-10 bg-slate-900 text-white border-slate-800">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400">Alur Kerja Profesional</span>
                <h2 class="font-serif-luxury text-3xl sm:text-4xl font-bold">Proses Dari Reservasi Hingga Hasil Jadi</h2>
                <p class="text-xs sm:text-sm text-slate-400 font-medium">Langkah terstruktur agar sesi dokumentasi Anda berjalan santai, tepat waktu, dan menyenangkan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="space-y-3 relative">
                    <div class="font-serif-luxury text-4xl font-black text-slate-700">01</div>
                    <h3 class="font-bold text-white text-base">Reservasi & Konsep</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Pilih paket dan tentukan tanggal sesi pemotretan. Tim kami akan menghubungi Anda untuk menyelaraskan konsep & *moodboard*.</p>
                </div>

                <!-- Step 2 -->
                <div class="space-y-3 relative">
                    <div class="font-serif-luxury text-4xl font-black text-slate-700">02</div>
                    <h3 class="font-bold text-white text-base">Hari Pemotretan</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Fotografer kami memandu pose secara natural dengan pencahayaan profesional, tanpa rasa kaku atau canggung.</p>
                </div>

                <!-- Step 3 -->
                <div class="space-y-3 relative">
                    <div class="font-serif-luxury text-4xl font-black text-slate-700">03</div>
                    <h3 class="font-bold text-white text-base">Client Proofing</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Kami mengunggah preview foto ke akun Anda. Anda bebas memilih foto terbaik dan memberikan catatan editing secara online.</p>
                </div>

                <!-- Step 4 -->
                <div class="space-y-3 relative">
                    <div class="font-serif-luxury text-4xl font-black text-slate-700">04</div>
                    <h3 class="font-bold text-white text-base">Master Delivery</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Hasil akhir di-retouch dengan teliti dan dikirimkan dalam resolusi maksimal, siap dicetak maupun dibagikan ke media sosial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Related Portfolio Showcase (If Available) -->
    @if(isset($relatedPortofolio) && $relatedPortofolio->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Galeri Hasil Karya</span>
                    <h2 class="font-serif-luxury text-3xl font-bold text-slate-900 mt-0.5">Portofolio {{ $layanan->nama }}</h2>
                </div>
                <a href="{{ route('public.portfolio', ['category' => $layanan->kategori]) }}" class="text-xs font-bold text-slate-900 hover:text-slate-600 flex items-center gap-1">
                    Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($relatedPortofolio as $port)
                    <div class="group relative rounded-2xl overflow-hidden aspect-[3/4] clean-card shadow-sm hover:shadow-xl transition-all">
                        <img src="{{ $port->foto_url }}" alt="{{ $port->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-4 flex flex-col justify-end text-white">
                            <span class="text-[10px] uppercase font-bold text-amber-300">{{ $port->kategori }}</span>
                            <div class="text-xs font-bold truncate">{{ $port->judul }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 7. Interactive Service FAQ (Alpine.js Accordion) -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ activeFaq: null }">
        <div class="text-center space-y-2">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Pertanyaan Umum</span>
            <h2 class="font-serif-luxury text-3xl font-bold text-slate-900">Hal yang Kerap Ditanyakan Klien</h2>
            <p class="text-xs text-slate-500 font-medium">Segala hal yang perlu Anda ketahui mengenai sesi pemotretan di RanggaMotret.</p>
        </div>

        <div class="space-y-3">
            <!-- FAQ 1 -->
            <div class="clean-card rounded-2xl overflow-hidden border border-slate-200">
                <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-xs sm:text-sm hover:bg-slate-50 transition-colors">
                    <span>Berapa lama proses editing hingga hasil foto final diterima?</span>
                    <i class="fa-solid text-slate-400 text-xs" :class="activeFaq === 1 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 1" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Pratinjau foto mentah (*preview*) untuk sesi proofing akan diunggah ke akun Anda maksimal <strong>2-3 hari kerja</strong> setelah pemotretan. Setelah Anda memilih foto favorit, proses *master retouching* memerlukan waktu <strong>7 hingga 14 hari kerja</strong> tergantung pada varian paket yang Anda pilih.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="clean-card rounded-2xl overflow-hidden border border-slate-200">
                <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-xs sm:text-sm hover:bg-slate-50 transition-colors">
                    <span>Apakah seluruh berkas foto mentah (original) akan diberikan?</span>
                    <i class="fa-solid text-slate-400 text-xs" :class="activeFaq === 2 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 2" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    <strong>Ya, 100%!</strong> Kami memberikan seluruh file foto asli (*high-resolution JPEG*) melalui tautan cloud storage eksklusif yang dapat Anda unduh dan simpan selamanya, selain foto-foto yang di-retouch secara khusus.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="clean-card rounded-2xl overflow-hidden border border-slate-200">
                <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-xs sm:text-sm hover:bg-slate-50 transition-colors">
                    <span>Bagaimana jika kami perlu menjadwalkan ulang (reschedule)?</span>
                    <i class="fa-solid text-slate-400 text-xs" :class="activeFaq === 3 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 3" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Penjadwalan ulang dapat dilakukan tanpa biaya tambahan jika dikonfirmasikan minimal <strong>5 hari sebelum tanggal sesi</strong> (atau karena faktor cuaca buruk mendadak untuk sesi outdoor). Tim kami akan membantu mencocokkan kembali dengan ketersediaan kalender studio.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="clean-card rounded-2xl overflow-hidden border border-slate-200">
                <button @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-xs sm:text-sm hover:bg-slate-50 transition-colors">
                    <span>Apakah RanggaMotret melayani pemotretan di luar kota?</span>
                    <i class="fa-solid text-slate-400 text-xs" :class="activeFaq === 4 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 4" x-collapse class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Tentu saja! Kami berpengalaman meliput acara pernikahan dan prewedding di berbagai kota di Indonesia. Untuk sesi di luar area utama, terdapat penyesuaian biaya akomodasi dan transportasi yang akan dibahas transparan pada sesi konsultasi.
                </div>
            </div>
        </div>
    </section>

    <!-- 8. VIP Climax Call to Action Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl p-8 sm:p-14 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white relative overflow-hidden shadow-2xl border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-3 max-w-xl text-center md:text-left z-10">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400">Konsultasi Pribadi</span>
                <h3 class="font-serif-luxury text-3xl sm:text-4xl font-bold leading-tight">Punya Konsep Khusus yang Ingin Diwujudkan?</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed font-medium">Diskusikan referensi moodboard, busana, atau kebutuhan unik dokumentasi Anda bersama tim profesional kami secara gratis.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 z-10 w-full md:w-auto">
                <a href="{{ route('public.consultation') }}" class="btn-dark bg-white text-slate-900 hover:bg-slate-100 px-7 py-4 rounded-xl text-xs font-bold uppercase tracking-wider w-full sm:w-auto text-center shadow-lg">
                    <i class="fa-solid fa-paper-plane mr-2 text-slate-800"></i> Kirim Form Konsultasi
                </a>
                <a href="#paket-pilihan" class="px-7 py-4 rounded-xl text-xs font-bold uppercase tracking-wider w-full sm:w-auto text-center border border-white/20 hover:bg-white/10 text-white transition-colors">
                    Lihat Paket Sesi
                </a>
            </div>

            <!-- Background subtle ornament -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>
    </section>

</div>
@endsection
