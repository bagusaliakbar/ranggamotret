@extends('layouts.admin')

@section('title', 'Detail Reservasi ' . $booking->nomor_booking . ' - Owner')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.bookings.index') }}" class="hover:text-slate-700 transition-colors">Kelola Reservasi</a>
                <span>/</span>
                <span class="text-slate-700">{{ $booking->nomor_booking }}</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $booking->nomor_booking }}</h1>
        </div>
        
        <div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase border tracking-wider
                {{ in_array($booking->status, ['Dibayar', 'Selesai', 'Pemotretan Selesai', 'Terjadwal']) ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (in_array($booking->status, ['Dibatalkan', 'Ditolak']) ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-amber-50 border-amber-200 text-amber-800') }}">
                {{ $booking->status }}
            </span>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <div class="clean-card p-6 rounded-3xl space-y-5">
            <h3 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Data Pelanggan & Layanan</h3>
            <div class="space-y-4">
                <div>
                    <span class="text-slate-500 block mb-1">Nama Pelanggan:</span>
                    <span class="font-extrabold text-slate-900 text-sm">{{ $booking->customer?->name ?? 'Guest User' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block mb-1">Kontak WA/Email:</span>
                    <span class="text-slate-700 font-medium">{{ $booking->customer?->phone ?? '-' }} ({{ $booking->customer?->email ?? '-' }})</span>
                </div>
                @if($booking->isKursus())
                    <div>
                        <span class="text-slate-500 block mb-1">Program Kursus:</span>
                        <span class="text-slate-900 font-bold">{{ $booking->kursus?->nama ?? 'Kursus Akademi Fotografi' }} (Level: {{ $booking->kursus?->level ?? 'Semua Level' }})</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Harga Tagihan:</span>
                        <span class="font-extrabold text-slate-900 text-sm">Rp {{ number_format($booking->kursus?->harga ?? ($booking->pembayaran?->jumlah ?? 0), 0, ',', '.') }}</span>
                    </div>
                @else
                    <div>
                        <span class="text-slate-500 block mb-1">Layanan & Paket:</span>
                        <span class="text-slate-900 font-bold">{{ $booking->layanan?->nama ?? 'Layanan Fotografi' }} - {{ $booking->paket?->nama_paket ?? 'Paket Foto' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Harga Tagihan:</span>
                        <span class="font-extrabold text-slate-900 text-sm">Rp {{ number_format($booking->paket?->harga ?? ($booking->pembayaran?->jumlah ?? 0), 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="clean-card p-6 rounded-3xl space-y-5">
            <h3 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Jadwal & Lokasi</h3>
            <div class="space-y-4">
                @if($booking->isKursus())
                    <div>
                        <span class="text-slate-500 block mb-1">Tanggal Pendaftaran:</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $booking->created_at ? $booking->created_at->format('d F Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Mode Pelaksanaan:</span>
                        <span class="text-slate-700 font-medium">{{ $booking->lokasi ?: 'Online LMS / Studio RanggaMotret' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Akses Materi:</span>
                        <span class="text-slate-600 leading-relaxed">{{ in_array($booking->status, ['Dibayar', 'Selesai', 'aktif']) ? 'Akses Aktif (Bisa langsung belajar LMS)' : 'Menunggu pembayaran lunas' }}</span>
                    </div>
                @else
                    <div>
                        <span class="text-slate-500 block mb-1">Tanggal Pemotretan:</span>
                        <span class="font-extrabold text-slate-900 text-sm">
                            {{ $booking->tanggal ? date('d F Y', strtotime($booking->tanggal)) : '-' }} 
                            @if($booking->waktu)
                                ({{ substr($booking->waktu, 0, 5) }} WIB)
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Alamat / Venue:</span>
                        <span class="text-slate-700 font-medium">{{ $booking->lokasi ?: 'Studio RanggaMotret' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Kebutuhan Khusus:</span>
                        <span class="text-slate-600 leading-relaxed">{{ $booking->kebutuhan_khusus ?? '-' }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($booking->isFotografi())
        @php
            $adminFotosData = $booking->galeriFotos->map(function($f, $idx) {
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

        <!-- Owner Photo Deliverables Uploader & Proofing Manager -->
        <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6"
             x-data="{
                files: [],
                previews: [],
                isDragging: false,
                photos: {{ Js::from($adminFotosData) }},
                showLightbox: false,
                activePhotoIndex: 0,
                openLightbox(index) {
                    this.activePhotoIndex = index;
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
                },
                prevPhoto() {
                    if (this.activePhotoIndex > 0) {
                        this.activePhotoIndex--;
                    } else {
                        this.activePhotoIndex = this.photos.length - 1;
                    }
                },
                get activePhoto() {
                    return this.photos[this.activePhotoIndex] || null;
                },
                handleFiles(selectedFiles) {
                    const valid = Array.from(selectedFiles).filter(f => f.type.startsWith('image/'));
                    this.files = valid;
                    this.previews = [];
                    valid.slice(0, 12).forEach(f => {
                        const reader = new FileReader();
                        reader.onload = (e) => this.previews.push({ name: f.name, size: (f.size / (1024 * 1024)).toFixed(1) + ' MB', url: e.target.result });
                        reader.readAsDataURL(f);
                    });
                },
                removeFile(idx) {
                    this.files.splice(idx, 1);
                    this.previews.splice(idx, 1);
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    this.$refs.fileInput.files = dt.files;
                },
                resetFiles() {
                    this.files = [];
                    this.previews = [];
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                }
             }"
             @keydown.escape.window="closeLightbox()"
             @keydown.arrow-left.window="if(showLightbox) prevPhoto()"
             @keydown.arrow-right.window="if(showLightbox) nextPhoto()">
            <!-- Card Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0 border border-purple-100">
                        <i class="ph-duotone ph-camera-rotate"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Unggah Hasil Foto Pemotretan</h3>
                        <p class="text-xs text-slate-500 font-medium">Foto yang diunggah akan otomatis tersedia di portal proofing & unduhan pelanggan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('customer.proofing.show', $booking->id) }}" class="inline-flex items-center gap-1.5 text-xs font-black text-purple-700 bg-purple-50 hover:bg-purple-100 px-3.5 py-1.5 rounded-xl border border-purple-200 transition-colors shadow-2xs">
                        <i class="ph-duotone ph-eye text-sm"></i>
                        <span>Lihat Tampilan Pelanggan</span>
                    </a>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black text-slate-700 bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-200">
                        <i class="ph-duotone ph-images text-slate-500 text-sm"></i>
                        <span>{{ $booking->galeriFotos->count() }} Foto Terunggah</span>
                    </span>
                </div>
            </div>

            <!-- Upload Area Form -->
            <form method="POST" action="{{ route('admin.proofing.upload', $booking->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="fotos[]" multiple required accept="image/*" x-ref="fileInput"
                       @change="handleFiles($event.target.files)"
                       class="hidden">

                <!-- Interactive Drag and Drop Zone -->
                <div @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files); $refs.fileInput.files = $event.dataTransfer.files"
                     @click="$refs.fileInput.click()"
                     :class="isDragging ? 'border-purple-600 bg-purple-50/60 scale-[1.005]' : 'border-slate-200 hover:border-purple-400 bg-slate-50/50 hover:bg-white'"
                     class="group relative cursor-pointer border-2 border-dashed rounded-3xl p-8 sm:p-10 transition-all duration-300 flex flex-col items-center justify-center text-center">
                    
                    <!-- Decorative Background Glow -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-linear-to-tr from-purple-100 via-indigo-50 to-pink-100 text-purple-700 flex items-center justify-center text-3xl sm:text-4xl mb-4 group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                        <i class="ph-duotone ph-cloud-arrow-up"></i>
                    </div>

                    <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mb-1">
                        Tarik & Lepaskan File Foto ke Sini, atau <span class="text-purple-600 underline underline-offset-4 decoration-purple-300">Pilih Berkas</span>
                    </h4>
                    <p class="text-xs text-slate-500 max-w-md font-medium leading-relaxed">
                        Mendukung format JPG, PNG, WEBP resolusi tinggi. Dapat memilih banyak foto sekaligus untuk sesi pemotretan ini.
                    </p>

                    <!-- Hint specs -->
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-[11px] font-bold text-slate-400">
                        <span class="inline-flex items-center gap-1 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                            <i class="ph-duotone ph-image text-xs"></i> Multi-upload
                        </span>
                        <span class="inline-flex items-center gap-1 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                            <i class="ph-duotone ph-sparkle text-xs text-amber-500"></i> Max 20MB / file
                        </span>
                    </div>
                </div>

                <!-- Selected Files Preview Tray (Muncul saat user memilih foto) -->
                <div x-show="previews.length > 0" x-cloak class="p-5 rounded-2xl bg-white border border-slate-200 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-black text-slate-900" x-text="`${files.length} Foto Siap Diunggah`"></span>
                        </div>
                        <button type="button" @click="resetFiles()" class="text-xs text-rose-600 hover:text-rose-700 font-bold inline-flex items-center gap-1">
                            <i class="ph-duotone ph-x-circle"></i> Batal Semua
                        </button>
                    </div>

                    <!-- Mini thumbnail preview grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-8 gap-3 max-h-56 overflow-y-auto pr-1">
                        <template x-for="(p, idx) in previews" :key="idx">
                            <div class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shadow-2xs">
                                <img :src="p.url" :alt="p.name" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" @click="removeFile(idx)" class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center hover:bg-rose-700 text-xs shadow-md">
                                        <i class="ph-bold ph-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs inline-flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all active:scale-95">
                            <i class="ph-duotone ph-upload-simple text-base"></i>
                            <span x-text="`Mulai Unggah ${files.length} Foto`"></span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Existing Photos Grid -->
            @if($booking->galeriFotos->count() > 0)
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Galeri Foto Tersimpan</span>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('customer.proofing.show', $booking->id) }}" class="text-xs font-extrabold text-purple-700 hover:text-purple-800 inline-flex items-center gap-1">
                                <i class="ph-duotone ph-arrows-out-simple"></i> Buka Mode Studio Pelanggan
                            </a>
                            <span class="text-slate-300">•</span>
                            <span class="text-[11px] font-medium text-slate-400">Arahkan kursor pada foto untuk menghapus</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($booking->galeriFotos as $foto)
                            <div class="rounded-2xl overflow-hidden clean-card shadow-xs border border-slate-200 bg-white flex flex-col">
                                <div class="relative group h-40 bg-slate-100 overflow-hidden">
                                    <img src="{{ asset($foto->foto_path) }}" alt="Foto Hasil Pemotretan" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    
                                    @if($foto->is_selected)
                                        <div class="absolute top-2.5 left-2.5 bg-emerald-500/95 backdrop-blur-xs text-white px-2.5 py-1 rounded-lg text-[10px] font-black shadow-xs flex items-center gap-1">
                                            <i class="ph-fill ph-check-circle text-xs"></i> DIPILIH
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-linear-to-t from-slate-950/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-2.5">
                                        <button type="button" @click="openLightbox({{ $loop->index }})" class="w-7 h-7 rounded-lg bg-white/90 text-slate-900 hover:bg-white text-xs font-bold flex items-center justify-center shadow-md backdrop-blur-xs transition-colors" title="Buka Resolusi Penuh">
                                            <i class="ph-duotone ph-arrows-out text-sm"></i>
                                        </button>

                                        <form method="POST" action="{{ route('admin.proofing.delete', $foto->id) }}">
                                            @csrf
                                            <button type="submit" class="w-7 h-7 rounded-lg bg-rose-600 text-white hover:bg-rose-700 text-xs font-bold flex items-center justify-center shadow-md transition-colors" onclick="return confirm('Hapus foto ini dari galeri pelanggan?')" title="Hapus Foto">
                                                <i class="ph-duotone ph-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Card Footer: Catatan Khusus Pelanggan -->
                                <div class="p-2.5 border-t border-slate-100 flex-1 flex flex-col justify-between bg-slate-50/60">
                                    @if(!empty($foto->catatan))
                                        <div class="space-y-1">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-amber-700 flex items-center gap-1">
                                                <i class="ph-duotone ph-note-pencil text-xs"></i> Catatan Pelanggan:
                                            </span>
                                            <p class="text-[11px] font-bold text-slate-800 leading-snug line-clamp-2 bg-amber-50/80 border border-amber-200/80 rounded-lg p-1.5" title="{{ $foto->catatan }}">
                                                "{{ $foto->catatan }}"
                                            </p>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 text-[10px] font-medium text-slate-400 py-1">
                                            <i class="ph-duotone ph-minus-circle text-xs"></i>
                                            <span>Tanpa catatan</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Full-Screen Lightbox Multi-Photo Viewer untuk Owner -->
            <template x-teleport="body">
                <div x-show="showLightbox" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[99999] bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-6 lg:p-8"
                     style="display: none;">
                    
                    <!-- Top Lightbox Bar -->
                    <div class="flex items-center justify-between text-white border-b border-white/10 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full bg-white/10 text-slate-200 text-xs font-mono font-bold"
                                  x-text="`${activePhotoIndex + 1} / ${photos.length}`">
                            </span>
                            <div class="text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-white text-sm" x-text="activePhoto?.code"></span>
                                    <template x-if="activePhoto?.is_selected">
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1">
                                            <i class="ph-fill ph-check-circle text-xs"></i> Dipilih Pelanggan
                                        </span>
                                    </template>
                                </div>
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">{{ $booking->layanan?->nama ?? 'Sesi Foto' }} • Paket {{ $booking->paket?->nama_paket ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a :href="activePhoto?.foto_path" download class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-base" title="Unduh Resolusi Penuh">
                                <i class="ph-duotone ph-download-simple"></i>
                            </a>

                            <button @click="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-base" title="Tutup (Esc)">
                                <i class="ph-bold ph-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Center Image Display -->
                    <div class="relative flex-1 flex items-center justify-center my-4 overflow-hidden">
                        <button @click="prevPhoto()" class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Sebelumnya (Panah Kiri)">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>

                        <div class="max-w-5xl max-h-[72vh] flex items-center justify-center overflow-hidden rounded-2xl shadow-2xl border border-white/10">
                            <img :src="activePhoto?.foto_path" 
                                 :alt="activePhoto?.code" 
                                 class="max-w-full max-h-[72vh] object-contain rounded-2xl transition-all duration-300">
                        </div>

                        <button @click="nextPhoto()" class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-all active:scale-95 text-lg" title="Foto Selanjutnya (Panah Kanan)">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                    </div>

                    <!-- Bottom Notes Drawer (Catatan Pelanggan) -->
                    <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 text-white max-w-4xl mx-auto w-full">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <i class="ph-duotone ph-note-pencil text-amber-400 text-lg"></i>
                                <span class="text-xs font-black uppercase tracking-wider text-amber-300">Catatan Pelanggan:</span>
                                <span class="text-xs font-bold text-slate-100" x-text="activePhoto?.catatan ? `&quot;${activePhoto.catatan}&quot;` : 'Tidak ada catatan untuk foto ini'"></span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium">
                                Gunakan tombol <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white text-[10px]">←</kbd> <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white text-[10px]">→</kbd> untuk geser foto
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @else
        <!-- Course LMS Information Card -->
        <div class="clean-card p-8 rounded-3xl space-y-4 border border-slate-200 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="ph-duotone ph-graduation-cap"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Program Kursus Akademi Fotografi</h3>
                    <p class="text-xs text-slate-500">Reservasi ini merupakan akses modul pelatihan fotografi pelanggan melalui LMS.</p>
                </div>
            </div>
            <div class="pt-2 flex items-center gap-3">
                <a href="{{ route('admin.courses.reservations.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all inline-flex items-center gap-2">
                    <i class="ph-duotone ph-arrow-left"></i>
                    <span>Kembali ke Reservasi Kursus</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
