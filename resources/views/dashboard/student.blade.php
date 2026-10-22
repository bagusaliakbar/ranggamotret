@extends('layouts.admin')

@section('title', 'Kursus Akademi Saya — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kursus Akademi Saya</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Akses materi pelatihan, pantau progress modul belajar, kerjakan tugas praktik, dan raih sertifikat resmi.</p>
        </div>

        <a href="{{ route('public.courses') }}" class="btn-dark px-4 py-2.5 rounded-xl text-xs font-bold shadow-md inline-flex items-center gap-2 self-start sm:self-auto hover:scale-[1.02] transition-transform">
            <i class="ph-duotone ph-magnifying-glass text-sm"></i>
            <span>Jelajahi Katalog Kursus</span>
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Kursus -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kursus</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-graduation-cap"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $totalEnrolled ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Kelas yang Anda ikuti</div>
        </div>

        <!-- 2. Sedang Berjalan -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Sedang Belajar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-book-open"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ $inProgressCourses ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Modul dalam progres</div>
        </div>

        <!-- 3. Kursus Selesai -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Tuntas Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ $completedCourses ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">100% kelulusan materi</div>
        </div>

        <!-- 4. Sertifikat Diraih -->
        <div class="clean-card p-5 rounded-2xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Sertifikat Digital</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="ph-duotone ph-certificate"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600">{{ $totalCertificates ?? 0 }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Sertifikat kompetensi resmi</div>
        </div>
    </div>

    <!-- Enrolled Courses Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ph-duotone ph-chalkboard text-slate-700"></i>
                <span>Daftar Kursus Saya</span>
            </h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $enrollments->count() }} Kursus Terdaftar</span>
        </div>

        @if($enrollments->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrollments as $e)
                    @php
                        $isCompleted = ($e->progress_persen >= 100);
                    @endphp
                    <div class="clean-card rounded-3xl overflow-hidden flex flex-col justify-between group border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 bg-white">
                        <div>
                            <!-- Thumbnail with Badges -->
                            <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                                <img src="{{ $e->kursus->thumbnail }}" alt="{{ $e->kursus->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                <div class="absolute top-3 left-3 flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-900/90 text-white text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm shadow-sm">
                                        {{ $e->kursus->level }}
                                    </span>
                                </div>

                                @if($isCompleted)
                                    <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-md">
                                        <i class="ph-bold ph-check"></i> Selesai
                                    </div>
                                @endif
                            </div>

                            <!-- Course Info Body -->
                            <div class="p-6 space-y-4">
                                <div class="space-y-1.5">
                                    <p class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                                        <i class="ph-duotone ph-user text-slate-500"></i>
                                        <span>Pengampu: <strong class="text-slate-700">{{ $e->kursus->instruktur->name ?? 'Owner RanggaMotret' }}</strong></span>
                                    </p>
                                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-slate-700 transition-colors line-clamp-2 leading-snug">
                                        {{ $e->kursus->nama }}
                                    </h3>
                                </div>

                                <!-- Progress Bar -->
                                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                    <div class="flex justify-between text-[11px] font-bold">
                                        <span class="text-slate-500">Progress Belajar</span>
                                        <span class="{{ $isCompleted ? 'text-emerald-700' : 'text-slate-900' }}">{{ $e->progress_persen }}%</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                        <div class="h-full {{ $isCompleted ? 'bg-emerald-600' : 'bg-slate-900' }} transition-all duration-500 rounded-full" style="width: {{ $e->progress_persen }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Footer -->
                        <div class="p-6 pt-0 space-y-2.5">
                            <a href="{{ route('student.learn', $e->kursus_id) }}" class="btn-dark w-full py-3 rounded-xl text-center text-xs font-bold flex items-center justify-center gap-2 shadow-sm hover:scale-[1.01] active:scale-95 transition-all">
                                <i class="ph-duotone ph-play-circle text-base"></i>
                                <span>{{ $isCompleted ? 'Buka Kembali Materi' : ($e->progress_persen > 0 ? 'Lanjutkan Belajar' : 'Mulai Belajar Sekarang') }}</span>
                            </a>

                            @if($e->sertifikat)
                                <a href="{{ route('student.certificate.show', $e->sertifikat->id) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 text-xs font-bold flex items-center justify-center gap-1.5 transition-colors">
                                    <i class="ph-duotone ph-certificate text-base"></i>
                                    <span>Unduh Sertifikat Kelulusan</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="clean-card p-12 sm:p-16 text-center rounded-3xl space-y-4 border border-slate-200 bg-white">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    <i class="ph-duotone ph-graduation-cap"></i>
                </div>
                <div class="space-y-1.5 max-w-md mx-auto">
                    <h3 class="text-lg font-black text-slate-900">Belum Ada Kursus yang Diikuti</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Tingkatkan keterampilan fotografi Anda mulai dari dasar teknis hingga lighting komersial bersama Founder & Owner RanggaMotret.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('public.courses') }}" class="btn-dark px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 shadow-md hover:scale-[1.02] transition-transform">
                        <i class="ph-duotone ph-magnifying-glass text-sm"></i>
                        <span>Lihat Semua Katalog Kursus</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Certificates Section (if any earned) -->
    @if(isset($certificates) && $certificates->isNotEmpty())
        <div class="space-y-4 pt-6 border-t border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="ph-duotone ph-certificate text-amber-500"></i>
                        <span>Sertifikat Kelulusan Resmi Anda</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Sertifikat terverifikasi QR Code sebagai bukti kompetensi fotografi.</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                    {{ $certificates->count() }} Sertifikat Tersedia
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($certificates as $cert)
                    <div class="clean-card p-5 rounded-2xl border border-slate-200 flex items-center justify-between gap-4 bg-white hover:border-slate-300 transition-colors">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-200">
                                <i class="ph-duotone ph-certificate"></i>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="font-extrabold text-slate-900 text-xs">{{ $cert->kursus->nama }}</h4>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2">
                                    <span>No: <code class="font-mono text-slate-700 font-bold">{{ $cert->nomor_sertifikat }}</code></span>
                                    <span>•</span>
                                    <span>Nilai: <strong class="text-emerald-700">{{ $cert->nilai_akhir }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.certificate.show', $cert->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold flex items-center gap-1.5 shrink-0 transition-colors shadow-sm">
                            <i class="ph-duotone ph-download-simple"></i>
                            <span>Cetak PDF</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
