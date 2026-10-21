@extends('layouts.app')

@section('title', 'Formulir Konsultasi — RanggaMotret.id')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Diskusi Konsep</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900">Konsultasi Pre-Booking Sesi Foto</h1>
        <p class="text-xs sm:text-sm text-slate-600">Punya kebutuhan atau konsep khusus? Diskusikan ide Anda bersama tim studio RanggaMotret.</p>
    </div>

    <div class="clean-card p-8 sm:p-10 rounded-2xl space-y-6">
        <form method="POST" action="{{ route('public.consultation.store') }}" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900" placeholder="Nama Anda">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900" placeholder="nama@email.com">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900" placeholder="08123456789">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kategori Layanan</label>
                    <select name="jenis_layanan" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900 font-medium">
                        @foreach($layananList as $lay)
                            <option value="{{ $lay }}">{{ $lay }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Rencana Tanggal Sesi</label>
                    <input type="date" name="tanggal" min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Estimasi Budget (Rp)</label>
                    <input type="number" name="budget" placeholder="Misal: 5000000"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Konsep & Ide Lokasi</label>
                <input type="text" name="konsep" placeholder="Indoor Studio / Outdoor Alam / Adat Tradisional"
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900">
            </div>

            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Catatan Detail Permintaan</label>
                <textarea name="kebutuhan" rows="4"
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:outline-none text-slate-900"
                    placeholder="Tuliskan harapan atau pertanyaan Anda seputar sesi pemotretan..."></textarea>
            </div>

            <button type="submit" class="w-full btn-dark py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md">
                <i class="fa-paper-plane fa-solid mr-2"></i> Kirim Permintaan Konsultasi
            </button>
        </form>
    </div>
</div>
@endsection
