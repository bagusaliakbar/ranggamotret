@extends('layouts.app')

@section('title', 'Konsultasi Konsep Pemotretan — RanggaMotret.id')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12" x-data="{ selectedBudget: '{{ old('budget', '') }}' }">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-slate-900"></span> Diskusi & Perencanaan Kreatif
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
            Konsultasi <span class="font-serif italic font-normal text-slate-600">Konsep Sesi</span> Pemotretan.
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 font-medium max-w-2xl mx-auto">
            Punya ide tema spesifik, lokasi impian, atau kebutuhan anggaran khusus? Diskusikan dengan tim kreatif RanggaMotret tanpa dipungut biaya.
        </p>
    </div>

    <!-- 2 Columns Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- Left: Consultation Form (7 Cols) -->
        <div class="lg:col-span-7 clean-card p-8 sm:p-10 rounded-3xl space-y-6 shadow-sm border border-slate-200">
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
                            <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-400"></i>
                            <input type="text" name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors" placeholder="Nama Anda">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Email Aktif <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400"></i>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors" placeholder="nama@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="fa-brands fa-whatsapp absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors" placeholder="08123456789">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Kategori Layanan <span class="text-rose-500">*</span></label>
                        <select name="jenis_layanan" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-bold transition-colors">
                            @foreach($layananList as $lay)
                                <option value="{{ $lay }}">{{ $lay }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Rencana Tanggal Pemotretan</label>
                        <div class="relative">
                            <input type="date" name="tanggal" min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Estimasi Budget (Rp)</label>
                        <input type="number" name="budget" x-model="selectedBudget" placeholder="Misal: 5000000"
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
                    <input type="text" name="konsep" placeholder="Contoh: Indoor Modern Studio / Sunset Pantai / Nuansa Adat Tradisional"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors">
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Catatan Detail Permintaan</label>
                    <textarea name="kebutuhan" rows="4"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-900 focus:bg-white focus:outline-none text-slate-900 font-medium transition-colors leading-relaxed"
                        placeholder="Ceritakan gambaran momen spesial Anda, jumlah anggota keluarga, atau hal khusus yang ingin diabadikan..."></textarea>
                </div>

                <button type="submit" class="w-full btn-dark py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-slate-900/15 flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95 transition-all">
                    <i class="fa-solid fa-paper-plane text-emerald-400"></i> Kirim Permintaan Konsultasi
                </button>
            </form>
        </div>

        <!-- Right: Fast Response WhatsApp & FAQ Guide (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Fast Response Card -->
            <div class="clean-card p-6 sm:p-8 rounded-3xl bg-slate-900 text-white space-y-5 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl"></div>

                <div class="space-y-2">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider inline-flex items-center gap-1.5 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Respons Cepat 5-15 Menit
                    </span>
                    <h3 class="text-2xl font-black text-white tracking-tight">Butuh Tanya Cepat via WhatsApp?</h3>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        Langsung terhubung dengan creative consultant kami untuk memeriksa ketersediaan tanggal atau estimasi biaya sesi foto Anda.
                    </p>
                </div>

                <a href="https://wa.me/628123456789?text=Halo%20Studio%20RanggaMotret,%20saya%20ingin%20konsultasi%20mengenai%20layanan%20foto" 
                   target="_blank"
                   class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 px-6 rounded-2xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2.5 shadow-lg shadow-emerald-900/40 transition-all hover:scale-[1.02]">
                    <i class="fa-brands fa-whatsapp text-lg"></i> Chat Tim via WhatsApp
                </a>
            </div>

            <!-- Consultation Steps Card -->
            <div class="clean-card p-6 rounded-3xl border border-slate-200 bg-white space-y-4">
                <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-route text-slate-700"></i> Alur Konsultasi Klien:
                </h4>
                
                <div class="space-y-3 text-xs text-slate-600 font-medium">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-900 font-black text-[11px] flex items-center justify-center shrink-0 border border-slate-200">1</span>
                        <span>Isi formulir atau chat WhatsApp dengan gambaran konsep Anda.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-900 font-black text-[11px] flex items-center justify-center shrink-0 border border-slate-200">2</span>
                        <span>Fotografer kami mengirimkan *Moodboard* referensi visual & penawaran paket.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-900 font-black text-[11px] flex items-center justify-center shrink-0 border border-slate-200">3</span>
                        <span>Kunci tanggal pemotretan & jadwal sesi melalui portal member Anda.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
