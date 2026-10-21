@extends('layouts.admin')

@section('title', 'Manajemen Konsultasi - Admin RanggaMotret')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Manajemen Permintaan Konsultasi Pre-Booking</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Tanggapi pertanyaan calon client seputar konsep foto, jadwal, dan rekomendasi paket.</p>
    </div>

    <div class="clean-card rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-900 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama & WA</th>
                        <th class="p-4">Layanan</th>
                        <th class="p-4">Konsep & Lokasi</th>
                        <th class="p-4">Budget</th>
                        <th class="p-4">Kebutuhan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Tanggapan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($consultations as $c)
                        <tr class="hover:bg-slate-50 transition-colors" x-data="{ openReply: false }">
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $c->nama }}</div>
                                <div class="text-[10px] text-slate-500">{{ $c->phone }} ({{ $c->email }})</div>
                            </td>
                            <td class="p-4 font-bold text-slate-900">{{ $c->jenis_layanan }}</td>
                            <td class="p-4">
                                <div class="text-slate-900">{{ $c->konsep ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $c->lokasi ?? '-' }}</div>
                            </td>
                            <td class="p-4 font-mono font-bold text-slate-700">
                                {{ $c->budget ? 'Rp ' . number_format($c->budget, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs">{{ $c->kebutuhan }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $c->status === 'replied' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button @click="openReply = true" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold transition-colors shadow-sm">
                                    <i class="fa-solid fa-reply mr-1"></i> Tanggapi
                                </button>

                                <div x-show="openReply" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                                    <div class="bg-white p-6 rounded-3xl max-w-md w-full text-left space-y-4 border border-slate-200 shadow-xl" @click.away="openReply = false">
                                        <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                                            <h4 class="font-bold text-base text-slate-900">Tanggapi Konsultasi: {{ $c->nama }}</h4>
                                            <button @click="openReply = false" class="text-slate-400 hover:text-slate-900 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.consultations.reply', $c->id) }}" class="space-y-4 text-xs">
                                            @csrf
                                            <div>
                                                <label class="block text-slate-700 font-bold mb-2">Tanggapan / Rekomendasi Paket Studio</label>
                                                <textarea name="tanggapan_admin" rows="4" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors">{{ old('tanggapan_admin', $c->tanggapan_admin) }}</textarea>
                                            </div>
                                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-bold shadow-md transition-all">Kirim Tanggapan</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
