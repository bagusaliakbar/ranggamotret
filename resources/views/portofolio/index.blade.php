@extends('layouts.app')

@section('title', 'Portofolio Karya Fotografi — RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12"
     x-data="{
        showLightbox: false,
        currentIndex: 0,
        photos: [
            @foreach($portofolio as $p)
            {
                id: {{ $p->id }},
                judul: @js($p->judul),
                kategori: @js($p->kategori),
                foto: @js($p->foto_url),
                deskripsi: @js($p->deskripsi),
                tanggal: @js(date('d M Y', strtotime($p->tanggal)))
            },
            @endforeach
        ],
        openLightbox(index) {
            this.currentIndex = index;
            this.showLightbox = true;
            document.body.style.overflow = 'hidden';
        },
        closeLightbox() {
            this.showLightbox = false;
            document.body.style.overflow = '';
        },
        next() {
            if (this.currentIndex < this.photos.length - 1) {
                this.currentIndex++;
            } else {
                this.currentIndex = 0;
            }
        },
        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
            } else {
                this.currentIndex = this.photos.length - 1;
            }
        }
     }"
     @keydown.escape.window="closeLightbox()"
     @keydown.arrow-right.window="if(showLightbox) next()"
     @keydown.arrow-left.window="if(showLightbox) prev()">

    <!-- Header Section -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-slate-900"></span> Galeri Hasil Pemotretan
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
            Portofolio <span class="font-serif italic font-normal text-slate-600">Dokumentasi</span> Visual.
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed max-w-2xl mx-auto">
            Koleksi karya pemotretan autentik RanggaMotret dari pernikahan sakral, momen prewedding intim, hingga potret personal dan kebutuhan komersial. Klik foto untuk melihat detail resolusi tinggi.
        </p>
    </div>

    <!-- Category Filters -->
    <div class="flex items-center justify-center gap-2 flex-wrap text-xs font-bold">
        <a href="{{ route('public.portfolio') }}" 
           class="px-5 py-2.5 rounded-xl transition-all shadow-sm {{ !$category ? 'bg-slate-900 text-white shadow-slate-900/10' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:border-slate-300' }}">
            Semua Kategori ({{ $portofolio->count() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('public.portfolio', ['category' => $cat]) }}" 
               class="px-5 py-2.5 rounded-xl transition-all shadow-sm {{ $category === $cat ? 'bg-slate-900 text-white shadow-slate-900/10' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:border-slate-300' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    @if($portofolio->isEmpty())
        <div class="clean-card p-16 text-center rounded-3xl space-y-4 max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 text-2xl flex items-center justify-center mx-auto">
                <i class="ph-duotone ph-images"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum Ada Karya dalam Kategori Ini</h3>
            <p class="text-xs text-slate-500 font-medium">Silakan pilih kategori lainnya atau kembali ke seluruh katalog karya.</p>
            <a href="{{ route('public.portfolio') }}" class="btn-dark inline-block px-5 py-2.5 rounded-xl text-xs font-bold mt-2">
                Lihat Semua Portofolio
            </a>
        </div>
    @else
        <!-- Gallery Grid with Lightbox Trigger -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($portofolio as $index => $p)
                <div class="clean-card rounded-2xl overflow-hidden group relative h-96 cursor-pointer shadow-sm hover:shadow-2xl transition-all duration-300"
                     @click="openLightbox({{ $index }})">
                    
                    <img src="{{ $p->foto_url }}" alt="{{ $p->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    
                    <!-- Top Category Chip -->
                    <div class="absolute top-4 left-4 z-10">
                        <span class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider border border-white/10 shadow-sm">
                            {{ $p->kategori }}
                        </span>
                    </div>

                    <!-- Magnify Zoom Hover Badge -->
                    <div class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/90 backdrop-blur-md text-slate-900 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity shadow-md">
                        <i class="fa-solid fa-expand"></i>
                    </div>

                    <!-- Overlay Bottom Details -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent opacity-85 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end text-white">
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ $p->kategori }}</span>
                        <h3 class="text-xl font-bold text-white mt-1">{{ $p->judul }}</h3>
                        <p class="text-xs text-slate-200 mt-1 line-clamp-2 leading-relaxed font-normal">{{ $p->deskripsi }}</p>
                        
                        <div class="flex items-center justify-between text-[11px] text-slate-300 mt-3 pt-3 border-t border-white/15">
                            <span class="font-medium"><i class="fa-regular fa-calendar mr-1"></i> {{ date('d M Y', strtotime($p->tanggal)) }}</span>
                            <span class="font-bold text-amber-300 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                <span>Lihat HD</span> &rarr;
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Full-Screen Studio Lightbox Modal -->
    <div x-show="showLightbox" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-6 lg:p-8"
         style="display: none;">
        
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between text-white border-b border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-white/10 text-slate-200 text-xs font-mono font-bold"
                      x-text="`${currentIndex + 1} / ${photos.length}`">
                </span>
                <div class="text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]" x-text="photos[currentIndex]?.kategori"></span>
                    <h4 class="text-sm font-extrabold text-white truncate max-w-xs sm:max-w-md" x-text="photos[currentIndex]?.judul"></h4>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('customer.bookings.create') }}" class="hidden sm:inline-flex btn-dark bg-white hover:bg-slate-100 text-slate-900 px-4 py-2 rounded-xl text-xs font-bold items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-slate-700"></i> Pesan Sesi Serupa
                </a>
                <button @click="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-base" title="Tutup (Esc)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Center Image Display with Nav Arrows -->
        <div class="relative flex-1 flex items-center justify-center my-4 overflow-hidden">
            <!-- Prev Button -->
            <button @click="prev()" class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Sebelumnya (Panah Kiri)">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <!-- Image View -->
            <div class="max-w-5xl max-h-[70vh] flex items-center justify-center overflow-hidden rounded-2xl shadow-2xl border border-white/10">
                <img :src="photos[currentIndex]?.foto" 
                     :alt="photos[currentIndex]?.judul" 
                     class="max-w-full max-h-[70vh] object-contain rounded-2xl transition-all duration-300">
            </div>

            <!-- Next Button -->
            <button @click="next()" class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Selanjutnya (Panah Kanan)">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- Bottom Description & Conversion CTA -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1 max-w-2xl">
                <p class="text-xs text-slate-300 font-medium leading-relaxed" x-text="photos[currentIndex]?.deskripsi"></p>
                <div class="flex items-center gap-4 text-[11px] text-slate-400 font-medium">
                    <span><i class="fa-regular fa-calendar mr-1"></i> Tanggal: <span class="text-white" x-text="photos[currentIndex]?.tanggal"></span></span>
                    <span>•</span>
                    <span><i class="fa-solid fa-camera mr-1"></i> Sony Alpha G-Master Optics</span>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a :href="photos[currentIndex]?.foto" target="_blank" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold flex items-center justify-center gap-2 transition-colors flex-1 sm:flex-initial">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Fullscreen
                </a>
                <a href="{{ route('customer.bookings.create') }}" class="btn-dark bg-white hover:bg-slate-100 text-slate-900 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-lg flex-1 sm:flex-initial">
                    <i class="fa-solid fa-calendar-plus"></i> Reservasi Sesi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
