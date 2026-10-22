@extends('layouts.admin')

@section('title', 'Panel Penilaian Tugas — Instruktur RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Panel Evaluasi & Penilaian Tugas</h1>
        <p class="text-xs text-slate-600 font-medium">Periksa berkas kiriman tugas siswa, berikan nilai (0-100), dan masukan feedback.</p>
    </div>

    <div class="clean-card rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama Peserta</th>
                        <th class="p-4">Judul Tugas</th>
                        <th class="p-4">Berkas Kiriman</th>
                        <th class="p-4">Tanggal Kirim</th>
                        <th class="p-4">Nilai Terdaftar</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Beri Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($submissions as $sub)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-bold text-slate-900">{{ $sub->user->name }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ $sub->tugas->judul }}</td>
                            <td class="p-4">
                                <a href="{{ $sub->file_tugas }}" target="_blank" class="text-slate-900 font-bold hover:underline inline-flex items-center">
                                    <i class="ph-duotone ph-arrow-square-out mr-1 text-base"></i> Buka Link Berkas
                                </a>
                            </td>
                            <td class="p-4 text-slate-500">{{ date('d M Y, H:i', strtotime($sub->submitted_at)) }}</td>
                            <td class="p-4 font-extrabold text-slate-900 text-sm">
                                {{ $sub->nilai !== null ? $sub->nilai . ' / 100' : '-' }}
                            </td>
                            <td class="p-4">
                                @php
                                    $subBadgeClass = match($sub->status) {
                                        'graded' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'revision_requested' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $subBadgeClass }}">
                                    {{ $sub->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <form method="POST" action="{{ route('instructor.assignments.grade', $sub->id) }}" class="flex items-center justify-center gap-2">
                                    @csrf
                                    <input type="number" name="nilai" min="0" max="100" value="{{ $sub->nilai }}" placeholder="Skor" required
                                        class="w-16 px-2 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold text-center">
                                    <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="Catatan Evaluasi"
                                        class="w-36 px-2 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                                    <button type="submit" class="btn-dark px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase shadow-sm">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500 font-medium">Belum ada kiriman tugas dari siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
