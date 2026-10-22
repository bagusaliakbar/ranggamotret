@extends('layouts.admin')

@section('title', 'Notifikasi — RanggaMotret.id')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Notifikasi</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">Notifikasi Akun</h1>
        <p class="text-xs text-slate-600 font-medium">Pesan pembaruan status booking, verifikasi pembayaran, dan perkembangan kursus.</p>
    </div>

    <div class="space-y-4">
        @forelse($notifikasis as $n)
            <div class="clean-card p-5 rounded-2xl flex items-start justify-between gap-4 {{ $n->is_read ? 'opacity-70 bg-slate-50' : 'bg-white border-slate-300' }}">
                <div class="space-y-1">
                    <div class="font-bold text-sm text-slate-900">{{ $n->judul }}</div>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $n->pesan }}</p>
                    <div class="text-[10px] text-slate-400 font-medium pt-1"><i class="ph-duotone ph-clock mr-1"></i> {{ $n->created_at->diffForHumans() }}</div>
                </div>

                @if(!$n->is_read)
                    <a href="{{ route('notifications.read', $n->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold text-[10px] uppercase whitespace-nowrap shadow-sm">
                        Tandai Dibaca
                    </a>
                @endif
            </div>
        @empty
            <div class="clean-card p-12 text-center rounded-2xl text-slate-500 text-xs font-medium">
                Belum ada pemberitahuan notifikasi.
            </div>
        @endforelse
    </div>
</div>
@endsection
