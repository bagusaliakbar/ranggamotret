@extends('layouts.admin')

@section('title', 'Ruang Belajar Akademi — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Ruang Belajar Akademi</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">Dashboard Ruang Belajar</h1>
        <p class="text-xs text-slate-600 font-medium">Akses kelas kursus terdaftar, pantau progress pembelajaran, dan unduh sertifikat.</p>
    </div>

    <!-- Enrolled Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($enrollments as $e)
            <div class="clean-card rounded-2xl overflow-hidden flex flex-col justify-between group">
                <div>
                    <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                        <img src="{{ $e->kursus->thumbnail }}" alt="{{ $e->kursus->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                            Level: {{ $e->kursus->level }}
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-slate-700 transition-colors">{{ $e->kursus->nama }}</h3>
                        
                        <!-- Progress Bar -->
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-700">Progress Belajar</span>
                                <span class="text-slate-900">{{ $e->progress_persen }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                <div class="h-full bg-slate-900 transition-all duration-500" style="width: {{ $e->progress_persen }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 space-y-3">
                    <a href="{{ route('student.learn', $e->kursus_id) }}" class="btn-dark w-full py-3 rounded-xl text-center text-xs font-bold block shadow-md">
                        <i class="fa-solid fa-play mr-1"></i> Lanjutkan Belajar
                    </a>

                    @if($e->sertifikat)
                        <a href="{{ route('student.certificate.show', $e->sertifikat->id) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold block text-center shadow-sm">
                            <i class="fa-solid fa-award mr-1"></i> Cetak Sertifikat
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 clean-card p-12 text-center rounded-2xl space-y-3">
                <i class="fa-solid fa-graduation-cap text-4xl text-slate-300"></i>
                <h3 class="text-base font-bold text-slate-900">Belum Ada Kursus Diikuti</h3>
                <p class="text-xs text-slate-500 font-medium">Jelajahi katalog kursus kami dan mulailah perjalanan edukasi fotografi Anda.</p>
                <a href="{{ route('public.courses') }}" class="btn-dark inline-block px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider mt-2 shadow-md">
                    Lihat Katalog Kursus
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
