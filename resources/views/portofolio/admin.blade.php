@extends('layouts.admin')

@section('title', 'Kelola Portofolio Karya — RanggaMotret.id')

@section('content')
<div class="space-y-8" 
     x-data="{
        openAddModal: false,
        openEditModal: false,
        openDeleteModal: false,
        deleteUrl: '',
        deleteTitle: '',
        previewAdd: null,
        previewEdit: null,
        addMode: 'file', // 'file' or 'url'
        editMode: 'file', // 'file' or 'url'

        // Form Tambah
        addForm: {
            judul: '',
            kategori: '{{ $allCategories->first() ?? 'Wedding' }}',
            customKategori: '',
            deskripsi: '',
            tanggal: '{{ date('Y-m-d') }}',
            status_publikasi: 'published',
            foto_url: ''
        },

        // Form Edit
        editForm: {
            id: null,
            actionUrl: '',
            judul: '',
            kategori: '',
            deskripsi: '',
            tanggal: '',
            status_publikasi: 'published',
            currentFoto: '',
            foto_url: ''
        },

        openEdit(item) {
            this.editForm = {
                id: item.id,
                actionUrl: '{{ url('admin/portofolio') }}/' + item.id,
                judul: item.judul,
                kategori: item.kategori,
                deskripsi: item.deskripsi || '',
                tanggal: item.tanggal,
                status_publikasi: item.status_publikasi,
                currentFoto: item.foto_url,
                foto_url: (item.foto && item.foto.startsWith('http')) ? item.foto : ''
            };
            this.previewEdit = item.foto_url;
            this.editMode = (item.foto && item.foto.startsWith('http')) ? 'url' : 'file';
            this.openEditModal = true;
        },

        confirmDelete(url, judul) {
            this.deleteUrl = url;
            this.deleteTitle = judul;
            this.openDeleteModal = true;
        },

        handleFileSelect(event, target) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (target === 'add') {
                        this.previewAdd = e.target.result;
                    } else {
                        this.previewEdit = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }
     }">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Portofolio Karya Foto</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 flex items-center gap-2.5">
                <span>Kelola Portofolio Galeri</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Koleksi foto hasil pemotretan studio yang ditampilkan di halaman beranda, katalog layanan, dan galeri publik.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="openAddModal = true" 
                    class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                <i class="ph-duotone ph-plus-circle text-base text-emerald-400"></i>
                <span>Tambah Portofolio Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
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

    <!-- 4 Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="clean-card p-5 rounded-2xl bg-white border border-slate-200 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Album</span>
                <div class="text-2xl font-black text-slate-900">{{ $totalAlbums ?? $portofolios->total() }}</div>
                <div class="text-[11px] text-slate-500 font-medium">Koleksi karya terdaftar</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-2xl">
                <i class="ph-duotone ph-folders"></i>
            </div>
        </div>

        <div class="clean-card p-5 rounded-2xl bg-white border border-slate-200 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Album Tayang</span>
                <div class="text-2xl font-black text-emerald-600">{{ $publishedAlbums ?? 0 }}</div>
                <div class="text-[11px] text-emerald-600 font-medium">Aktif di galeri publik</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
        </div>

        <div class="clean-card p-5 rounded-2xl bg-white border border-slate-200 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Draft / Arsip</span>
                <div class="text-2xl font-black text-amber-600">{{ $draftAlbums ?? 0 }}</div>
                <div class="text-[11px] text-amber-600 font-medium">Hanya terlihat owner</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl">
                <i class="ph-duotone ph-pause-circle"></i>
            </div>
        </div>

        <div class="clean-card p-5 rounded-2xl bg-white border border-slate-200 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Foto Koleksi</span>
                <div class="text-2xl font-black text-indigo-600">{{ $totalPhotos ?? 0 }}</div>
                <div class="text-[11px] text-indigo-600 font-medium">Foto di seluruh album</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                <i class="ph-duotone ph-images"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.portofolio.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Pencarian Judul/Deskripsi -->
            <div class="sm:col-span-5 relative">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul atau deskripsi foto..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all">
            </div>

            <!-- Filter Kategori -->
            <div class="sm:col-span-3">
                <select name="category" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent bg-white font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($allCategories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent bg-white font-medium">
                    <option value="">Semua Status</option>
                    <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published (Tampil)</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (Sembunyi)</option>
                    <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-all">
                    Filter
                </button>
                @if($search || $category || $status)
                    <a href="{{ route('admin.portofolio.index') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors" title="Reset Filter">
                        <i class="ph ph-arrow-counter-clockwise text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Portfolio Cards Grid -->
    @if($portofolios->isEmpty())
        <div class="clean-card p-16 text-center rounded-3xl space-y-4 max-w-lg mx-auto bg-white border border-slate-200">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 text-3xl flex items-center justify-center mx-auto">
                <i class="ph-duotone ph-images"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-900">Belum Ada Karya Portofolio</h3>
            <p class="text-xs text-slate-500 font-medium">
                @if($search || $category || $status)
                    Tidak ada hasil karya yang cocok dengan kata kunci atau filter yang Anda pilih.
                @else
                    Mulai perindah galeri website Anda dengan menambahkan foto-foto hasil pemotretan terbaik.
                @endif
            </p>
            <button @click="openAddModal = true" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-sm inline-flex items-center gap-2">
                <i class="ph-duotone ph-plus-circle text-emerald-400 text-base"></i>
                <span>Tambah Foto Pertama</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($portofolios as $item)
                <div class="clean-card rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Thumbnail Frame with Overlay Action -->
                        <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->judul }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Category Badge -->
                            <div class="absolute top-3 left-3 z-10">
                                <span class="px-2.5 py-1 rounded-full bg-slate-900/85 backdrop-blur-md text-white font-extrabold text-[10px] uppercase tracking-wider shadow-sm">
                                    {{ $item->kategori }}
                                </span>
                            </div>

                            <!-- Count of Photos in Album Badge -->
                            <div class="absolute bottom-3 left-3 z-10">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-900/85 backdrop-blur-md text-white font-black text-[10px] shadow-sm flex items-center gap-1.5 border border-white/10">
                                    <i class="ph-duotone ph-images text-indigo-400"></i>
                                    <span>{{ $item->fotos_count ?? $item->fotos->count() }} Foto</span>
                                </span>
                            </div>

                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3 z-10">
                                @if($item->status_publikasi === 'published')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500 text-white font-black text-[9px] uppercase tracking-wider shadow-sm flex items-center gap-1">
                                        <i class="ph-fill ph-check-circle text-xs"></i> Published
                                    </span>
                                @elseif($item->status_publikasi === 'draft')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white font-black text-[9px] uppercase tracking-wider shadow-sm flex items-center gap-1">
                                        <i class="ph-fill ph-pause-circle text-xs"></i> Draft
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-500 text-white font-black text-[9px] uppercase tracking-wider shadow-sm">
                                        Archived
                                    </span>
                                @endif
                            </div>

                        </div>

                        <!-- Card Content Information -->
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                                <span class="flex items-center gap-1">
                                    <i class="ph-duotone ph-calendar text-xs text-slate-500"></i>
                                    <span>{{ date('d M Y', strtotime($item->tanggal)) }}</span>
                                </span>
                                <span class="font-mono text-[10px]">ID: #{{ $item->id }}</span>
                            </div>

                            <h3 class="font-extrabold text-slate-900 text-sm line-clamp-1 group-hover:text-indigo-600 transition-colors" title="{{ $item->judul }}">
                                {{ $item->judul }}
                            </h3>

                            <p class="text-xs text-slate-500 font-medium line-clamp-2 leading-relaxed">
                                {{ $item->deskripsi ?: 'Tidak ada deskripsi tambahan untuk karya ini.' }}
                            </p>
                        </div>
                    </div>                    <!-- Bottom Quick Actions -->
                    <div class="p-4 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <!-- Manage Album Photos Button -->
                        <a href="{{ route('admin.portofolio.show', $item->id) }}"
                           class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-colors inline-flex items-center gap-1.5 border border-indigo-200/60"
                           title="Buka detail dan kelola foto-foto di dalam album">
                            <i class="ph-duotone ph-folder-open text-xs"></i>
                            <span>Kelola Foto Album</span>
                        </a>

                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    @click="openEdit({{ json_encode($item) }})"
                                    class="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors" title="Edit">
                                <i class="ph-duotone ph-pencil-simple text-sm"></i>
                            </button>
                            <button type="button" 
                                    @click="confirmDelete('{{ route('admin.portofolio.destroy', $item->id) }}', '{{ addslashes($item->judul) }}')"
                                    class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus">
                                <i class="ph-duotone ph-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $portofolios->links() }}
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 1: TAMBAH PORTOFOLIO BARU           -->
    <!-- ========================================== -->
    <div x-show="openAddModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 relative"
             @click.away="openAddModal = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-lg shadow-sm">
                        <i class="ph-duotone ph-images"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Tambah Album Portofolio Baru</h3>
                        <p class="text-xs text-slate-400 font-medium">Buat koleksi galeri foto baru dengan banyak foto sekaligus</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.portofolio.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Judul Foto / Project -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Album / Momen Foto <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" x-model="addForm.judul" required
                           placeholder="Contoh: Romantic Prewedding Ceremony at Bromo"
                           class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium">
                </div>

                <!-- Kategori & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Foto <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="addForm.kategori" required
                                class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white font-medium">
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                            <option value="Personal Portrait">Personal Portrait</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Family & Maternity">Family & Maternity</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Pemotretan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" x-model="addForm.tanggal" required
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium">
                    </div>
                </div>

                <!-- Mode Input Cover Gambar -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Foto Cover Utama <span class="text-slate-400 font-normal">(Opsional jika memilih multiple galeri)</span></label>
                        <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-[11px] font-bold">
                            <button type="button" @click="addMode = 'file'" 
                                    :class="addMode === 'file' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" 
                                    class="px-2.5 py-1 rounded-md transition-all">
                                Upload File
                            </button>
                            <button type="button" @click="addMode = 'url'" 
                                    :class="addMode === 'url' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" 
                                    class="px-2.5 py-1 rounded-md transition-all">
                                Link URL (Unsplash/CDN)
                            </button>
                        </div>
                    </div>

                    <!-- Input Mode File -->
                    <div x-show="addMode === 'file'" class="space-y-2">
                        <div class="border-2 border-dashed border-slate-200 hover:border-slate-400 rounded-2xl p-4 text-center cursor-pointer transition-colors relative bg-slate-50/50">
                            <input type="file" name="foto_file" accept="image/*" @change="handleFileSelect($event, 'add')"
                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                            <template x-if="!previewAdd">
                                <div class="space-y-1">
                                    <i class="ph-duotone ph-cloud-arrow-up text-2xl text-slate-400"></i>
                                    <div class="text-xs font-bold text-slate-700">Pilih berkas cover album atau seret ke sini</div>
                                    <p class="text-[10px] text-slate-400">Format JPG, PNG, WEBP (Maksimal 5MB)</p>
                                </div>
                            </template>
                            <template x-if="previewAdd">
                                <div class="relative w-full h-36 rounded-xl overflow-hidden">
                                    <img :src="previewAdd" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-xs font-bold">
                                        Klik untuk ganti cover
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Input Mode URL -->
                    <div x-show="addMode === 'url'" class="space-y-2">
                        <input type="url" name="foto_url" x-model="addForm.foto_url" 
                               placeholder="https://images.unsplash.com/photo-..."
                               class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-mono">
                    </div>
                </div>

                <!-- Multiple Photo Gallery Input (Opsi A) -->
                <div class="space-y-1.5 p-3.5 bg-indigo-50/60 rounded-2xl border border-indigo-100">
                    <label class="block text-xs font-bold text-indigo-950 flex items-center gap-1.5">
                        <i class="ph-duotone ph-images text-indigo-600 text-sm"></i>
                        <span>Unggah Foto Galeri Album Sekaligus</span>
                    </label>
                    <input type="file" name="gallery_files[]" multiple accept="image/*"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-indigo-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                    <p class="text-[10px] text-indigo-600 font-medium">Bisa memilih lebih dari 1 foto sekaligus untuk langsung dimasukkan ke dalam album ini.</p>
                </div>

                <!-- Deskripsi Karya -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Cerita / Detail Karya</label>
                    <textarea name="deskripsi" x-model="addForm.deskripsi" rows="3"
                              placeholder="Ceritakan konsep pemotretan, lokasi, pencahayaan, atau sentuhan unik pada karya ini..."
                              class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium"></textarea>
                </div>

                <!-- Status Publikasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Tampilan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all"
                               :class="addForm.status_publikasi === 'published' ? 'border-slate-900 bg-slate-50' : 'border-slate-200'">
                            <input type="radio" name="status_publikasi" value="published" x-model="addForm.status_publikasi" class="sr-only">
                            <i class="ph-duotone ph-globe text-base text-emerald-600"></i>
                            <div>
                                <div class="text-xs font-extrabold text-slate-900">Publikasikan Langsung</div>
                                <div class="text-[10px] text-slate-400">Tampil di galeri & katalog</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all"
                               :class="addForm.status_publikasi === 'draft' ? 'border-slate-900 bg-slate-50' : 'border-slate-200'">
                            <input type="radio" name="status_publikasi" value="draft" x-model="addForm.status_publikasi" class="sr-only">
                            <i class="ph-duotone ph-file-text text-base text-amber-600"></i>
                            <div>
                                <div class="text-xs font-extrabold text-slate-900">Simpan Sebagai Draft</div>
                                <div class="text-[10px] text-slate-400">Hanya terlihat oleh Owner</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Submit -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-1.5">
                        <i class="ph-duotone ph-check-circle text-emerald-400 text-sm"></i>
                        <span>Simpan & Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: UBAH / EDIT PORTOFOLIO           -->
    <!-- ========================================== -->
    <div x-show="openEditModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 relative"
             @click.away="openEditModal = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg shadow-sm border border-indigo-200">
                        <i class="ph-duotone ph-pencil-simple"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Ubah Data Portofolio</h3>
                        <p class="text-xs text-slate-400 font-medium">Perbarui judul, foto, kategori, atau status publikasi</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <form method="POST" :action="editForm.actionUrl" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Judul Foto -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Karya / Momen Foto <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" x-model="editForm.judul" required
                           class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium">
                </div>

                <!-- Kategori & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Foto <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="editForm.kategori" required
                                class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white font-medium">
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                            <option value="Personal Portrait">Personal Portrait</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Family & Maternity">Family & Maternity</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Pemotretan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" x-model="editForm.tanggal" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium">
                    </div>
                </div>

                <!-- Ganti Foto -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Ganti Foto (Biarkan kosong jika tidak diubah)</label>
                        <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-[11px] font-bold">
                            <button type="button" @click="editMode = 'file'" 
                                    :class="editMode === 'file' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" 
                                    class="px-2.5 py-1 rounded-md transition-all">
                                Upload File
                            </button>
                            <button type="button" @click="editMode = 'url'" 
                                    :class="editMode === 'url' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" 
                                    class="px-2.5 py-1 rounded-md transition-all">
                                Link URL
                            </button>
                        </div>
                    </div>

                    <!-- Mode File -->
                    <div x-show="editMode === 'file'" class="space-y-2">
                        <div class="border-2 border-dashed border-slate-200 hover:border-slate-400 rounded-2xl p-3 text-center cursor-pointer transition-colors relative bg-slate-50/50">
                            <input type="file" name="foto_file" accept="image/*" @change="handleFileSelect($event, 'edit')"
                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                            <div class="flex items-center gap-3 text-left">
                                <div class="w-20 h-16 rounded-xl overflow-hidden bg-slate-200 flex-shrink-0">
                                    <img :src="previewEdit || editForm.currentFoto" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Klik untuk mengganti berkas foto</div>
                                    <p class="text-[10px] text-slate-400">JPG, PNG, WEBP (Maksimal 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mode URL -->
                    <div x-show="editMode === 'url'" class="space-y-2">
                        <input type="url" name="foto_url" x-model="editForm.foto_url" 
                               placeholder="https://images.unsplash.com/photo-..."
                               class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-mono">
                    </div>
                </div>

                <!-- Deskripsi Karya -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Cerita / Detail Karya</label>
                    <textarea name="deskripsi" x-model="editForm.deskripsi" rows="3"
                              class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-medium"></textarea>
                </div>

                <!-- Status Publikasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Tampilan</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs font-bold"
                               :class="editForm.status_publikasi === 'published' ? 'border-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200 text-slate-600'">
                            <input type="radio" name="status_publikasi" value="published" x-model="editForm.status_publikasi" class="sr-only">
                            <i class="ph-fill ph-check-circle text-emerald-600"></i> Published
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs font-bold"
                               :class="editForm.status_publikasi === 'draft' ? 'border-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200 text-slate-600'">
                            <input type="radio" name="status_publikasi" value="draft" x-model="editForm.status_publikasi" class="sr-only">
                            <i class="ph-fill ph-pause-circle text-amber-600"></i> Draft
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs font-bold"
                               :class="editForm.status_publikasi === 'archived' ? 'border-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200 text-slate-600'">
                            <input type="radio" name="status_publikasi" value="archived" x-model="editForm.status_publikasi" class="sr-only">
                            <i class="ph-fill ph-archive text-slate-500"></i> Arsip
                        </label>
                    </div>
                </div>

                <!-- Action Submit -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-1.5">
                        <i class="ph-duotone ph-check-circle text-emerald-400 text-sm"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: KONFIRMASI HAPUS PORTOFOLIO      -->
    <!-- ========================================== -->
    <div x-show="openDeleteModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-slate-200"
             @click.away="openDeleteModal = false">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto border border-rose-100">
                <i class="ph-duotone ph-trash"></i>
            </div>
            <div class="text-center space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Hapus Karya Portofolio?</h3>
                <p class="text-xs text-slate-500 font-medium">
                    Apakah Anda yakin ingin menghapus karya <strong class="text-slate-800" x-text="deleteTitle"></strong>? Tindakan ini permanen dan foto akan dihapus dari etalase frontend.
                </p>
            </div>
            <form method="POST" :action="deleteUrl" class="flex items-center justify-center gap-3 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="openDeleteModal = false" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md transition-all">
                    Ya, Hapus Karya
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
