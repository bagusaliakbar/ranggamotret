@extends('layouts.app')

@section('title', 'Konsultasi Konsep Pemotretan — RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12" x-data="{ selectedBudget: '{{ old('budget', '') }}' }">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-slate-900"></span> Diskusi & Perencanaan Konsep
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
            Konsultasi <span class="font-serif italic font-normal text-slate-600">Konsep Sesi</span> Pemotretan.
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 font-medium max-w-2xl mx-auto">
            Punya ide tema spesifik, lokasi impian, atau kebutuhan anggaran khusus? Diskusikan dengan tim RanggaMotret tanpa dipungut biaya.
        </p>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="max-w-3xl mx-auto p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="ph-duotone ph-check-circle text-2xl text-emerald-600 shrink-0"></i>
            <div>
                <p class="font-bold text-sm">Berhasil Terkirim!</p>
                <p class="text-emerald-700 font-normal">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-3xl mx-auto p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="ph-duotone ph-warning-circle text-2xl text-rose-600 shrink-0"></i>
            <div>
                <p class="font-bold text-sm">Gagal Mengirim</p>
                <p class="text-rose-700 font-normal">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-3xl mx-auto p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="ph-duotone ph-warning-circle text-lg text-rose-600"></i>
                <span>Terdapat kesalahan pada formulir konsultasi:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-4 font-medium">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Konsultasi Konsep (Centered Luxury Card) -->
    <div class="max-w-3xl mx-auto clean-card p-8 sm:p-10 rounded-3xl space-y-6 shadow-sm border border-slate-200">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-lg font-black text-slate-900">Formulir Pengajuan Diskusi Konsep</h3>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Tim kami akan meninjau dan merespons dalam waktu 1x24 jam kerja.</p>
        </div>

            <form method="POST" action="{{ route('public.consultation.store') }}" class="space-y-5 text-xs">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="ph-duotone ph-user absolute left-3.5 top-3.5 text-slate-400 text-base"></i>
                            <input type="text" name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" required
                                @class([
                                    'w-full pl-10 pr-4 py-3 rounded-xl border focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors',
                                    'border-rose-400 bg-rose-50/50' => $errors->has('nama'),
                                    'border-slate-200 bg-slate-50' => !$errors->has('nama'),
                                ])
                                placeholder="Nama Anda">
                        </div>
                        @error('nama')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Email Aktif <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="ph-duotone ph-envelope absolute left-3.5 top-3.5 text-slate-400 text-base"></i>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                                @class([
                                    'w-full pl-10 pr-4 py-3 rounded-xl border focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors',
                                    'border-rose-400 bg-rose-50/50' => $errors->has('email'),
                                    'border-slate-200 bg-slate-50' => !$errors->has('email'),
                                ])
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="ph-duotone ph-whatsapp-logo absolute left-3.5 top-3.5 text-emerald-600 text-base"></i>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                                @class([
                                    'w-full pl-10 pr-4 py-3 rounded-xl border focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors',
                                    'border-rose-400 bg-rose-50/50' => $errors->has('phone'),
                                    'border-slate-200 bg-slate-50' => !$errors->has('phone'),
                                ])
                                placeholder="08123456789">
                        </div>
                        @error('phone')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Kategori Layanan <span class="text-rose-500">*</span></label>
                        <select name="jenis_layanan" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-bold transition-colors">
                            @foreach($layananList as $lay)
                                <option value="{{ $lay }}" {{ old('jenis_layanan') == $lay ? 'selected' : '' }}>{{ $lay }}</option>
                            @endforeach
                        </select>
                        @error('jenis_layanan')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Rencana Tanggal Pemotretan</label>
                        <div class="relative">
                            <input type="date" name="tanggal" value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Estimasi Budget (Rp)</label>
                        <input type="number" name="budget" value="{{ old('budget') }}" x-model="selectedBudget" placeholder="Misal: 5000000"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-bold transition-colors">
                    </div>
                </div>

                <!-- Budget Quick Chips -->
                <div class="space-y-1.5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Pilihan Cepat Rentang Budget:</span>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button" @click="selectedBudget = 2500000" class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all"
                            :class="selectedBudget == 2500000 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'">
                            &lt; Rp 3 Jt
                        </button>
                        <button type="button" @click="selectedBudget = 5000000" class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all"
                            :class="selectedBudget == 5000000 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'">
                            Rp 3 - 7 Jt
                        </button>
                        <button type="button" @click="selectedBudget = 10000000" class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all"
                            :class="selectedBudget == 10000000 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'">
                            Rp 7 - 15 Jt
                        </button>
                        <button type="button" @click="selectedBudget = 20000000" class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all"
                            :class="selectedBudget == 20000000 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'">
                            Custom Luxury (&gt; 15 Jt)
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Konsep & Ide Lokasi Pemotretan</label>
                    <input type="text" name="konsep" value="{{ old('konsep') }}" placeholder="Contoh: Indoor Modern Studio / Sunset Pantai / Nuansa Adat Tradisional"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Catatan Detail Permintaan</label>
                    <textarea name="kebutuhan" rows="4"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors leading-relaxed"
                        placeholder="Ceritakan gambaran momen spesial Anda, jumlah anggota keluarga, atau hal khusus yang ingin diabadikan...">{{ old('kebutuhan') }}</textarea>
                </div>

                <button type="submit" class="w-full btn-dark py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-slate-900/15 flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95 transition-all">
                    <i class="ph-duotone ph-paper-plane-tilt text-emerald-400 text-base"></i> Kirim Permintaan Konsultasi
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
