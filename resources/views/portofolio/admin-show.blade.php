@extends('layouts.admin')

@section('title', 'Kelola Album: ' . $album->judul . ' — RanggaMotret.id')

@section('content')
<div class="space-y-8"
     x-data="{
        openUploadModal: false,
        openDeleteModal: false,
        deleteUrl: '',
        deleteIndex: null,
        uploadFiles: [],
        fileCount: 0,
        handleFiles(event) {
            this.uploadFiles = Array.from(event.target.files);
            this.fileCount = this.uploadFiles.length;
        },
        confirmDelete(url, index) {
            this.deleteUrl = url;
            this.deleteIndex = index;
            this.openDeleteModal = true;
        }
     }">

    <!-- Page Header & Navigation Breadcrumb -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.portofolio.index') }}" class="hover:text-slate-700 transition-colors">Portofolio Galeri</a>
                <span>/</span>
                <span class="text-slate-700">Detail Isi Album</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.portofolio.index') }}" 
                   class="w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors"
                   title="Kembali ke Daftar Portofolio">
                    <i class="ph-duotone ph-arrow-left text-base"></i>
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $album->judul }}
                </h1>
            </div>
            <div class="flex items-center gap-3 mt-2 text-xs text-slate-500 font-medium">
                <span class="px-2.5 py-0.5 rounded-full bg-slate-900 text-white font-extrabold text-[10px] uppercase tracking-wider">
                    {{ $album->kategori }}
                </span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <i class="ph-duotone ph-calendar text-xs text-slate-400"></i>
                    {{ date('d F Y', strtotime($album->tanggal)) }}
                </span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <i class="ph-duotone ph-images text-xs text-indigo-500"></i>
                    <strong class="text-slate-900">{{ $album->fotos->count() }} Foto Galeri</strong>
                </span>
            </div>
        </div>

    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ph-duotone ph-check-circle text-lg text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <i class="ph ph-x"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ph-duotone ph-warning-circle text-lg text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                <i class="ph ph-x"></i>
            </button>
        </div>
    @endif

    <!-- Album Overview & Cover Banner -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cover Card -->
        <div class="clean-card rounded-2xl bg-white border border-slate-200 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Cover Utama Album</span>
                @if($album->status_publikasi === 'published')
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold flex items-center gap-1">
                        <i class="ph-fill ph-check-circle text-xs"></i> Published
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-extrabold flex items-center gap-1">
                        <i class="ph-fill ph-pause-circle text-xs"></i> Draft
                    </span>
                @endif
            </div>

            <div class="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 border border-slate-200 relative group">
                <img src="{{ $album->foto_url }}" alt="{{ $album->judul }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <a href="{{ $album->foto_url }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/95 text-slate-900 text-xs font-bold shadow-md flex items-center gap-1.5">
                        <i class="ph-duotone ph-arrows-out text-sm"></i>
                        <span>Lihat HD</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2 pt-2">
                <h3 class="text-sm font-extrabold text-slate-900">Deskripsi Album</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    {{ $album->deskripsi ?: 'Tidak ada deskripsi rinci untuk album karya foto ini.' }}
                </p>
            </div>
        </div>

        <!-- Quick Stats & Upload Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stats Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                        <i class="ph-duotone ph-images"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Foto Album</div>
                        <div class="text-lg font-black text-slate-900">{{ $album->fotos->count() }} Lembar</div>
                    </div>
                </div>

                <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="ph-duotone ph-eye"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Visibilitas</div>
                        <div class="text-lg font-black text-slate-900 capitalize">{{ $album->status_publikasi }}</div>
                    </div>
                </div>

                <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                        <i class="ph-duotone ph-sparkle"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Koleksi Tema</div>
                        <div class="text-lg font-black text-slate-900 truncate">{{ $album->kategori }}</div>
                    </div>
                </div>
            </div>

            <!-- Drag & Drop Multiple Upload Dropzone Widget -->
            <div class="clean-card p-6 rounded-2xl bg-white border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="ph-duotone ph-cloud-arrow-up text-base text-indigo-600"></i>
                            <span>Unggah Foto Tambahan Sekaligus</span>
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">
                            Pilih multiple file JPG, PNG, atau WEBP sekaligus untuk ditambahkan ke album ini
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.portofolio.upload-photos', $album->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="border-2 border-dashed border-slate-200 hover:border-slate-400 rounded-2xl p-6 text-center cursor-pointer transition-colors relative bg-slate-50/50">
                        <input type="file" name="photos[]" multiple accept="image/*" @change="handleFiles($event)" required
                               class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        <div class="space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 text-2xl flex items-center justify-center mx-auto">
                                <i class="ph-duotone ph-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-700">
                                <span x-show="fileCount === 0">Pilih beberapa foto atau seret ke area ini</span>
                                <span x-show="fileCount > 0" class="text-indigo-600 font-extrabold" x-text="`${fileCount} file foto terpilih siap diunggah`"></span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">Bisa memilih banyak foto sekaligus (Maksimal 5MB per file)</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3" x-show="fileCount > 0">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-md transition-all flex items-center gap-2">
                            <i class="ph-duotone ph-upload-simple text-sm text-emerald-400"></i>
                            <span>Mulai Unggah Semua Foto</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Gallery Grid of Photos in this Album -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <span>Daftar Foto dalam Album</span>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                    {{ $album->fotos->count() }} Foto
                </span>
            </h2>
        </div>

        @if($album->fotos->isEmpty())
            <div class="clean-card p-12 text-center rounded-3xl space-y-3 bg-white border border-slate-200">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 text-2xl flex items-center justify-center mx-auto">
                    <i class="ph-duotone ph-camera"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900">Belum Ada Foto Rinci di Album Ini</h4>
                <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto">
                    Album ini baru memiliki cover utama. Gunakan area unggah di atas untuk menambahkan foto-foto ke dalam galeri album.
                </p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($album->fotos as $foto)
                    <div class="clean-card rounded-2xl overflow-hidden bg-white border border-slate-200 group relative flex flex-col justify-between shadow-2xs hover:shadow-lg transition-all duration-300">
                        <!-- Frame Foto -->
                        <div class="aspect-square bg-slate-100 relative overflow-hidden">
                            <img src="{{ $foto->foto_url }}" alt="{{ $foto->caption ?? $album->judul }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Index Badge -->
                            <div class="absolute top-2 left-2 z-10">
                                <span class="px-2 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-md text-white text-[9px] font-mono font-bold">
                                    #{{ $loop->iteration }}
                                </span>
                            </div>

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <a href="{{ $foto->foto_url }}" target="_blank"
                                   class="w-8 h-8 rounded-lg bg-white/95 text-slate-900 flex items-center justify-center text-xs shadow-md hover:scale-110 transition-transform"
                                   title="Buka Foto HD">
                                    <i class="ph-duotone ph-arrows-out"></i>
                                </a>
                                <button type="button" 
                                        @click="confirmDelete('{{ route('admin.portofolio.destroy-photo', $foto->id) }}', {{ $loop->iteration }})"
                                        class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs shadow-md hover:scale-110 transition-transform"
                                        title="Hapus Foto">
                                    <i class="ph-duotone ph-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Caption & Urutan -->
                        <div class="p-2.5 text-xs text-slate-500 flex items-center justify-between border-t border-slate-100">
                            <span class="truncate text-[11px] font-medium text-slate-700">
                                {{ $foto->caption ?: 'Foto Galeri #' . $loop->iteration }}
                            </span>
                            <button type="button"
                                    @click="confirmDelete('{{ route('admin.portofolio.destroy-photo', $foto->id) }}', {{ $loop->iteration }})"
                                    class="text-rose-500 hover:text-rose-700 text-xs font-bold"
                                    title="Hapus Foto">
                                <i class="ph-duotone ph-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL: KONFIRMASI HAPUS FOTO TERTENTU      -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-show="openDeleteModal" 
             x-cloak
             class="modal-backdrop-fixed bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl border border-slate-200"
                 @click.away="openDeleteModal = false">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mx-auto border border-rose-100">
                    <i class="ph-duotone ph-trash"></i>
                </div>
                <div class="text-center space-y-1">
                    <h3 class="text-sm font-extrabold text-slate-900">Hapus Foto dari Album?</h3>
                    <p class="text-xs text-slate-500 font-medium">
                        Foto <span class="font-bold text-slate-700" x-text="`#${deleteIndex}`"></span> akan dihapus secara permanen dari album ini.
                    </p>
                </div>
                <form method="POST" :action="deleteUrl" class="flex items-center justify-center gap-3 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="w-1/2 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md transition-all">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
