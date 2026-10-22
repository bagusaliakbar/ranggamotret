@extends('layouts.app')

@section('title', 'Formulir Reservasi Fotografi — RanggaMotret.id')

@section('content')
@php
    $initialLayananId = $layananId ?? ($selectedPaket->layanan_id ?? ($layananList->first()->id ?? ''));
    $initialPaketId = $selectedPaket->id ?? ($layananList->firstWhere('id', $initialLayananId)->pakets->first()->id ?? '');
    
    // Prepare JSON dataset for Alpine real-time live preview
    $layananJson = $layananList->map(function($l) {
        return [
            'id' => $l->id,
            'nama' => $l->nama,
            'kategori' => $l->kategori,
            'thumbnail' => $l->thumbnail,
            'harga_mulai' => $l->harga_mulai,
            'pakets' => $l->pakets->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama_paket' => $p->nama_paket,
                    'harga' => $p->harga,
                    'harga_formatted' => 'Rp ' . number_format($p->harga, 0, ',', '.'),
                    'durasi' => $p->durasi,
                    'jumlah_fotografer' => $p->jumlah_fotografer,
                    'fasilitas' => $p->fasilitas,
                ];
            })
        ];
    });
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8" 
     x-data="{ 
        layananData: @js($layananJson),
        selectedLayanan: '{{ $initialLayananId }}',
        selectedPaket: '{{ $initialPaketId }}',
        tanggal: '{{ old('tanggal', date('Y-m-d')) }}',
        waktu: '{{ old('waktu', '') }}',
        lokasi: '{{ old('lokasi', '') }}',
        todayStr: '{{ date('Y-m-d') }}',
        popularSlots: [
            '08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:30', '16:30', '18:30', '19:30'
        ],
        init() {
            this.checkAndAdjustTime();
            this.ensureValidPaket();
        },
        ensureValidPaket() {
            const currentLayanan = this.getCurrentLayanan();
            if (currentLayanan && currentLayanan.pakets && currentLayanan.pakets.length > 0) {
                const paketExists = currentLayanan.pakets.some(p => p.id == this.selectedPaket);
                if (!paketExists) {
                    this.selectedPaket = currentLayanan.pakets[0].id;
                }
            }
        },
        getCurrentLayanan() {
            return this.layananData.find(l => l.id == this.selectedLayanan);
        },
        getCurrentPaket() {
            const l = this.getCurrentLayanan();
            if (!l || !l.pakets) return null;
            return l.pakets.find(p => p.id == this.selectedPaket) || l.pakets[0] || null;
        },
        getCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        },
        getMinTime() {
            if (this.tanggal === this.todayStr) {
                return this.getCurrentTime();
            }
            return '06:00';
        },
        isPastTime(timeStr) {
            if (!timeStr) return false;
            if (this.tanggal === this.todayStr) {
                return timeStr <= this.getCurrentTime();
            }
            return false;
        },
        checkAndAdjustTime() {
            if (this.tanggal === this.todayStr) {
                const curTime = this.getCurrentTime();
                if (this.waktu && this.waktu <= curTime) {
                    this.waktu = '';
                    const nextAvailable = this.popularSlots.find(s => s > curTime);
                    if (nextAvailable) {
                        this.waktu = nextAvailable;
                    }
                } else if (!this.waktu) {
                    const nextAvailable = this.popularSlots.find(s => s > curTime);
                    if (nextAvailable) {
                        this.waktu = nextAvailable;
                    }
                }
            } else {
                if (!this.waktu) {
                    this.waktu = '09:00';
                }
            }
        }
     }">

    <!-- Top Header -->
    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
                <a href="{{ route('home') }}" class="hover:text-slate-700 transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('public.services') }}" class="hover:text-slate-700 transition-colors">Fotografi</a>
                <span>/</span>
                <span class="text-slate-700">Formulir Reservasi</span>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-[10px] font-extrabold uppercase tracking-wider mb-2">
                <i class="ph-duotone ph-camera text-slate-700"></i> Reservasi Fotografi
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Formulir Booking Sesi Foto</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Konfigurasikan paket dokumentasi, jadwal pemotretan, dan lokasi venue yang Anda kehendaki.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('public.services') }}" class="btn-outline px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto">
                <i class="ph-duotone ph-arrow-left"></i> Kembali ke Katalog
            </a>
            @auth
                <a href="{{ route('customer.bookings.index') }}" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto shadow-sm">
                    <i class="ph-duotone ph-clock-counter-clockwise"></i> Riwayat Booking
                </a>
            @endauth
        </div>
    </div>

    <!-- 2 Columns Responsive Grid -->
    <form id="bookingForm" method="POST" action="{{ route('customer.bookings.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Step-by-Step Configuration (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Step 1: Layanan Fotografi -->
                <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-5 bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0">1</span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Pilih Jenis Layanan Spesialis</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Tentukan kategori dokumentasi fotografi yang Anda perlukan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($layananList as $l)
                            <label @click="selectedLayanan = '{{ $l->id }}'; ensureValidPaket()" 
                                   class="p-4 rounded-2xl bg-white border cursor-pointer flex flex-col justify-between transition-all hover:border-slate-400 hover:shadow-md"
                                   :class="selectedLayanan == '{{ $l->id }}' ? 'border-slate-900 ring-2 ring-slate-900 ring-offset-2 bg-slate-50/50' : 'border-slate-200'">
                                <input type="radio" name="layanan_id" value="{{ $l->id }}" x-model="selectedLayanan" class="sr-only">
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] text-slate-500 uppercase font-bold">{{ $l->kategori }}</span>
                                        <span x-show="selectedLayanan == '{{ $l->id }}'" class="text-emerald-600 text-xs font-bold">
                                            <i class="ph-duotone ph-check-circle"></i>
                                        </span>
                                    </div>
                                    <div class="font-extrabold text-slate-900 text-sm">{{ $l->nama }}</div>
                                </div>

                                <div class="text-[11px] text-slate-600 font-medium pt-3 border-t border-slate-100 mt-3 flex justify-between items-center">
                                    <span class="text-slate-400 text-[10px]">Mulai</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($l->harga_mulai, 0, ',', '.') }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Step 2: Select Package -->
                <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-5 bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0">2</span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Pilih Paket Fotografi</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Setiap paket mencakup durasi sesi, fotografer, dan deliverables yang berbeda.</p>
                            </div>
                        </div>
                    </div>

                    @foreach($layananList as $l)
                        <div x-show="selectedLayanan == '{{ $l->id }}'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($l->pakets as $p)
                                <label class="p-5 rounded-2xl bg-white border cursor-pointer transition-all space-y-3 flex flex-col justify-between hover:border-slate-400 hover:shadow-md"
                                    :class="selectedPaket == '{{ $p->id }}' ? 'border-slate-900 ring-2 ring-slate-900 ring-offset-2 bg-slate-50/50' : 'border-slate-200'">
                                    
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="paket_id" value="{{ $p->id }}" x-model="selectedPaket" class="mt-1 text-slate-900 focus:ring-slate-900">
                                        <div class="flex-1">
                                            <div class="font-extrabold text-slate-900 text-sm">{{ $p->nama_paket }}</div>
                                            <div class="text-slate-900 font-black text-base mt-0.5">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                                            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 mt-1">
                                                <span><i class="ph-duotone ph-clock text-slate-400"></i> {{ $p->durasi }}</span>
                                                <span>•</span>
                                                <span><i class="ph-duotone ph-user text-slate-400"></i> {{ $p->jumlah_fotografer }} Fotografer</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-[11px] text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed font-medium">
                                        {{ $p->fasilitas }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Step 3: Schedule & Location -->
                <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6 bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0">3</span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Tanggal, Waktu & Lokasi Sesi</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Pilih jadwal waktu yang tepat dan informasikan alamat venue pemotretan.</p>
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium hidden sm:inline-block">
                            <i class="ph-duotone ph-clock text-slate-400 mr-1"></i> Waktu lokal: <strong class="text-slate-800" x-text="getCurrentTime()"></strong> WIB
                        </span>
                    </div>

                    @if(isset($errors) && $errors->any())
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                            <div class="font-bold flex items-center gap-1.5"><i class="ph-duotone ph-warning-circle text-rose-600"></i> Mohon periksa kembali jadwal pemotretan:</div>
                            <ul class="list-disc list-inside text-[11px] text-rose-700 pl-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Tanggal Pemotretan -->
                        <div>
                            <label class="block text-slate-700 font-bold mb-2 text-xs">Tanggal Pemotretan <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal" required 
                                x-model="tanggal" 
                                @change="checkAndAdjustTime()"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:border-slate-900 focus:bg-white focus:outline-none transition-colors">
                            <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                                <i class="ph-duotone ph-calendar-check text-slate-500"></i> Tanggal lampau otomatis dinonaktifkan.
                            </p>
                        </div>

                        <!-- Waktu / Jam Sesi -->
                        <div>
                            <label class="block text-slate-700 font-bold mb-2 text-xs">Waktu / Jam Sesi <span class="text-rose-500">*</span></label>
                            <input type="time" name="waktu" required
                                x-model="waktu"
                                :min="getMinTime()"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border text-slate-900 font-bold focus:border-slate-900 focus:bg-white focus:outline-none transition-colors"
                                :class="isPastTime(waktu) ? 'border-rose-300 bg-rose-50 text-rose-900' : 'border-slate-200'">
                            
                            <!-- Real-time warning if past time is chosen for today -->
                            <div x-show="isPastTime(waktu)" x-cloak class="mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-medium flex items-center gap-1.5">
                                <i class="ph-duotone ph-warning-circle text-rose-600"></i>
                                <span>Jam sesi sudah terlewat untuk hari ini. Silakan pilih jam setelah <strong x-text="getCurrentTime()"></strong>.</span>
                            </div>
                        </div>

                        <!-- Quick Slot Selector Pills -->
                        <div class="md:col-span-2 bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                            <div class="flex items-center justify-between text-[11px] flex-wrap gap-1">
                                <span class="font-extrabold text-slate-800"><i class="ph-duotone ph-lightning text-amber-500 mr-1"></i> Pilihan Cepat Jam Sesi Populer:</span>
                                <span class="text-[10px] text-slate-400" x-show="tanggal === todayStr">Slot lampau otomatis dinonaktifkan</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="slot in popularSlots" :key="slot">
                                    <button type="button" 
                                        @click="if(!isPastTime(slot)) waktu = slot"
                                        :disabled="isPastTime(slot)"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border"
                                        :class="{
                                            'bg-slate-900 text-white border-slate-900 shadow-sm': waktu === slot && !isPastTime(slot),
                                            'bg-white text-slate-700 border-slate-200 hover:border-slate-900 hover:bg-slate-100': waktu !== slot && !isPastTime(slot),
                                            'bg-slate-100 text-slate-300 border-slate-200 line-through cursor-not-allowed opacity-60': isPastTime(slot)
                                        }">
                                        <span x-text="slot"></span>
                                        <span x-show="isPastTime(slot)" class="text-[9px] font-normal no-underline text-slate-400">(lewat)</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Venue Location -->
                        <div class="md:col-span-2">
                            <label class="block text-slate-700 font-bold mb-2 text-xs">Alamat Lokasi Pemotretan / Venue <span class="text-rose-500">*</span></label>
                            <input type="text" name="lokasi" required x-model="lokasi" placeholder="Nama Gedung / Hotel / Rumah / Alamat Lengkap Venue..."
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium placeholder-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition-colors">
                        </div>

                        <!-- Special Request Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-slate-700 font-bold mb-2 text-xs">Kebutuhan Khusus / Catatan Tema (Opsional)</label>
                            <textarea name="kebutuhan_khusus" rows="3" placeholder="Misal: Tema Adat Sunda Modern, Butuh cetak foto kanvas ukuran 60x90, atau permintaan dress code fotografer..."
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium placeholder-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition-colors leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Sticky Live Order Summary Sidebar (4 Cols) -->
            <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
                <div class="clean-card p-6 sm:p-7 rounded-3xl border border-slate-200 bg-white shadow-xl space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Ringkasan Pemesanan</span>
                        <h3 class="text-lg font-black text-slate-900 mt-0.5">Live Order Breakdown</h3>
                    </div>

                    <!-- Dynamic Service & Package Badge -->
                    <div class="space-y-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider" x-text="getCurrentLayanan()?.kategori"></span>
                                <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 text-[10px] font-extrabold">Aktif</span>
                            </div>
                            <div class="font-black text-slate-900 text-base" x-text="getCurrentLayanan()?.nama"></div>
                            
                            <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-xs">
                                <span class="text-slate-600 font-semibold" x-text="'Paket ' + (getCurrentPaket()?.nama_paket || '-')"></span>
                                <span class="font-black text-slate-900 text-sm" x-text="getCurrentPaket()?.harga_formatted || 'Rp 0'"></span>
                            </div>
                        </div>

                        <!-- Schedule Mini Chip -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1 text-xs font-semibold text-slate-700">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500"><i class="ph-duotone ph-calendar-blank mr-1"></i> Jadwal:</span>
                                <span class="font-bold text-slate-900" x-text="tanggal"></span>
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500"><i class="ph-duotone ph-clock mr-1"></i> Jam:</span>
                                <span class="font-bold text-slate-900" x-text="waktu ? waktu + ' WIB' : 'Belum dipilih'"></span>
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500"><i class="ph-duotone ph-users mr-1"></i> Kru Foto:</span>
                                <span class="font-bold text-slate-900" x-text="(getCurrentPaket()?.jumlah_fotografer || 1) + ' Fotografer'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Price Calculation Total -->
                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs font-bold text-slate-500">Estimasi Total Tagihan:</span>
                            <span class="text-2xl font-black text-slate-900" x-text="getCurrentPaket()?.harga_formatted || 'Rp 0'"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium">Pembayaran dapat diselesaikan melalui Midtrans Snap atau Transfer Bank setelah formulir dikirim.</p>
                    </div>

                    <!-- Primary Submit Button -->
                    <button type="submit" 
                        :disabled="isPastTime(waktu)"
                        class="w-full py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl transition-all flex items-center justify-center gap-2"
                        :class="isPastTime(waktu) ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'btn-dark shadow-slate-900/20 hover:scale-[1.02] active:scale-95'">
                        <i class="ph-duotone ph-lock-key" x-show="!isPastTime(waktu)"></i>
                        <span x-text="isPastTime(waktu) ? 'Pilih Jam Sesi yang Belum Terlewat' : 'Kirim Booking Pemotretan &rarr;'"></span>
                    </button>

                    <!-- Trust Guarantees -->
                    <div class="space-y-2 text-[11px] text-slate-500 font-medium pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="ph-duotone ph-check-circle text-emerald-600"></i>
                            <span>Konfirmasi Cepat Admin 1x24 Jam</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ph-duotone ph-check-circle text-emerald-600"></i>
                            <span>Bebas Reschedule Jadwal (Maks. H-7)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ph-duotone ph-shield-check text-slate-700"></i>
                            <span>Enkripsi Transaksi Midtrans Resmi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
