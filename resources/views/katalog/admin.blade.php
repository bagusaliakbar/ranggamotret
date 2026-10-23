@extends('layouts.admin')

@section('title', 'Kelola Layanan & Paket — RanggaMotret.id')

@section('content')
<div class="space-y-8" 
     x-data="{
        openAddLayanan: false,
        openEditLayanan: false,
        openAddPaket: false,
        openEditPaket: false,
        openDeleteModal: false,
        deleteUrl: '',
        deleteTitle: '',
        deleteMessage: '',
        editLayanan: {
            id: null,
            nama: '',
            kategori: '',
            harga_mulai: 0,
            thumbnail: '',
            status: 'aktif',
            deskripsi: ''
        },
        addPaketLayananId: null,
        addPaketLayananNama: '',
        editPaket: {
            id: null,
            nama_paket: '',
            harga: 0,
            durasi: '',
            jumlah_fotografer: 1,
            jumlah_foto: 50,
            fasilitas: '',
            deskripsi: '',
            status: 'aktif'
        },
        // Upload/URL tab switchers
        addImgTab: 'url',
        editImgTab: 'current',
        addKategoriChoice: 'Wedding',
        editKategoriChoice: '',
        
        previewAddImg(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('add-img-preview').src = e.target.result;
                    document.getElementById('add-img-preview-container').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        },
        previewEditImg(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('edit-img-preview').src = e.target.result;
                    document.getElementById('edit-img-preview-container').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        },
        openEditLayananModal(item) {
            this.editLayanan = {
                id: item.id,
                nama: item.nama,
                kategori: item.kategori,
                harga_mulai: Math.round(Number(item.harga_mulai) || 0),
                thumbnail: item.thumbnail || '',
                status: item.status || 'aktif',
                deskripsi: item.deskripsi || ''
            };
            this.editKategoriChoice = item.kategori;
            this.editImgTab = 'current';
            this.openEditLayanan = true;
        },
        openAddPaketModal(layananId, layananNama) {
            this.addPaketLayananId = layananId;
            this.addPaketLayananNama = layananNama;
            this.openAddPaket = true;
        },
        openEditPaketModal(p) {
            this.editPaket = {
                id: p.id,
                nama_paket: p.nama_paket,
                harga: Math.round(Number(p.harga) || 0),
                durasi: p.durasi || '',
                jumlah_fotografer: p.jumlah_fotografer || 1,
                jumlah_foto: p.jumlah_foto || 50,
                fasilitas: p.fasilitas || '',
                deskripsi: p.deskripsi || '',
                status: p.status || 'aktif'
            };
            this.openEditPaket = true;
        },
        confirmDelete(url, title, message) {
            this.deleteUrl = url;
            this.deleteTitle = title;
            this.deleteMessage = message;
            this.openDeleteModal = true;
        }
     }">

    <!-- Page Header & Action Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Layanan & Paket</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kelola Katalog Layanan Fotografi</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Atur portofolio layanan, varian paket harga, durasi sesi, dan ketersediaan status publikasi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kategori-layanan.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-2xs">
                <i class="ph-duotone ph-squares-four text-base text-slate-600"></i>
                <span>Kategori Layanan</span>
            </a>
            <button @click="openAddLayanan = true" 
                    class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                <i class="ph-duotone ph-plus-circle text-base text-emerald-400"></i>
                <span>Tambah Layanan Baru</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards (Ringkasan Metrik Layanan & Paket) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Layanan -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Layanan</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-camera"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalLayanan) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Katalog tema fotografi</div>
        </div>

        <!-- 2. Layanan Aktif -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-emerald-700 uppercase tracking-wider">Layanan Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($layananAktif) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Bisa dibooking oleh klien</div>
        </div>

        <!-- 3. Total Paket -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Paket</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-package"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-indigo-600">{{ number_format($totalPaket) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Opsi variasi harga & durasi</div>
        </div>

        <!-- 4. Paket Aktif -->
        <div class="clean-card p-5 rounded-2xl space-y-1 bg-white border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Paket Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-tag"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600">{{ number_format($paketAktif) }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Tersedia untuk reservasi</div>
        </div>
    </div>

    <!-- Filter, Search, & Toolbar -->
    <div class="clean-card p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.layanan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <!-- Search Query -->
            <div class="sm:col-span-5 relative">
                <i class="ph-duotone ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama layanan, kategori, deskripsi..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
            </div>

            <!-- Filter Kategori -->
            <div class="sm:col-span-3">
                <select name="kategori" onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $kategori === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all font-medium">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-black text-white py-2.5 rounded-xl font-bold transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                    <i class="ph-duotone ph-funnel text-sm"></i> Filter
                </button>
                @if($search || ($kategori && $kategori !== 'all') || ($status && $status !== 'all'))
                    <a href="{{ route('admin.layanan.index') }}" title="Reset Filter" class="p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold transition-all flex items-center justify-center">
                        <i class="ph-duotone ph-arrows-counter-clockwise text-base"></i>
                    </a>
            </div>
        </form>
    </div>

    <!-- Services Grid List -->
    @if($layananList->isEmpty())
        <div class="clean-card p-12 rounded-3xl text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                <i class="fa-solid fa-camera"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-extrabold text-base text-slate-900">Belum Ada Layanan Fotografi</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    @if($search || $kategori !== 'all' || $status !== 'all')
                        Tidak ditemukan layanan yang sesuai dengan kriteria filter pencarian.
                    @else
                        Mulai bangun katalog studio Anda dengan menambahkan layanan fotografi baru.
                    @endif
                </p>
            </div>
            @if($search || $kategori !== 'all' || $status !== 'all')
                <a href="{{ route('admin.layanan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-all">
                    <i class="fa-solid fa-xmark"></i> Bersihkan Filter
                </a>
            @else
                <button @click="openAddLayanan = true" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 px-5 py-2.5 rounded-xl shadow-md transition-all">
                    <i class="fa-solid fa-plus"></i> Tambah Layanan Sekarang
                </button>
            @endif
        </div>
    @else
        <div class="space-y-6">
            @foreach($layananList as $item)
                <div class="clean-card rounded-3xl overflow-hidden transition-all hover:shadow-md border border-slate-200" 
                     x-data="{ showPakets: true }">
                    
                    <!-- Service Main Row Card -->
                    <div class="p-6 bg-white space-y-6">
                        <div class="flex flex-col lg:flex-row gap-6 items-start justify-between">
                            
                            <!-- Left: Thumbnail & Core Details -->
                            <div class="flex flex-col sm:flex-row gap-5 flex-1 min-w-0">
                                <!-- Thumbnail with category badge overlay -->
                                <div class="relative w-full sm:w-44 h-40 sm:h-32 rounded-2xl overflow-hidden shrink-0 border border-slate-100 bg-slate-100 shadow-inner group">
                                    <img src="{{ $item->thumbnail }}" 
                                         alt="{{ $item->nama }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80'">
                                    
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white text-[9px] font-extrabold uppercase tracking-wider">
                                            {{ $item->kategori }}
                                        </span>
                                    </div>

                                    <div class="absolute bottom-2 right-2">
                                        @if($item->status === 'aktif')
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/90 text-white text-[9px] font-bold shadow-sm flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Aktif
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md bg-slate-600/90 text-white text-[9px] font-bold shadow-sm">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Text Details -->
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <h3 class="text-xl font-extrabold text-slate-900 leading-snug">{{ $item->nama }}</h3>
                                    </div>

                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                                        {{ $item->deskripsi ?: 'Tidak ada deskripsi layanan yang dicantumkan.' }}
                                    </p>

                                    <div class="flex items-center gap-4 text-xs pt-1 flex-wrap">
                                        <div class="bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                                            <span class="text-slate-500 font-medium text-[11px]">Mulai Dari: </span>
                                            <span class="font-extrabold text-slate-900 text-sm">Rp {{ number_format($item->harga_mulai, 0, ',', '.') }}</span>
                                        </div>

                                        <div class="bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 flex items-center gap-1.5 text-[11px] font-semibold">
                                            <i class="fa-solid fa-calendar-check text-slate-400"></i>
                                            <span>{{ $item->bookings_count }} Riwayat Reservasi</span>
                                        </div>

                                        <div class="bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 flex items-center gap-1.5 text-[11px] font-semibold">
                                            <i class="fa-solid fa-box text-slate-400"></i>
                                            <span>{{ $item->pakets->count() }} Paket Sesi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Action Buttons Group -->
                            <div class="flex items-center gap-2 w-full lg:w-auto justify-end border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                                <!-- Edit Layanan Button -->
                                <button @click="openEditLayananModal({{ json_encode($item) }})" 
                                        class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all"
                                        title="Ubah Data Layanan">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <!-- Toggle Status Button -->
                                <form method="POST" action="{{ route('admin.layanan.toggle-status', $item->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2.5 rounded-xl {{ $item->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }} text-xs font-bold transition-all"
                                            title="{{ $item->status === 'aktif' ? 'Nonaktifkan Layanan' : 'Aktifkan Layanan' }}">
                                        <i class="fa-solid {{ $item->status === 'aktif' ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off' }} text-sm"></i>
                                    </button>
                                </form>

                                <!-- View Public Service Link -->
                                <a href="{{ route('public.services.detail', $item->id) }}" target="_blank"
                                   class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all"
                                   title="Lihat Pratinjau Publik">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>

                                <!-- Delete Service Button -->
                                <button @click="confirmDelete('{{ route('admin.layanan.destroy', $item->id) }}', 'Hapus Layanan {{ addslashes($item->nama) }}', 'Apakah Anda yakin ingin menghapus layanan ini? Semua paket sesi di bawahnya juga akan ikut terhapus.')" 
                                        class="p-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-all"
                                        title="Hapus Layanan">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                        </div>
                    </div>

                    <!-- Packages Section Accordion Bar -->
                    <div class="bg-slate-50/80 border-t border-slate-100 px-6 py-4">
                        <div class="flex items-center justify-between mb-3">
                            <button @click="showPakets = !showPakets" class="flex items-center gap-2 text-xs font-extrabold text-slate-800 uppercase tracking-wider hover:text-slate-900">
                                <i class="fa-solid fa-box-open text-slate-600"></i>
                                <span>Paket Sesi Fotografi ({{ $item->pakets->count() }})</span>
                                <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200" 
                                   :class="showPakets ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>

                            <button @click="openAddPaketModal({{ $item->id }}, '{{ addslashes($item->nama) }}')" 
                                    class="text-[11px] font-bold text-slate-700 hover:text-slate-900 transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i> Tambah Varian Paket
                            </button>
                        </div>

                        <!-- Packages Grid -->
                        <div x-show="showPakets" x-collapse>
                            @if($item->pakets->isEmpty())
                                <div class="bg-white rounded-2xl p-6 text-center border border-dashed border-slate-300 space-y-2">
                                    <p class="text-xs text-slate-500 font-medium">Layanan ini belum memiliki varian paket sesi.</p>
                                    <button @click="openAddPaketModal({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-all">
                                        <i class="fa-solid fa-plus"></i> Tambah Varian Paket
                                    </button>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-1">
                                    @foreach($item->pakets as $p)
                                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3 flex flex-col justify-between hover:border-slate-300 transition-all text-xs">
                                            <!-- Package Header -->
                                            <div>
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <h4 class="font-extrabold text-slate-900 text-sm leading-tight">{{ $p->nama_paket }}</h4>
                                                        <div class="text-base font-black text-slate-900 mt-1">
                                                            Rp {{ number_format($p->harga, 0, ',', '.') }}
                                                        </div>
                                                    </div>

                                                    @if($p->status === 'aktif')
                                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[9px]">
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200 font-bold text-[9px]">
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Specifications Badges -->
                                                <div class="flex flex-wrap gap-1.5 mt-3 text-[10px] font-semibold text-slate-600">
                                                    @if($p->durasi)
                                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 flex items-center gap-1">
                                                            <i class="fa-regular fa-clock text-slate-400"></i> {{ $p->durasi }}
                                                        </span>
                                                    @endif
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 flex items-center gap-1">
                                                        <i class="fa-solid fa-user-tie text-slate-400"></i> {{ $p->jumlah_fotografer }} Fotografer
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 flex items-center gap-1">
                                                        <i class="fa-solid fa-images text-slate-400"></i> {{ $p->jumlah_foto }} Foto
                                                    </span>
                                                </div>

                                                <!-- Facilities / Deliverables -->
                                                @if($p->fasilitas)
                                                    <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600 leading-relaxed">
                                                        <div class="font-bold text-[10px] uppercase text-slate-400 mb-1">Fasilitas & Output:</div>
                                                        {{ $p->fasilitas }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Package Actions Footer -->
                                            <div class="border-t border-slate-100 pt-3 flex items-center justify-between text-[11px]">
                                                <div class="flex items-center gap-1">
                                                    <!-- Toggle Status Paket -->
                                                    <form method="POST" action="{{ route('admin.layanan.paket.toggle-status', $p->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="px-2 py-1 rounded-lg {{ $p->status === 'aktif' ? 'text-emerald-700 hover:bg-emerald-50' : 'text-slate-500 hover:bg-slate-100' }} font-bold text-[10px] transition-colors"
                                                                title="Ubah status paket">
                                                            <i class="fa-solid {{ $p->status === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                                                            {{ $p->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="flex items-center gap-1.5">
                                                    <!-- Edit Paket -->
                                                    <button @click="openEditPaketModal({{ json_encode($p) }})" 
                                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all"
                                                            title="Edit Paket">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>

                                                    <!-- Delete Paket -->
                                                    <button @click="confirmDelete('{{ route('admin.layanan.paket.destroy', $p->id) }}', 'Hapus Paket {{ addslashes($p->nama_paket) }}', 'Apakah Anda yakin ingin menghapus varian paket ini?')" 
                                                            class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition-all"
                                                            title="Hapus Paket">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 1: TAMBAH LAYANAN BARU                                   -->
    <!-- ============================================================== -->
    <div x-show="openAddLayanan" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4 overflow-y-auto" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8" 
             @click.away="openAddLayanan = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Tambah Layanan Fotografi Baru</h3>
                        <p class="text-[11px] text-slate-500">Isi formulir untuk menambahkan katalog layanan fotografi baru.</p>
                    </div>
                </div>
                <button @click="openAddLayanan = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" action="{{ route('admin.layanan.store') }}" enctype="multipart/form-data" class="p-6 space-y-5 text-xs">
                @csrf

                <!-- Nama Layanan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Nama Layanan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Wedding Cinema Photography"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                </div>

                <!-- Kategori & Harga Mulai Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori -->
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Kategori Jasa <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="addKategoriChoice" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                            @foreach($categories as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                            <option value="custom">+ Kategori Lainnya (Custom)...</option>
                        </select>

                        <!-- Custom Kategori Input -->
                        <div x-show="addKategoriChoice === 'custom'" class="mt-2">
                            <input type="text" name="kategori_custom" placeholder="Ketik nama kategori baru..."
                                   class="w-full px-4 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Harga Mulai -->
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Harga Mulai Dari (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_mulai" min="0" required placeholder="Contoh: 2500000"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Status Layanan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="aktif" checked class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Aktif (Published)</div>
                                <div class="text-[10px] text-slate-500">Tampil di katalog publik</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="nonaktif" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Nonaktif (Draft)</div>
                                <div class="text-[10px] text-slate-500">Disembunyikan sementara</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Thumbnail Image Source -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-slate-700 font-extrabold">Thumbnail Foto Layanan</label>
                        <div class="flex rounded-lg bg-slate-100 p-0.5 text-[10px] font-bold">
                            <button type="button" @click="addImgTab = 'url'" 
                                    :class="addImgTab === 'url' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" 
                                    class="px-2.5 py-1 rounded-md transition-all">URL Gambar</button>
                            <button type="button" @click="addImgTab = 'upload'" 
                                    :class="addImgTab === 'upload' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" 
                                    class="px-2.5 py-1 rounded-md transition-all">Upload File</button>
                        </div>
                    </div>                    <!-- Option 1: URL -->
                    <div x-show="addImgTab === 'url'">
                        <input type="text" name="thumbnail_url" placeholder="https://images.unsplash.com/photo-..."
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                        <span class="text-[10px] text-slate-400 mt-1 block">Tempel link gambar langsung dari Unsplash / CDN atau path lokal.</span>
                    </div>

                    <!-- Option 2: Upload File -->
                    <div x-show="addImgTab === 'upload'">
                        <div class="flex items-center gap-4">
                            <input type="file" name="thumbnail_file" accept="image/*" @change="previewAddImg($event)"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                        </div>
                        <div id="add-img-preview-container" class="hidden mt-2">
                            <img id="add-img-preview" src="" class="w-24 h-24 object-cover rounded-xl border border-slate-200 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Layanan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Deskripsi Lengkap Layanan</label>
                    <textarea name="deskripsi" rows="3" placeholder="Jelaskan keunggulan, konsep pemotretan, dan nilai tambah layanan ini..."
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Submit Button Group -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="openAddLayanan = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition-all">
                        Simpan Layanan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: EDIT LAYANAN                                          -->
    <!-- ============================================================== -->
    <div x-show="openEditLayanan" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4 overflow-y-auto" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8" 
             @click.away="openEditLayanan = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Ubah Data Layanan Fotografi</h3>
                        <p class="text-[11px] text-slate-500">Perbarui rincian, kategori, harga awal, atau gambar thumbnail.</p>
                    </div>
                </div>
                <button @click="openEditLayanan = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" :action="'{{ url('admin/layanan') }}/' + editLayanan.id" enctype="multipart/form-data" class="p-6 space-y-5 text-xs">
                @csrf
                @method('PUT')

                <!-- Nama Layanan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Nama Layanan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" x-model="editLayanan.nama" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                </div>

                <!-- Kategori & Harga Mulai Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori -->
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Kategori Jasa <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="editKategoriChoice" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                            @foreach($categories as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                            <option value="custom">+ Kategori Lainnya (Custom)...</option>
                        </select>

                        <!-- Custom Kategori Input -->
                        <div x-show="editKategoriChoice === 'custom'" class="mt-2">
                            <input type="text" name="kategori_custom" placeholder="Ketik nama kategori baru..."
                                   class="w-full px-4 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Harga Mulai -->
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Harga Mulai Dari (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_mulai" x-model="editLayanan.harga_mulai" min="0" step="any" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Status Layanan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="aktif" x-model="editLayanan.status" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Aktif (Published)</div>
                                <div class="text-[10px] text-slate-500">Tampil di website</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-white transition-colors has-checked:border-slate-900 has-checked:bg-slate-900/5">
                            <input type="radio" name="status" value="nonaktif" x-model="editLayanan.status" class="text-slate-900 focus:ring-slate-900">
                            <div>
                                <div class="font-bold text-slate-900">Nonaktif (Draft)</div>
                                <div class="text-[10px] text-slate-500">Disembunyikan</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Thumbnail Image Source -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-slate-700 font-extrabold">Thumbnail Foto Layanan</label>
                        <div class="flex rounded-lg bg-slate-100 p-0.5 text-[10px] font-bold">
                            <button type="button" @click="editImgTab = 'current'" 
                                     :class="editImgTab === 'current' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" 
                                     class="px-2.5 py-1 rounded-md transition-all">Saat Ini</button>
                            <button type="button" @click="editImgTab = 'url'" 
                                     :class="editImgTab === 'url' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" 
                                     class="px-2.5 py-1 rounded-md transition-all">Ubah via URL</button>
                            <button type="button" @click="editImgTab = 'upload'" 
                                     :class="editImgTab === 'upload' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" 
                                     class="px-2.5 py-1 rounded-md transition-all">Upload Baru</button>
                        </div>
                    </div>

                    <!-- Current Preview -->
                    <div x-show="editImgTab === 'current'" class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                        <img :src="editLayanan.thumbnail" class="w-20 h-20 rounded-xl object-cover border border-slate-200 shadow-sm">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Gambar Terpasang:</span>
                            <div class="text-[11px] text-slate-700 font-medium truncate max-w-sm" x-text="editLayanan.thumbnail"></div>
                        </div>
                    </div>

                    <!-- Option URL -->
                    <div x-show="editImgTab === 'url'">
                        <input type="text" name="thumbnail_url" x-model="editLayanan.thumbnail" placeholder="https://images.unsplash.com/photo-..."
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>

                    <!-- Option Upload File -->
                    <div x-show="editImgTab === 'upload'">
                        <input type="file" name="thumbnail_file" accept="image/*" @change="previewEditImg($event)"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                        <div id="edit-img-preview-container" class="hidden mt-2">
                            <img id="edit-img-preview" src="" class="w-24 h-24 object-cover rounded-xl border border-slate-200 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Layanan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Deskripsi Lengkap Layanan</label>
                    <textarea name="deskripsi" x-model="editLayanan.deskripsi" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Submit Button Group -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="openEditLayanan = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition-all">
                        Perbarui Layanan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 3: TAMBAH PAKET LAYANAN                                  -->
    <!-- ============================================================== -->
    <div x-show="openAddPaket" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4 overflow-y-auto" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8" 
             @click.away="openAddPaket = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Tambah Varian Paket Sesi</h3>
                        <p class="text-[11px] text-slate-500">Untuk Layanan: <span class="font-bold text-slate-900" x-text="addPaketLayananNama"></span></p>
                    </div>
                </div>
                <button @click="openAddPaket = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" :action="'{{ url('admin/layanan') }}/' + addPaketLayananId + '/paket'" class="p-6 space-y-4 text-xs">
                @csrf

                <!-- Nama Paket -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Nama Paket <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_paket" required placeholder="Contoh: Paket Gold Full Day / Silver Half Day"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                </div>

                <!-- Harga & Durasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Harga Paket (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga" min="0" step="any" required placeholder="Contoh: 3500000"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Durasi Sesi</label>
                        <input type="text" name="durasi" placeholder="Contoh: 4 Jam / Full Day 8 Jam"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Jumlah Fotografer & Jumlah Foto -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Jumlah Fotografer <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_fotografer" value="1" min="1" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Estimasi Foto Diterima <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_foto" value="50" min="1" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Fasilitas & Output -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Fasilitas & Output Hasil Foto</label>
                    <textarea name="fasilitas" rows="3" placeholder="Contoh: Semua file foto original RAW di Flashdisk 32GB, 30 foto edit profesional, 1 cetak frame 16R, album kolase 20 halaman..."
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Deskripsi Paket Tambahan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Deskripsi Tambahan (Opsional)</label>
                    <textarea name="deskripsi" rows="2" placeholder="Catatan syarat, ketentuan lokasi outdoor/indoor, dll..."
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Status Paket -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Status Paket</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                        <option value="aktif">Aktif (Tersedia untuk Reservasi)</option>
                        <option value="nonaktif">Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <!-- Submit Button Group -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="openAddPaket = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition-all">
                        Simpan Paket
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 4: EDIT PAKET LAYANAN                                    -->
    <!-- ============================================================== -->
    <div x-show="openEditPaket" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4 overflow-y-auto" 
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8" 
             @click.away="openEditPaket = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Ubah Data Paket Sesi</h3>
                        <p class="text-[11px] text-slate-500">Perbarui harga, durasi, fasilitas, dan detail paket.</p>
                    </div>
                </div>
                <button @click="openEditPaket = false" class="text-slate-400 hover:text-slate-900 transition-colors p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" :action="'{{ url('admin/paket') }}/' + editPaket.id" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- Nama Paket -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Nama Paket <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_paket" x-model="editPaket.nama_paket" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                </div>

                <!-- Harga & Durasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Harga Paket (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga" x-model="editPaket.harga" min="0" step="any" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Durasi Sesi</label>
                        <input type="text" name="durasi" x-model="editPaket.durasi"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Jumlah Fotografer & Jumlah Foto -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Jumlah Fotografer <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_fotografer" x-model="editPaket.jumlah_fotografer" min="1" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-extrabold mb-1.5">Estimasi Foto Diterima <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_foto" x-model="editPaket.jumlah_foto" min="1" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Fasilitas & Output -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Fasilitas & Output Hasil Foto</label>
                    <textarea name="fasilitas" x-model="editPaket.fasilitas" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Deskripsi Paket Tambahan -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Deskripsi Tambahan (Opsional)</label>
                    <textarea name="deskripsi" x-model="editPaket.deskripsi" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Status Paket -->
                <div>
                    <label class="block text-slate-700 font-extrabold mb-1.5">Status Paket</label>
                    <select name="status" x-model="editPaket.status" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-all">
                        <option value="aktif">Aktif (Tersedia untuk Reservasi)</option>
                        <option value="nonaktif">Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <!-- Submit Button Group -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="openEditPaket = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition-all">
                        Perbarui Paket
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 5: KONFIRMASI HAPUS                                      -->
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

            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-2xl text-[11px] leading-relaxed">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
            </div>

            <form method="POST" :action="deleteUrl" class="flex items-center justify-end gap-3 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="openDeleteModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition-all">
                    Ya, Hapus Sekarang
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
