@extends('layouts.app')

@section('title', $album->judul . ' — Portofolio RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12"
     x-data="{
        showLightbox: false,
        currentIndex: 0,
        photos: [
            @foreach($album->fotos as $foto)
            {
                foto: @js($foto->foto_url),
                caption: @js($foto->caption ?: $album->judul)
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

    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-slate-700 transition-colors">Beranda</a>
        <span>/</span>
        <a href="{{ route('public.portfolio') }}" class="hover:text-slate-700 transition-colors">Portofolio</a>
        <span>/</span>
        <span class="text-slate-800">{{ $album->judul }}</span>
    </div>

    <!-- Editorial Header Album Hero -->
    <div class="max-w-3xl space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-[11px] font-bold uppercase tracking-wider">
            <i class="ph-duotone ph-camera text-slate-900"></i>
            <span>{{ $album->kategori }}</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            {{ $album->judul }}
        </h1>
        <div class="flex items-center gap-4 text-xs text-slate-500 font-medium pt-1">
            <span class="flex items-center gap-1.5">
                <i class="ph-duotone ph-calendar text-sm text-slate-400"></i>
                {{ date('d F Y', strtotime($album->tanggal)) }}
            </span>
            <span>•</span>
            <span class="flex items-center gap-1.5">
                <i class="ph-duotone ph-images text-sm text-indigo-600"></i>
                <span class="font-bold text-slate-800">{{ $album->fotos->count() }} Foto Dokumentasi</span>
            </span>
        </div>
        @if($album->deskripsi)
            <p class="text-sm text-slate-600 font-medium leading-relaxed pt-2">
                {{ $album->deskripsi }}
            </p>
        @endif
    </div>

    <!-- Photo Gallery Grid with Editorial Masonry Feel -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Foto-foto Isi Album -->
        @forelse($album->fotos as $foto)
            <div class="clean-card rounded-2xl overflow-hidden relative group aspect-[4/3] cursor-pointer bg-slate-100 shadow-sm hover:shadow-2xl transition-all duration-300"
                 @click="openLightbox({{ $loop->index }})">
                <img src="{{ $foto->foto_url }}" alt="{{ $foto->caption ?? $album->judul }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                
                @if($foto->caption)
                    <div class="absolute bottom-3 left-3 right-3 z-10">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-medium truncate block">
                            {{ $foto->caption }}
                        </span>
                    </div>
                @endif

                <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="px-4 py-2 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-2">
                        <i class="ph-duotone ph-arrows-out text-base"></i>
                        <span>Lihat Ukuran Penuh</span>
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full clean-card p-12 text-center rounded-2xl bg-white border border-slate-200 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 text-2xl flex items-center justify-center mx-auto">
                    <i class="ph-duotone ph-images"></i>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900">Belum Ada Foto dalam Album Ini</h3>
                <p class="text-xs text-slate-500 font-medium">Foto-foto dokumentasi album ini sedang disiapkan.</p>
            </div>
        @endforelse
    </div>



    <!-- Conversion Call-to-Action for Customer Booking -->
    <div class="clean-card p-8 sm:p-12 rounded-3xl bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl relative overflow-hidden">
        <div class="space-y-2 text-center sm:text-left z-10 max-w-xl">
            <span class="text-xs font-bold uppercase tracking-widest text-amber-300">Wujudkan Konsep Impian Anda</span>
            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Suka dengan estetika visual album ini?</h3>
            <p class="text-xs sm:text-sm text-slate-300 font-medium">
                Pesan sesi pemotretan Anda sekarang bersama tim fotografer profesional RanggaMotret dengan pencahayaan dan konsep personal.
            </p>
        </div>
        <div class="flex items-center gap-3 z-10 w-full sm:w-auto">
            <a href="{{ route('customer.bookings.create') }}" 
               class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-white hover:bg-slate-100 text-slate-900 text-xs font-black shadow-lg transition-all flex items-center justify-center gap-2 hover:scale-105">
                <i class="ph-duotone ph-calendar-plus text-base text-slate-900"></i>
                <span>Reservasi Sesi Sekarang</span>
            </a>
        </div>
    </div>

    <!-- Related Albums in Same Category -->
    @if(isset($relatedAlbums) && $relatedAlbums->isNotEmpty())
        <div class="space-y-6 pt-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900">Album Terkait Lainnya</h3>
                    <p class="text-xs text-slate-500 font-medium">Jelajahi koleksi karya foto lainnya dalam kategori {{ $album->kategori }}</p>
                </div>
                <a href="{{ route('public.portfolio', ['category' => $album->kategori]) }}" 
                   class="text-xs font-bold text-slate-900 hover:text-indigo-600 flex items-center gap-1 transition-colors">
                    <span>Lihat Semua</span>
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($relatedAlbums as $rel)
                    <a href="{{ route('portofolio.show', $rel->id) }}" 
                       class="clean-card rounded-2xl overflow-hidden group bg-white border border-slate-200 hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                                <img src="{{ $rel->foto_url }}" alt="{{ $rel->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold">
                                        {{ $rel->fotos->count() }} Foto
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $rel->kategori }}</span>
                                <h4 class="font-extrabold text-slate-900 text-sm line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ $rel->judul }}</h4>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Full-Screen Lightbox Multi-Photo Viewer -->
    <template x-teleport="body">
        <div x-show="showLightbox" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-6 lg:p-8"
             style="display: none;">
            
            <!-- Top Lightbox Bar -->
            <div class="flex items-center justify-between text-white border-b border-white/10 pb-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-white/10 text-slate-200 text-xs font-mono font-bold"
                          x-text="`${currentIndex + 1} / ${photos.length}`">
                    </span>
                    <div class="text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">{{ $album->kategori }}</span>
                        <h4 class="text-sm font-extrabold text-white truncate max-w-xs sm:max-w-md" x-text="photos[currentIndex]?.caption || '{{ addslashes($album->judul) }}'"></h4>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-base" title="Tutup (Esc)">
                        <i class="ph ph-x"></i>
                    </button>
                </div>
            </div>

            <!-- Center Image Display -->
            <div class="relative flex-1 flex items-center justify-center my-4 overflow-hidden">
                <button @click="prev()" class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Sebelumnya (Panah Kiri)">
                    <i class="ph ph-caret-left"></i>
                </button>

                <div class="max-w-5xl max-h-[70vh] flex items-center justify-center overflow-hidden rounded-2xl shadow-2xl border border-white/10">
                    <img :src="photos[currentIndex]?.foto" 
                         :alt="photos[currentIndex]?.caption" 
                         class="max-w-full max-h-[70vh] object-contain rounded-2xl transition-all duration-300">
                </div>

                <button @click="next()" class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Selanjutnya (Panah Kanan)">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>

            <!-- Bottom Caption Bar -->
            <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1 max-w-2xl">
                    <p class="text-xs text-slate-300 font-medium leading-relaxed" x-text="photos[currentIndex]?.caption || '{{ addslashes($album->judul) }}'"></p>
                    <div class="flex items-center gap-4 text-[11px] text-slate-400 font-medium">
                        <span><i class="ph-duotone ph-calendar mr-1"></i> Tanggal: <span class="text-white">{{ date('d M Y', strtotime($album->tanggal)) }}</span></span>
                        <span>•</span>
                        <span><i class="ph-duotone ph-camera mr-1"></i> Sony Alpha G-Master Optics</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('customer.bookings.create') }}" class="bg-white hover:bg-slate-100 text-slate-900 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-lg flex-1 sm:flex-initial">
                        <i class="ph-duotone ph-calendar-plus text-slate-900"></i>
                        <span>Reservasi Sesi</span>
                    </a>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
