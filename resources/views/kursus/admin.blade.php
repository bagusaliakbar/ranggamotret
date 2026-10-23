@extends('layouts.admin')

@section('title', 'Kelola Kursus & Modul LMS - Admin RanggaMotret')

@section('content')
    <div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8" x-data="{ 
            openAddCourse: false,
            openEditCourse: false,
            editCourseData: {},
            openAddModule: false,
            selectedCourseId: null,
            selectedCourseName: '',
            openEditModule: false,
            editModuleData: {},
            openAddMateri: false,
            selectedModulId: null,
            selectedModulTitle: '',
            materiType: 'teks',
            quizQuestions: [
                {
                    pertanyaan: '',
                    pilihan_a: '',
                    pilihan_b: '',
                    pilihan_c: '',
                    pilihan_d: '',
                    jawaban_benar: 'A',
                    bobot_nilai: 10,
                    pembahasan: ''
                }
            ],
            addQuizQuestion() {
                this.quizQuestions.push({
                    pertanyaan: '',
                    pilihan_a: '',
                    pilihan_b: '',
                    pilihan_c: '',
                    pilihan_d: '',
                    jawaban_benar: 'A',
                    bobot_nilai: 10,
                    pembahasan: ''
                });
            },
            removeQuizQuestion(index) {
                if (this.quizQuestions.length > 1) {
                    this.quizQuestions.splice(index, 1);
                }
            },
            openEditMateri: false,
            editMateriData: {}
         }">

        <!-- Top Header -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Kelola Kursus dan Materi Kursus</span>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900">Pengelolaan Kursus & Materi Kursus</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">Buat kursus baru, kelola silabus modul, serta atur materi
                    bacaan dan video pembelajaran.</p>
            </div>

            <button @click="openAddCourse = true"
                class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                <i class="ph-duotone ph-plus"></i> Tambah Kursus
            </button>
        </div>

        <!-- Courses List Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            @forelse($courses as $c)
                <div
                    class="clean-card p-6 rounded-3xl space-y-6 flex flex-col justify-between border-slate-200 hover:border-slate-300 transition-all">
                    <div class="space-y-5">
                        <!-- Header Card: Info & Action Toolbar -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                @if($c->thumbnail)
                                    <img src="{{ $c->thumbnail }}"
                                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow-sm border border-slate-100 shrink-0">
                                @else
                                    <div
                                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 border border-slate-200">
                                        <i class="fa-solid fa-graduation-cap text-2xl"></i>
                                    </div>
                                @endif
                                <div class="space-y-1.5">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <span
                                            class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-0.5 rounded-md border border-slate-200">
                                            Level: {{ $c->level }}
                                        </span>
                                        <form method="POST" action="{{ route('admin.courses.toggle-status', $c->id) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit" title="Klik untuk ubah status"
                                                class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase border cursor-pointer transition-all {{ $c->status === 'aktif' ? 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200' }}">
                                                <i
                                                    class="fa-solid fa-circle text-[8px] mr-1 {{ $c->status === 'aktif' ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                                                {{ $c->status }}
                                            </button>
                                        </form>
                                    </div>
                                    <h3 class="text-lg font-extrabold text-slate-900 leading-snug">{{ $c->nama }}</h3>
                                    <div class="text-xs text-slate-600 font-medium flex flex-wrap gap-x-3 gap-y-1">
                                        <span>Biaya: <strong class="text-slate-900 font-extrabold">Rp
                                                {{ number_format($c->harga, 0, ',', '.') }}</strong></span>
                                        <span>&bull; Durasi: <strong>{{ $c->durasi }}</strong></span>
                                        <span>&bull; <strong>{{ $c->enrollments->count() }}</strong> Siswa</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Pengampu (Owner): <span
                                            class="text-slate-600 font-bold">{{ $c->instruktur->name ?? 'Owner' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions for Course -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button @click="editCourseData = {
                                                id: {{ $c->id }},
                                                nama: '{{ addslashes($c->nama) }}',
                                                harga: {{ (int) $c->harga }},
                                                level: '{{ $c->level }}',
                                                durasi: '{{ addslashes($c->durasi) }}',
                                                instruktur_id: {{ $c->instruktur_id }},
                                                thumbnail: '{{ addslashes($c->thumbnail ?? '') }}',
                                                status: '{{ $c->status }}',
                                                deskripsi: '{{ addslashes(str_replace(["\r", "\n"], ' ', $c->deskripsi ?? '')) }}'
                                            }; openEditCourse = true"
                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors"
                                    title="Edit Kursus">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.courses.destroy', $c->id) }}"
                                    onsubmit="return confirm('Hapus kursus {{ addslashes($c->nama) }} beserta seluruh modul dan materi di dalamnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors"
                                        title="Hapus Kursus">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Modules & Syllabus Accordion Container -->
                        <div class="border-t border-slate-100 pt-4 space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span
                                    class="font-extrabold text-slate-900 uppercase tracking-wider text-[10px] flex items-center gap-1.5">
                                    <i class="fa-solid fa-book-open text-slate-500"></i> Daftar Modul Silabus
                                    ({{ $c->moduls->count() }})
                                </span>
                                <button
                                    @click="selectedCourseId = {{ $c->id }}; selectedCourseName = '{{ addslashes($c->nama) }}'; openAddModule = true"
                                    class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[10px] shadow-sm transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-[9px]"></i> Tambah Modul
                                </button>
                            </div>

                            <!-- Modules List -->
                            <div class="space-y-3 pt-1">
                                @forelse($c->moduls as $mod)
                                    <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden"
                                        x-data="{ expanded: false }">
                                        <div class="p-3.5 flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2.5 flex-1 min-w-0 cursor-pointer"
                                                @click="expanded = !expanded">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold flex items-center justify-center shrink-0">
                                                    {{ $mod->urutan }}
                                                </span>
                                                <span class="font-bold text-slate-900 text-xs truncate">{{ $mod->judul }}</span>
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-bold bg-white text-slate-500 border border-slate-200 shrink-0">
                                                    {{ $mod->materis->count() }} Materi
                                                </span>
                                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform"
                                                    :class="expanded ? 'rotate-180' : ''"></i>
                                            </div>

                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <!-- Add Materi Button -->
                                                <button
                                                    @click="selectedModulId = {{ $mod->id }}; selectedModulTitle = '{{ addslashes($mod->judul) }}'; openAddMateri = true"
                                                    class="px-2.5 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold shadow-sm transition-colors flex items-center gap-1"
                                                    title="Tambah Materi Baru">
                                                    <i class="fa-solid fa-plus text-[8px]"></i> Materi
                                                </button>

                                                <!-- Edit Module -->
                                                <button @click="editModuleData = {
                                                                    id: {{ $mod->id }},
                                                                    judul: '{{ addslashes($mod->judul) }}',
                                                                    urutan: {{ $mod->urutan }},
                                                                    deskripsi: '{{ addslashes($mod->deskripsi ?? '') }}'
                                                                }; openEditModule = true"
                                                    class="w-7 h-7 rounded-md bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors"
                                                    title="Edit Modul">
                                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                                </button>

                                                <!-- Delete Module -->
                                                <form method="POST" action="{{ route('admin.courses.modules.destroy', $mod->id) }}"
                                                    onsubmit="return confirm('Hapus modul {{ addslashes($mod->judul) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-7 h-7 rounded-md bg-white border border-slate-200 hover:bg-rose-50 text-rose-600 flex items-center justify-center transition-colors"
                                                        title="Hapus Modul">
                                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Expanded Materials List -->
                                        <div x-show="expanded" x-transition
                                            class="border-t border-slate-200 bg-white p-3 space-y-2 text-xs">
                                            @forelse($mod->materis as $mat)
                                                <div
                                                    class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition-colors">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        @php
                                                            $typeIcon = match ($mat->tipe) {
                                                                'video' => 'fa-video text-rose-500',
                                                                'pdf' => 'fa-file-pdf text-amber-500',
                                                                'link' => 'fa-link text-blue-500',
                                                                default => 'fa-file-lines text-slate-500',
                                                            };
                                                        @endphp
                                                        <i class="fa-solid {{ $typeIcon }} text-xs shrink-0"></i>
                                                        <div class="truncate">
                                                            <span class="font-bold text-slate-800">{{ $mat->judul }}</span>
                                                            @if($mat->video_url)
                                                                <span
                                                                    class="text-[10px] text-slate-400 block truncate">{{ $mat->video_url }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-1 shrink-0">
                                                        <button @click="editMateriData = {
                                                                                id: {{ $mat->id }},
                                                                                judul: '{{ addslashes($mat->judul) }}',
                                                                                tipe: '{{ $mat->tipe }}',
                                                                                urutan: {{ $mat->urutan }},
                                                                                video_url: '{{ addslashes($mat->video_url ?? '') }}',
                                                                                isi: '{{ addslashes(str_replace(["\r", "\n"], ' ', $mat->isi ?? '')) }}'
                                                                            }; openEditMateri = true"
                                                            class="w-6 h-6 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center transition-colors"
                                                            title="Edit Materi">
                                                            <i class="fa-solid fa-pen text-[9px]"></i>
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('admin.modules.materis.destroy', $mat->id) }}"
                                                            onsubmit="return confirm('Hapus materi {{ addslashes($mat->judul) }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="w-6 h-6 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors"
                                                                title="Hapus Materi">
                                                                <i class="fa-solid fa-trash text-[9px]"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                @if($mod->quizs->isEmpty())
                                                    <div class="p-3 text-center text-slate-400 text-[11px] italic">
                                                        Belum ada materi pembelajaran atau kuis dalam modul ini. Klik tombol <strong>+ Materi</strong>
                                                        di atas.
                                                    </div>
                                                @endif
                                            @endforelse

                                            {{-- Daftar Kuis Evaluasi pada Modul --}}
                                            @foreach($mod->quizs as $qz)
                                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-blue-50/70 border border-blue-200 hover:border-blue-300 transition-colors">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <i class="ph-duotone ph-list-checks text-blue-600 text-base shrink-0"></i>
                                                        <div class="truncate">
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-extrabold text-blue-950">{{ $qz->judul }}</span>
                                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-blue-100 text-blue-800 border border-blue-200">Kuis Evaluasi</span>
                                                            </div>
                                                            <span class="text-[10px] text-blue-700/80 block truncate">
                                                                Durasi: {{ $qz->durasi_menit }} Menit • Passing Grade: {{ $qz->passing_grade }}% • Total: {{ $qz->soals->count() }} Soal
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-1 shrink-0">
                                                        <form method="POST"
                                                            action="{{ route('admin.quizzes.destroy', $qz->id) }}"
                                                            onsubmit="return confirm('Hapus kuis evaluasi {{ addslashes($qz->judul) }} beserta seluruh butir soalnya?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="w-6 h-6 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors"
                                                                title="Hapus Kuis">
                                                                <i class="ph-duotone ph-trash text-xs"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="p-5 text-center text-slate-400 text-xs italic bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                        Belum ada modul silabus. Klik tombol <strong>+ Tambah Modul</strong> untuk mulai menyusun
                                        materi kursus.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full clean-card p-12 text-center rounded-3xl space-y-4">
                    <i class="fa-solid fa-graduation-cap text-5xl text-slate-300"></i>
                    <h3 class="font-extrabold text-slate-900 text-lg">Belum Ada Kursus Terdaftar</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Mulai buat kursus fotografi atau lighting masterclass
                        pertama untuk akademi RanggaMotret.</p>
                    <button @click="openAddCourse = true" class="btn-dark px-6 py-3 rounded-xl text-xs font-bold shadow-md">
                        <i class="fa-solid fa-plus mr-1"></i> Buat Kursus Sekarang
                    </button>
                </div>
            @endforelse
        </div>

        <!-- ==================== MODALS ==================== -->

        <!-- 1. Modal Tambah Kursus Baru -->
        <div x-show="openAddCourse" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4">
            <div class="bg-white p-6 sm:p-8 rounded-3xl max-w-lg w-full space-y-6 text-xs border border-slate-200 shadow-2xl max-h-[90vh] overflow-y-auto"
                @click.away="openAddCourse = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-base text-slate-900">Tambah Kursus</h3>
                    <button type="button" @click="openAddCourse = false" 
                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all shrink-0"
                        title="Tutup (Esc)">
                        <i class="ph-duotone ph-x text-base font-bold"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Nama Kursus <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Contoh: Studio Lighting Masterclass"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Biaya Kursus (Rp) <span
                                    class="text-rose-500">*</span></label>
                            <input type="number" name="harga" required placeholder="750000"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Level <span
                                    class="text-rose-500">*</span></label>
                            <select name="level"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                                <option value="Pemula">Pemula</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Mahir">Mahir</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Durasi Kursus <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="durasi" required placeholder="Contoh: 4 Minggu / 12 Jam"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Status Publikasi</label>
                            <select name="status"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                                <option value="aktif">Aktif (Tampil)</option>
                                <option value="draft">Draft (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Pengampu Kursus</label>
                        <div class="px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold flex items-center justify-between">
                            <span>Owner Studio (RanggaMotret)</span>
                            <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold">Otomatis</span>
                        </div>
                        <input type="hidden" name="instruktur_id" value="{{ Auth::id() }}">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">URL Thumbnail Gambar</label>
                        <input type="text" name="thumbnail" placeholder="https://images.unsplash.com/..."
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="3" placeholder="Jelaskan poin-poin yang akan dipelajari..."
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl font-bold shadow-md transition-all">
                        Simpan Kursus Baru
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Modal Edit Kursus -->
        <div x-show="openEditCourse" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4">
            <div class="bg-white p-6 sm:p-8 rounded-3xl max-w-lg w-full space-y-6 text-xs border border-slate-200 shadow-2xl max-h-[90vh] overflow-y-auto"
                @click.away="openEditCourse = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-base text-slate-900">Edit Data Kursus</h3>
                    <button type="button" @click="openEditCourse = false" 
                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all shrink-0"
                        title="Tutup (Esc)">
                        <i class="ph-duotone ph-x text-base font-bold"></i>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/admin/courses') }}/' + editCourseData.id" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Nama Kursus <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="nama" x-model="editCourseData.nama" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Biaya Kursus (Rp) <span
                                    class="text-rose-500">*</span></label>
                            <input type="number" name="harga" x-model="editCourseData.harga" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Level <span
                                    class="text-rose-500">*</span></label>
                            <select name="level" x-model="editCourseData.level"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                                <option value="Pemula">Pemula</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Mahir">Mahir</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Durasi Kursus <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="durasi" x-model="editCourseData.durasi" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Status Publikasi</label>
                            <select name="status" x-model="editCourseData.status"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                                <option value="aktif">Aktif (Tampil)</option>
                                <option value="draft">Draft (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Pengampu Kursus</label>
                        <div class="px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold flex items-center justify-between">
                            <span>Owner Studio (RanggaMotret)</span>
                            <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold">Otomatis</span>
                        </div>
                        <input type="hidden" name="instruktur_id" :value="editCourseData.instruktur_id || '{{ Auth::id() }}'">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">URL Thumbnail Gambar</label>
                        <input type="text" name="thumbnail" x-model="editCourseData.thumbnail"
                            placeholder="https://images.unsplash.com/..."
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi" x-model="editCourseData.deskripsi" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl font-bold shadow-md transition-all">
                        Simpan Perubahan Kursus
                    </button>
                </form>
            </div>
        </div>

        <!-- 3. Modal Tambah Modul Silabus -->
        <div x-show="openAddModule" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4">
            <div class="bg-white p-6 rounded-3xl max-w-md w-full space-y-6 text-xs border border-slate-200 shadow-2xl"
                @click.away="openAddModule = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Tambah Modul Silabus</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5" x-text="selectedCourseName"></p>
                    </div>
                    <button type="button" @click="openAddModule = false" 
                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all shrink-0"
                        title="Tutup (Esc)">
                        <i class="ph-duotone ph-x text-base font-bold"></i>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/admin/courses') }}/' + selectedCourseId + '/modules'"
                    class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Judul Modul <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="judul" required placeholder="Contoh: Pengenalan Anatomi Kamera"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Urutan Modul (Nomor)</label>
                        <input type="number" name="urutan" value="1" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Deskripsi / Capaian Modul (Opsional)</label>
                        <textarea name="deskripsi" rows="2" placeholder="Ringkasan topik yang dibahas..."
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-bold shadow-md transition-all">
                        Simpan Modul Silabus
                    </button>
                </form>
            </div>
        </div>

        <!-- 4. Modal Edit Modul Silabus -->
        <div x-show="openEditModule" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4">
            <div class="bg-white p-6 rounded-3xl max-w-md w-full space-y-6 text-xs border border-slate-200 shadow-2xl"
                @click.away="openEditModule = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-base text-slate-900">Edit Modul Silabus</h3>
                    <button type="button" @click="openEditModule = false" 
                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all shrink-0"
                        title="Tutup (Esc)">
                        <i class="ph-duotone ph-x text-base font-bold"></i>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/admin/modules') }}/' + editModuleData.id" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Judul Modul <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="editModuleData.judul" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Urutan Modul (Nomor)</label>
                        <input type="number" name="urutan" x-model="editModuleData.urutan" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Deskripsi / Capaian Modul</label>
                        <textarea name="deskripsi" x-model="editModuleData.deskripsi" rows="2"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-bold shadow-md transition-all">
                        Simpan Perubahan Modul
                    </button>
                </form>
            </div>
        </div>

        <!-- 5. Modal Tambah Materi Pembelajaran / Kuis Evaluasi -->
        <div x-show="openAddMateri" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full border border-slate-200/80 shadow-2xl max-h-[92vh] flex flex-col overflow-hidden"
                @click.away="openAddMateri = false">
                <!-- Modal Sticky Header -->
                <div class="px-6 py-4 sm:px-7 sm:py-5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                    <div class="space-y-0.5 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="materiType === 'quiz' ? 'bg-blue-600' : 'bg-slate-900'"></span>
                            <h3 class="font-extrabold text-base text-slate-900" x-text="materiType === 'quiz' ? 'Tambah Kuis Evaluasi Pilihan Ganda' : 'Tambah Materi Pembelajaran'"></h3>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium truncate" x-text="'Modul: ' + selectedModulTitle"></p>
                    </div>
                    <button type="button" @click="openAddMateri = false" 
                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all shrink-0"
                        title="Tutup Modal (Esc)">
                        <i class="ph-duotone ph-x text-base font-bold"></i>
                    </button>
                </div>

                <!-- Modal Scrollable Body with Luxury Custom Scrollbar -->
                <form method="POST" :action="materiType === 'quiz' ? '{{ url('/admin/modules') }}/' + selectedModulId + '/quizzes' : '{{ url('/admin/modules') }}/' + selectedModulId + '/materis'"
                    class="flex flex-col flex-1 overflow-hidden">
                    @csrf

                    <div class="p-6 sm:p-7 space-y-5 overflow-y-auto custom-scrollbar flex-1 text-xs">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">
                                <span x-text="materiType === 'quiz' ? 'Judul Kuis Evaluasi' : 'Judul Materi'"></span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" required :placeholder="materiType === 'quiz' ? 'Contoh: Kuis Evaluasi Pemahaman Exposure & Lighting' : 'Contoh: Mengenal Sensor Full-Frame vs APS-C'"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Tipe Konten <span
                                    class="text-rose-500">*</span></label>
                            <select name="tipe" x-model="materiType" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                                <option value="teks">Teks & Artikel Bacaan</option>
                                <option value="video">Video Pembelajaran</option>
                                <option value="pdf">Dokumen / E-Book PDF</option>
                                <option value="link">Link Eksternal</option>
                                <option value="quiz">Kuis Evaluasi (Pilihan Ganda)</option>
                            </select>
                        </div>
                        <template x-if="materiType !== 'quiz'">
                            <div>
                                <label class="block text-slate-700 font-bold mb-1">Urutan Materi</label>
                                <input type="number" name="urutan" value="1" required
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                            </div>
                        </template>
                        <template x-if="materiType === 'quiz'">
                            <div>
                                <label class="block text-slate-700 font-bold mb-1">Durasi Pengerjaan (Menit) <span class="text-rose-500">*</span></label>
                                <input type="number" name="durasi_menit" value="15" min="1" required
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                            </div>
                        </template>
                    </div>

                    <!-- Fields Khusus Materi Pembelajaran Biasa -->
                    <div x-show="materiType !== 'quiz'" class="space-y-4">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">URL Video (Opsional, YouTube / Vimeo /
                                MP4)</label>
                            <input type="text" name="video_url" placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Isi Materi / Penjelasan Teks</label>
                            <textarea name="isi" rows="4"
                                placeholder="Tuliskan materi pembelajaran atau rangkuman teori di sini..."
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Fields Khusus Kuis Evaluasi Pilihan Ganda (Mendukung Multiple Butir Soal) -->
                    <div x-show="materiType === 'quiz'" class="space-y-5 bg-blue-50/50 p-4 sm:p-5 rounded-2xl border border-blue-200/70">
                        <!-- Parameter Global Kuis -->
                        <div class="flex items-center justify-between pb-3 border-b border-blue-200/60">
                            <span class="font-extrabold text-blue-900 text-xs flex items-center gap-1.5">
                                <i class="ph-duotone ph-sparkle text-blue-600 text-sm"></i> Parameter Kelulusan & Soal
                            </span>
                            <div class="flex items-center gap-2">
                                <label class="text-slate-600 font-bold text-[11px]">Passing Grade (%):</label>
                                <input type="number" name="passing_grade" value="70" min="1" max="100" required
                                    class="w-16 px-2 py-1 rounded-lg bg-white border border-blue-300 font-black text-slate-900 text-center">
                            </div>
                        </div>

                        <!-- Repeater Butir Soal -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-wide">
                                    Daftar Pertanyaan (<span x-text="quizQuestions.length"></span> Soal)
                                </span>
                            </div>

                            <template x-for="(q, idx) in quizQuestions" :key="idx">
                                <div class="p-4 rounded-2xl bg-white border border-blue-200/80 shadow-sm space-y-3.5 relative">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-[11px] font-black flex items-center justify-center"
                                                x-text="idx + 1"></span>
                                            <span class="font-bold text-slate-800 text-xs">Butir Soal Nomor <span x-text="idx + 1"></span></span>
                                        </div>
                                        <button type="button" @click="removeQuizQuestion(idx)"
                                            x-show="quizQuestions.length > 1"
                                            class="text-rose-500 hover:text-rose-700 text-xs font-bold flex items-center gap-1 hover:bg-rose-50 px-2 py-1 rounded-lg transition-colors"
                                            title="Hapus Butir Soal Ini">
                                            <i class="ph-duotone ph-trash text-sm"></i> Hapus Soal
                                        </button>
                                    </div>

                                    <div>
                                        <label class="block text-slate-800 font-bold mb-1">Pertanyaan Soal <span class="text-rose-500">*</span></label>
                                        <textarea :name="'soal[' + idx + '][pertanyaan]'" x-model="q.pertanyaan" rows="2" required placeholder="Tuliskan butir soal evaluasi pemahaman di sini..."
                                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-blue-600 focus:outline-none text-xs"></textarea>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-slate-800 font-bold text-[11px]">Pilihan Jawaban (A, B, C, D) <span class="text-rose-500">*</span></label>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">A</span>
                                                <input type="text" :name="'soal[' + idx + '][pilihan_a]'" x-model="q.pilihan_a" required placeholder="Pilihan A"
                                                    class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-blue-600 focus:outline-none">
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">B</span>
                                                <input type="text" :name="'soal[' + idx + '][pilihan_b]'" x-model="q.pilihan_b" required placeholder="Pilihan B"
                                                    class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-blue-600 focus:outline-none">
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">C</span>
                                                <input type="text" :name="'soal[' + idx + '][pilihan_c]'" x-model="q.pilihan_c" required placeholder="Pilihan C"
                                                    class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-blue-600 focus:outline-none">
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">D</span>
                                                <input type="text" :name="'soal[' + idx + '][pilihan_d]'" x-model="q.pilihan_d" required placeholder="Pilihan D"
                                                    class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-blue-600 focus:outline-none">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                        <div>
                                            <label class="block text-slate-800 font-bold mb-1 text-[11px]">Kunci Jawaban Benar <span class="text-rose-500">*</span></label>
                                            <select :name="'soal[' + idx + '][jawaban_benar]'" x-model="q.jawaban_benar" required
                                                class="w-full px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-400 text-blue-950 font-black text-xs focus:outline-none">
                                                <option value="A">Pilihan A</option>
                                                <option value="B">Pilihan B</option>
                                                <option value="C">Pilihan C</option>
                                                <option value="D">Pilihan D</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-slate-800 font-bold mb-1 text-[11px]">Bobot Nilai Soal</label>
                                            <input type="number" :name="'soal[' + idx + '][bobot_nilai]'" x-model="q.bobot_nilai" min="1"
                                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 font-bold text-xs focus:bg-white focus:outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-slate-800 font-bold mb-1 text-[11px]">Pembahasan & Penjelasan Teori (Opsional)</label>
                                        <textarea :name="'soal[' + idx + '][pembahasan]'" x-model="q.pembahasan" rows="1" placeholder="Penjelasan kenapa kunci jawaban tersebut benar..."
                                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-blue-600 focus:outline-none"></textarea>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addQuizQuestion()"
                                class="w-full py-2.5 rounded-xl border border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-100/50 text-blue-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                <i class="ph-duotone ph-plus-circle text-base"></i> Tambah Butir Soal Berikutnya
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Sticky Footer Action Bar -->
                <div class="px-6 py-4 sm:px-7 sm:py-4 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="openAddMateri = false"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                        <i :class="materiType === 'quiz' ? 'ph-duotone ph-list-checks' : 'ph-duotone ph-floppy-disk'" class="text-base"></i>
                        <span x-text="materiType === 'quiz' ? 'Simpan Kuis & Butir Soal' : 'Simpan Materi Pembelajaran'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

        <!-- 6. Modal Edit Materi Pembelajaran -->
        <div x-show="openEditMateri" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full border border-slate-200/80 shadow-2xl max-h-[92vh] flex flex-col overflow-hidden"
                @click.away="openEditMateri = false">
                <div class="px-6 py-4 sm:px-7 sm:py-5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                    <div class="space-y-0.5">
                        <h3 class="font-extrabold text-base text-slate-900">Edit Materi Pembelajaran</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Perbarui konten dan detail materi</p>
                    </div>
                    <button type="button" @click="openEditMateri = false" 
                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all shrink-0"
                        title="Tutup Modal (Esc)">
                        <i class="ph-duotone ph-x text-base font-bold"></i>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/admin/materis') }}/' + editMateriData.id" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-6 sm:p-7 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-xs">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Judul Materi <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="editMateriData.judul" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Tipe Materi <span
                                    class="text-rose-500">*</span></label>
                            <select name="tipe" x-model="editMateriData.tipe" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                                <option value="teks">Teks & Artikel Bacaan</option>
                                <option value="video">Video Pembelajaran</option>
                                <option value="pdf">Dokumen / E-Book PDF</option>
                                <option value="link">Link Eksternal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1">Urutan Materi</label>
                            <input type="number" name="urutan" x-model="editMateriData.urutan" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">URL Video (YouTube / Vimeo / MP4)</label>
                        <input type="text" name="video_url" x-model="editMateriData.video_url"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Isi Materi / Penjelasan Teks</label>
                        <textarea name="isi" x-model="editMateriData.isi" rows="4"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none"></textarea>
                    </div>

                    <!-- Modal Sticky Footer Action Bar -->
                    <div class="px-6 py-4 sm:px-7 sm:py-4 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="openEditMateri = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                            <i class="ph-duotone ph-floppy-disk text-base"></i>
                            <span>Simpan Perubahan Materi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
