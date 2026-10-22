@extends('layouts.admin')

@section('title', 'Studio Proofing Foto — ' . $booking->nomor_booking . ' — RanggaMotret.id')

@section('content')
@php
    $fotosData = $booking->galeriFotos->map(function($f, $idx) {
        return [
            'id' => $f->id,
            'index' => $idx,
            'foto_path' => asset($f->foto_path),
            'is_selected' => (bool)$f->is_selected,
            'catatan' => $f->catatan ?? '',
            'code' => '#IMG-' . str_pad($f->id, 4, '0', STR_PAD_LEFT)
        ];
    });
@endphp

<div class="space-y-8" 
     x-data="proofingApp()"
     @keydown.escape.window="closeLightbox()"
     @keydown.arrow-left.window="if(showLightbox) prevPhoto()"
     @keydown.arrow-right.window="if(showLightbox) nextPhoto()"
     @keydown.space.window.prevent="if(showLightbox) toggleActivePhotoSelect()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('customer.bookings.index') }}" class="hover:text-slate-700 transition-colors">Riwayat Reservasi</a>
                <span>/</span>
                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="hover:text-slate-700 transition-colors">{{ $booking->nomor_booking }}</a>
                <span>/</span>
                <span class="text-slate-700">Studio Proofing</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[10px] font-mono font-black text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                    {{ $booking->nomor_booking }}
                </span>
                <span class="text-xs font-bold text-slate-500">• {{ $booking->layanan->nama }} (Paket {{ $booking->paket->nama_paket }})</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 tracking-tight">Studio Proofing & Kurasi Foto</h1>
            <p class="text-xs text-slate-600 font-medium mt-1">
                Tinjau foto resolusi tinggi, pilih foto favorit dengan ikon bintang, dan beri catatan retouching khusus.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn-outline px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                <i class="ph-duotone ph-arrow-left"></i> Detail Reservasi
            </a>
            
            <div class="clean-card px-4 py-2.5 rounded-xl border border-slate-200 flex items-center gap-3 text-xs font-bold shadow-sm bg-white">
                <span class="text-slate-500">Dipilih:</span>
                <span class="px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-900 font-black text-xs" x-text="selectedCount + ' / ' + photos.length">
                    {{ $booking->galeriFotos->where('is_selected', true)->count() }} / {{ $booking->galeriFotos->count() }}
                </span>
            </div>
        </div>
    </div>

    @if($booking->galeriFotos->isEmpty())
        <div class="clean-card p-16 text-center rounded-3xl space-y-4 shadow-sm border border-slate-200 bg-white max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-700 text-2xl flex items-center justify-center mx-auto border border-slate-200">
                <i class="fa-solid fa-camera animate-pulse text-slate-800"></i>
            </div>
            <h3 class="font-black text-lg text-slate-900">Foto Sedang Dalam Proses Sortir</h3>
            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                Tim fotografer RanggaMotret sedang menyelesaikan proses transfer dan unggah foto sesi Anda. Halaman ini akan segera terisi otomatis dalam 24-48 jam.
            </p>
        </div>
    @else
        <!-- Filter Tabs & Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 flex-wrap text-xs font-bold">
                <button type="button" @click="activeFilter = 'all'"
                        class="px-4 py-2 rounded-xl transition-all shadow-sm border"
                        :class="activeFilter === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                    Semua Foto (<span x-text="photos.length"></span>)
                </button>
                <button type="button" @click="activeFilter = 'selected'"
                        class="px-4 py-2 rounded-xl transition-all shadow-sm border flex items-center gap-1.5"
                        :class="activeFilter === 'selected' ? 'bg-amber-500 text-slate-900 border-amber-500' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                    <i class="fa-solid fa-star text-xs"></i>
                    <span>Foto Terpilih (<span x-text="selectedCount"></span>)</span>
                </button>
                <button type="button" @click="activeFilter = 'unselected'"
                        class="px-4 py-2 rounded-xl transition-all shadow-sm border"
                        :class="activeFilter === 'unselected' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                    Belum Dipilih (<span x-text="photos.length - selectedCount"></span>)
                </button>
            </div>

            <div class="text-[11px] text-slate-500 font-medium">
                <i class="fa-solid fa-keyboard mr-1 text-slate-400"></i> Tips: Klik foto untuk zoom layar penuh.
            </div>
        </div>

        <!-- Photos Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 pb-20">
            <template x-for="(foto, index) in filteredPhotos" :key="foto.id">
                <div class="clean-card rounded-2xl overflow-hidden border transition-all duration-200 group relative flex flex-col justify-between bg-white shadow-sm hover:shadow-xl"
                     :class="foto.is_selected ? 'border-amber-400 ring-2 ring-amber-400/30' : 'border-slate-200'">
                    
                    <!-- Image Card Container -->
                    <div class="relative h-64 overflow-hidden bg-slate-100 cursor-pointer"
                         @click="openLightbox(foto.index)">
                        <img :src="foto.foto_path" alt="Proofing Photo" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        
                        <!-- Selected Ribbon Indicator -->
                        <div x-show="foto.is_selected" class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-amber-400 text-slate-900 text-[10px] font-black uppercase tracking-wider shadow-md flex items-center gap-1">
                            <i class="fa-solid fa-star text-[9px]"></i> Terpilih
                        </div>

                        <!-- Zoom Icon on Hover -->
                        <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-slate-900 flex items-center justify-center shadow-lg text-sm">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Card Bottom Details & Star Toggle -->
                    <div class="p-4 flex items-center justify-between text-xs border-t border-slate-100 bg-white">
                        <div class="space-y-0.5">
                            <span class="font-mono text-[11px] font-black text-slate-900" x-text="foto.code"></span>
                            <div x-show="foto.catatan" class="text-[10px] text-slate-500 italic truncate max-w-[130px]" x-text="foto.catatan"></div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Toggle Star Button -->
                            <button type="button" @click.stop="toggleSelect(foto.id)" 
                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-sm transition-all shadow-sm active:scale-90 border"
                                    :class="foto.is_selected ? 'bg-amber-400 text-slate-900 border-amber-500 shadow-amber-300' : 'bg-slate-50 text-slate-400 hover:text-amber-500 border-slate-200 hover:bg-white'"
                                    :title="foto.is_selected ? 'Hapus pilihan' : 'Pilih foto ini'">
                                <i class="fa-solid fa-star"></i>
                            </button>

                            <!-- Download Single Photo -->
                            <a :href="foto.foto_path" download 
                               class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center text-xs transition-colors"
                               title="Unduh resolusi tinggi">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Sticky Floating Bottom Selection Action Bar -->
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 max-w-xl w-[90%] bg-slate-900 text-white p-4 rounded-3xl shadow-2xl border border-slate-800 flex items-center justify-between gap-4 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-sm">
                    <i class="fa-solid fa-camera-retro"></i>
                </div>
                <div class="text-xs">
                    <span class="text-slate-400 font-bold block text-[10px] uppercase tracking-wider">Kurasi Foto Anda</span>
                    <span class="font-black text-white text-sm" x-text="selectedCount + ' Foto Terpilih'"></span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="confirmFinalSubmission()" 
                        class="px-5 py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-900 font-black text-xs uppercase tracking-wider shadow-lg flex items-center gap-2 transition-transform active:scale-95">
                    <i class="fa-solid fa-check text-emerald-600"></i>
                    <span>Kirim Kurasi Final</span>
                </button>
            </div>
        </div>

        <!-- Fullscreen High-Resolution Lightbox Modal -->
        <div x-show="showLightbox" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-6"
             style="display: none;">
            
            <!-- Modal Top Bar -->
            <div class="flex items-center justify-between text-white border-b border-white/10 pb-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-white/10 text-slate-200 text-xs font-mono font-bold"
                          x-text="(activePhotoIndex + 1) + ' / ' + photos.length">
                    </span>
                    <span class="font-mono font-black text-white text-sm" x-text="activePhoto?.code"></span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Toggle Star in Lightbox -->
                    <button type="button" @click="toggleSelect(activePhoto?.id)" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-md"
                            :class="activePhoto?.is_selected ? 'bg-amber-400 text-slate-900' : 'bg-white/10 text-white hover:bg-white/20'">
                        <i class="fa-solid fa-star"></i>
                        <span x-text="activePhoto?.is_selected ? 'Foto Terpilih' : 'Tandai Bintang'"></span>
                    </button>

                    <a :href="activePhoto?.foto_path" download class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-sm" title="Unduh Resolusi Penuh">
                        <i class="fa-solid fa-download"></i>
                    </a>

                    <button @click="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-base" title="Tutup (Esc)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Center Canvas -->
            <div class="relative flex-1 flex items-center justify-center my-4 overflow-hidden">
                <!-- Prev Button -->
                <button @click="prevPhoto()" class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Sebelumnya (Panah Kiri)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <!-- Fullscreen Image -->
                <div class="max-w-5xl max-h-[72vh] flex items-center justify-center overflow-hidden rounded-2xl shadow-2xl">
                    <img :src="activePhoto?.foto_path" 
                         :alt="activePhoto?.code" 
                         class="max-w-full max-h-[72vh] object-contain rounded-2xl transition-all duration-300">
                </div>

                <!-- Next Button -->
                <button @click="nextPhoto()" class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Selanjutnya (Panah Kanan)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- Modal Bottom Notes Drawer -->
            <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 text-white flex flex-col sm:flex-row items-center justify-between gap-4 max-w-4xl mx-auto w-full">
                <div class="flex-1 w-full flex items-center gap-3">
                    <i class="fa-solid fa-comment-pen text-slate-400 text-sm"></i>
                    <input type="text" x-model="activePhotoNotes" @change="savePhotoNotes()" placeholder="Catatan retouching untuk foto ini (misal: tolong bersihkan noda background)..."
                           class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-white transition-colors">
                </div>
                <div class="text-[11px] text-slate-400 font-medium shrink-0">
                    Tekan <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white text-[10px]">Spasi</kbd> untuk pilih bintang
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function proofingApp() {
        return {
            photos: @js($fotosData),
            activeFilter: 'all',
            showLightbox: false,
            activePhotoIndex: 0,
            activePhotoNotes: '',
            
            get filteredPhotos() {
                if (this.activeFilter === 'selected') {
                    return this.photos.filter(p => p.is_selected);
                }
                if (this.activeFilter === 'unselected') {
                    return this.photos.filter(p => !p.is_selected);
                }
                return this.photos;
            },

            get selectedCount() {
                return this.photos.filter(p => p.is_selected).length;
            },

            get activePhoto() {
                return this.photos[this.activePhotoIndex] || null;
            },

            openLightbox(index) {
                this.activePhotoIndex = index;
                this.activePhotoNotes = this.photos[index]?.catatan || '';
                this.showLightbox = true;
                document.body.style.overflow = 'hidden';
            },

            closeLightbox() {
                this.showLightbox = false;
                document.body.style.overflow = '';
            },

            nextPhoto() {
                if (this.activePhotoIndex < this.photos.length - 1) {
                    this.activePhotoIndex++;
                } else {
                    this.activePhotoIndex = 0;
                }
                this.activePhotoNotes = this.activePhoto?.catatan || '';
            },

            prevPhoto() {
                if (this.activePhotoIndex > 0) {
                    this.activePhotoIndex--;
                } else {
                    this.activePhotoIndex = this.photos.length - 1;
                }
                this.activePhotoNotes = this.activePhoto?.catatan || '';
            },

            toggleActivePhotoSelect() {
                if (this.activePhoto) {
                    this.toggleSelect(this.activePhoto.id);
                }
            },

            toggleSelect(fotoId) {
                const photo = this.photos.find(p => p.id === fotoId);
                if (!photo) return;

                // Optimistic UI update
                photo.is_selected = !photo.is_selected;

                fetch(`/customer/proofing/${fotoId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        photo.is_selected = data.is_selected;
                    } else {
                        // Revert on failure
                        photo.is_selected = !photo.is_selected;
                    }
                })
                .catch(() => {
                    photo.is_selected = !photo.is_selected;
                });
            },

            savePhotoNotes() {
                if (!this.activePhoto) return;
                const photo = this.activePhoto;
                photo.catatan = this.activePhotoNotes;

                fetch(`/customer/proofing/${photo.id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        catatan: this.activePhotoNotes
                    })
                });
            },

            confirmFinalSubmission() {
                if (this.selectedCount === 0) {
                    alert('Anda belum memilih foto satupun. Silakan pilih minimal 1 foto bintang terlebih dahulu.');
                    return;
                }
                if (confirm(`Apakah Anda yakin ingin menyelesaikan kurasi ${this.selectedCount} foto terpilih untuk diproses cetak & retouch oleh studio?`)) {
                    alert('Kurasi foto Anda berhasil dikunci! Tim fotografer RanggaMotret akan segera memproses berkas deliverables Anda.');
                }
            }
        }
    }
</script>
@endsection
