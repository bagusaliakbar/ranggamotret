@extends('layouts.admin')

@section('title', 'Kelola Testimoni — Admin RanggaMotret.id')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Kelola Testimoni</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">Kelola Ulasan Testimoni</h1>
        <p class="text-xs text-slate-600 font-medium">Moderasi testimoni pelanggan sebelum ditampilkan secara publik.</p>
    </div>

    <div class="clean-card rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama Pelanggan</th>
                        <th class="p-4">Rating</th>
                        <th class="p-4">Komentar</th>
                        <th class="p-4">Status Moderasi</th>
                        <th class="p-4 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($testimonis as $t)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-bold text-slate-900">{{ $t->user->name }}</td>
                            <td class="p-4 text-amber-500 font-bold">
                                {{ $t->rating }} / 5 <i class="ph-fill ph-star text-xs"></i>
                            </td>
                            <td class="p-4 text-slate-700 max-w-sm">"{{ $t->komentar }}"</td>
                            <td class="p-4">
                                @php
                                    $tStatusClass = match($t->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $tStatusClass }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <form method="POST" action="{{ route('admin.testimonials.update-status', $t->id) }}" class="flex items-center justify-center gap-2">
                                    @csrf
                                    <button type="submit" name="status" value="approved" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] uppercase shadow-sm">
                                        Setujui (Publikkan)
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[10px] uppercase shadow-sm">
                                        Sembunyikan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">Belum ada testimoni masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
