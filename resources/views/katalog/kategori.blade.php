@extends('layouts.admin')

@section('title', 'Kelola Kategori Layanan — RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8" 
     x-data="{
        openAddModal: false,
        openEditModal: false,
        openDeleteModal: false,
        deleteUrl: '',
        deleteTitle: '',
        deleteMessage: '',
        deleteWarning: '',
        canDelete: true,
        
        // Add Form Data
        addForm: {
            nama: '',
            slug: '',
            icon: 'fa-camera',
            deskripsi: '',
            status: 'aktif'
        },

        // Edit Form Data
        editForm: {
            id: null,
            nama: '',
            slug: '',
            icon: 'fa-camera',
            deskripsi: '',
            status: 'aktif'
        },

        // Auto slug helper
        generateSlug(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        },

        openEdit(category) {
            this.editForm = {
                id: category.id,
                nama: category.nama,
                slug: category.slug,
                icon: category.icon || 'fa-camera',
                deskripsi: category.deskripsi || '',
                status: category.status
            };
            this.openEditModal = true;
        },

        confirmDelete(url, nama, count) {
            this.deleteUrl = url;
            this.deleteTitle = 'Hapus Kategori ' + nama;
            if (count > 0) {
                this.canDelete = false;
                this.deleteMessage = 'Kategori ini tidak dapat dihapus karena masih digunakan oleh ' + count + ' layanan fotografi aktif/katalog.';
                this.deleteWarning = 'Silakan ubah kategori pada layanan-layanan terkait terlebih dahulu.';
            } else {
                this.canDelete = true;
                this.deleteMessage = 'Apakah Anda yakin ingin menghapus kategori ' + nama + '?';
                this.deleteWarning = 'Tindakan ini permanen dan kategori tidak akan muncul lagi di daftar pilihan.';
            }
            this.openDeleteModal = true;
        }
     }">

    <!-- Page Header & Action Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.layanan.index') }}" class="hover:text-slate-700 transition-colors">Layanan</a>
                <span>/</span>
                <span class="text-slate-700">Kategori Layanan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Kelola Kategori Layanan</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Kelola master segmentasi kategori fotografi, ikon visual, dan status ketersediaan.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="openAddModal = true" 
                    class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Kategori Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter, Search, & Toolbar -->
    <div class="clean-card p-4 rounded-2xl">
        <form method="GET" action="{{ route('admin.kategori-layanan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <!-- Search Query -->
            <div class="sm:col-span-7 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kategori, slug, deskripsi..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                    <option value="all">Semua Status ({{ $totalKategori }})</option>
                    <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif ({{ $kategoriAktif }})</option>
                    <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Nonaktif ({{ $totalKategori - $kategoriAktif }})</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all text-center">
                    Filter
                </button>
                @if($search || ($status && $status !== 'all'))
                    <a href="{{ route('admin.kategori-layanan.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Category Cards Grid -->
    @if($categories->isEmpty())
        <div class="clean-card p-12 rounded-3xl text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Tidak ada kategori ditemukan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Coba sesuaikan kata kunci pencarian atau buat kategori fotografi baru untuk studio Anda.</p>
            </div>
            <button @click="openAddModal = true" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kategori Baru
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($categories as $category)
                <div class="clean-card p-6 rounded-3xl border border-slate-200 hover:border-slate-300 transition-all duration-200 flex flex-col justify-between space-y-5 group">
                    
                    <!-- Card Top Header -->
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <!-- Icon Badge -->
                            <div class="w-12 h-12 rounded-2xl {{ $category->status === 'aktif' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fa-solid {{ $category->icon ?: 'fa-camera' }}"></i>
                            </div>

                            <!-- Status Pill with Toggle Form -->
                            <form method="POST" action="{{ route('admin.kategori-layanan.toggle-status', $category->id) }}">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5 transition-all {{ $category->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}"
                                        title="Klik untuk mengubah status kategori">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $category->status === 'aktif' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $category->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                        </div>

                        <!-- Category Name & Slug -->
                        <div>
                            <h3 class="font-extrabold text-lg text-slate-900 tracking-tight">{{ $category->nama }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">
                                    /{{ $category->slug }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-slate-600 leading-relaxed min-h-9">
                            {{ $category->deskripsi ?: 'Tidak ada keterangan tambahan untuk kategori ini.' }}
                        </p>
                    </div>

                    <!-- Card Bottom Info & Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <!-- Linked Services Counter -->
                        <a href="{{ route('admin.layanan.index', ['kategori' => $category->nama]) }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition-colors"
                           title="Lihat layanan dalam kategori ini">
                            <i class="fa-solid fa-camera text-slate-500 text-[11px]"></i>
                            <span>{{ $category->layanan_count }} Layanan</span>
                        </a>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1.5">
                            <!-- Edit Button -->
                            <button @click="openEdit({{ json_encode($category) }})" 
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all text-xs"
                                    title="Ubah Kategori">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <!-- Delete Button -->
                            <button @click="confirmDelete('{{ route('admin.kategori-layanan.destroy', $category->id) }}', '{{ addslashes($category->nama) }}', {{ $category->layanan_count }})" 
                                    class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition-all text-xs"
                                    title="Hapus Kategori">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 1: TAMBAH KATEGORI BARU                                  -->
    <!-- ============================================================== -->
    <div x-show="openAddModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4 overflow-y-auto" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden my-8" 
             @click.away="openAddModal = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Tambah Kategori Layanan Baru</h3>
                        <p class="text-[11px] text-slate-500">Buat master kelompok jenis fotografi baru.</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" action="{{ route('admin.kategori-layanan.store') }}" class="p-6 space-y-4 text-xs">
                @csrf

                <!-- Nama Kategori -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" x-model="addForm.nama" @input="addForm.slug = generateSlug(addForm.nama)" required placeholder="Contoh: Maternity & Newborn"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                </div>

                <!-- Slug URL -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Slug URL (Otomatis)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-[11px]">/</span>
                        <input type="text" name="slug" x-model="addForm.slug" placeholder="maternity-newborn"
                               class="w-full pl-7 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono text-[11px] focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Pilihan Ikon FontAwesome -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Pilih Ikon Visual</label>
                    <div class="grid grid-cols-4 gap-2 mb-2">
                        @foreach($iconPresets as $iconClass => $iconLabel)
                            <button type="button" @click="addForm.icon = '{{ $iconClass }}'"
                                    :class="addForm.icon === '{{ $iconClass }}' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="p-2 rounded-xl border flex flex-col items-center justify-center gap-1 transition-all text-center">
                                <i class="fa-solid {{ $iconClass }} text-sm"></i>
                                <span class="text-[9px] font-semibold truncate w-full">{{ explode(' / ', $iconLabel)[0] }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500 shrink-0">Ikon Terpilih:</span>
                        <input type="text" name="icon" x-model="addForm.icon" placeholder="fa-camera"
                               class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono text-xs focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                </div>

                <!-- Deskripsi Kategori -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" x-model="addForm.deskripsi" rows="2" placeholder="Jelaskan secara singkat cakupan fotografi kategori ini..."
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Status Kategori -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Status Kategori</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="aktif" x-model="addForm.status" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Aktif</div>
                                <div class="text-[10px] text-slate-500">Tersedia di form layanan</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="nonaktif" x-model="addForm.status" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Nonaktif</div>
                                <div class="text-[10px] text-slate-500">Disembunyikan</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button Group -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="openAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition-all">
                        Simpan Kategori
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: EDIT KATEGORI                                         -->
    <!-- ============================================================== -->
    <div x-show="openEditModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4 overflow-y-auto" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden my-8" 
             @click.away="openEditModal = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Ubah Data Kategori Layanan</h3>
                        <p class="text-[11px] text-slate-500">Perbarui nama, ikon, deskripsi, atau status kategori.</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" :action="'/admin/kategori-layanan/' + editForm.id" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- Nama Kategori -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" x-model="editForm.nama" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                </div>

                <!-- Slug URL -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Slug URL</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-[11px]">/</span>
                        <input type="text" name="slug" x-model="editForm.slug"
                               class="w-full pl-7 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono text-[11px] focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Pilihan Ikon FontAwesome -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Pilih Ikon Visual</label>
                    <div class="grid grid-cols-4 gap-2 mb-2">
                        @foreach($iconPresets as $iconClass => $iconLabel)
                            <button type="button" @click="editForm.icon = '{{ $iconClass }}'"
                                    :class="editForm.icon === '{{ $iconClass }}' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="p-2 rounded-xl border flex flex-col items-center justify-center gap-1 transition-all text-center">
                                <i class="fa-solid {{ $iconClass }} text-sm"></i>
                                <span class="text-[9px] font-semibold truncate w-full">{{ explode(' / ', $iconLabel)[0] }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500 shrink-0">Ikon Terpilih:</span>
                        <input type="text" name="icon" x-model="editForm.icon" placeholder="fa-camera"
                               class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono text-xs focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                </div>

                <!-- Deskripsi Kategori -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" x-model="editForm.deskripsi" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Status Kategori -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Status Kategori</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="aktif" x-model="editForm.status" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Aktif</div>
                                <div class="text-[10px] text-slate-500">Tersedia di form layanan</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="nonaktif" x-model="editForm.status" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Nonaktif</div>
                                <div class="text-[10px] text-slate-500">Disembunyikan</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button Group -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="openEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition-all">
                        Perbarui Kategori
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 3: KONFIRMASI HAPUS                                      -->
    <!-- ============================================================== -->
    <div x-show="openDeleteModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-md w-full border border-slate-200 shadow-2xl p-6 space-y-5 text-xs" 
             @click.away="openDeleteModal = false">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900" x-text="deleteTitle"></h3>
                    <p class="text-slate-500 mt-0.5" x-text="deleteMessage"></p>
                </div>
            </div>

            <div class="p-3 rounded-2xl text-[11px] leading-relaxed"
                 :class="canDelete ? 'bg-amber-50 border border-amber-200 text-amber-900' : 'bg-rose-50 border border-rose-200 text-rose-900'">
                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                <span x-text="deleteWarning"></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="openDeleteModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                    <span x-text="canDelete ? 'Batal' : 'Tutup'"></span>
                </button>
                <template x-if="canDelete">
                    <form method="POST" :action="deleteUrl">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition-all">
                            Ya, Hapus Sekarang
                        </button>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
