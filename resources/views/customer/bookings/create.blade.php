@extends('layouts.admin')

@section('title', 'Buat Booking Pemotretan — RanggaMotret.id')

@section('content')
<div class="space-y-8" 
     x-data="{ 
        selectedLayanan: '{{ $layananId ?? ($layananList->first()->id ?? '') }}',
        tanggal: '{{ old('tanggal', date('Y-m-d')) }}',
        waktu: '{{ old('waktu', '') }}',
        todayStr: '{{ date('Y-m-d') }}',
        popularSlots: [
            '08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:30', '16:30', '18:30', '19:30'
        ],
        init() {
            this.checkAndAdjustTime();
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
    <div class="border-b border-slate-200 pb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Formulir Booking Sesi Fotografi</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Pilih jenis layanan, paket, jadwal, dan lokasi pemotretan yang Anda inginkan.</p>
    </div>

    <div class="clean-card p-8 rounded-3xl space-y-8">
        <form method="POST" action="{{ route('customer.bookings.store') }}" class="space-y-8 text-xs">
            @csrf

            <!-- Step 1: Layanan & Paket -->
            <div class="space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">1. Pilih Layanan Fotografi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($layananList as $l)
                        <label @click="selectedLayanan = '{{ $l->id }}'" class="p-4 rounded-2xl bg-white border cursor-pointer flex flex-col justify-between transition-all hover:border-slate-400 hover:shadow-md"
                            :class="selectedLayanan == '{{ $l->id }}' ? 'border-slate-900 ring-2 ring-slate-900 ring-offset-2' : 'border-slate-200'">
                            <input type="radio" name="layanan_id" value="{{ $l->id }}" x-model="selectedLayanan" class="sr-only">
                            <div class="space-y-1">
                                <span class="text-[10px] text-slate-500 uppercase font-bold">{{ $l->kategori }}</span>
                                <div class="font-bold text-slate-900 text-sm">{{ $l->nama }}</div>
                            </div>
                            <div class="text-[11px] text-slate-600 font-medium pt-3 border-t border-slate-100 mt-3">
                                Mulai Rp {{ number_format($l->harga_mulai, 0, ',', '.') }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Step 2: Select Package dynamically -->
            <div class="space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">2. Pilih Paket Fotografi</h3>
                @foreach($layananList as $l)
                    <div x-show="selectedLayanan == '{{ $l->id }}'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($l->pakets as $p)
                            <label class="p-5 rounded-2xl bg-white border cursor-pointer transition-all space-y-3 flex flex-col justify-between hover:border-slate-400 hover:shadow-md"
                                :class="'border-slate-200'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="paket_id" value="{{ $p->id }}" {{ ($selectedPaket->id ?? null) == $p->id ? 'checked' : '' }} class="mt-1 text-slate-900 focus:ring-slate-900">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $p->nama_paket }}</div>
                                        <div class="text-slate-700 font-extrabold text-sm mt-0.5">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium mt-1">Durasi: {{ $p->durasi }} | {{ $p->jumlah_fotografer }} Fotografer</div>
                                    </div>
                                </div>
                                <div class="text-[11px] text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                                    {{ $p->fasilitas }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Step 3: Schedule & Location -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-base font-extrabold text-slate-900">3. Tanggal, Waktu & Lokasi Sesi Pemotretan</h3>
                    <span class="text-[11px] text-slate-500 font-medium"><i class="fa-solid fa-clock text-slate-400 mr-1"></i> Waktu lokal: <span class="font-bold text-slate-700" x-text="getCurrentTime()"></span> WIB</span>
                </div>

                @if(isset($errors) && $errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                        <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-600"></i> Mohon periksa kembali jadwal pemotretan:</div>
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
                        <label class="block text-slate-700 font-bold mb-2">Tanggal Pemotretan <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="date" name="tanggal" required 
                                x-model="tanggal" 
                                @change="checkAndAdjustTime()"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-calendar-check text-slate-500"></i> Tanggal lampau otomatis dinonaktifkan (disable).
                        </p>
                    </div>

                    <!-- Waktu / Jam Sesi -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-2">Waktu / Jam Sesi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="time" name="waktu" required
                                x-model="waktu"
                                :min="getMinTime()"
                                class="w-full px-4 py-3 rounded-xl bg-white border text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors"
                                :class="isPastTime(waktu) ? 'border-rose-300 bg-rose-50/50 text-rose-900' : 'border-slate-200'">
                        </div>
                        
                        <!-- Real-time warning if past time is chosen for today -->
                        <div x-show="isPastTime(waktu)" x-cloak class="mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                            <span>Jam sesi ini sudah terlewat untuk hari ini. Silakan pilih jam setelah <strong x-text="getCurrentTime()"></strong>.</span>
                        </div>
                        
                        <p x-show="!isPastTime(waktu)" class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-info-circle text-slate-500"></i> Jam operasional sesi: 08:00 - 20:00 WIB.
                        </p>
                    </div>

                    <!-- Quick Slot Selector Pills -->
                    <div class="md:col-span-2 bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                        <div class="flex items-center justify-between text-[11px] flex-wrap gap-1">
                            <span class="font-bold text-slate-700"><i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Pilihan Cepat Slot Jam Sesi:</span>
                            <span class="text-[10px] text-slate-400" x-show="tanggal === todayStr">Slot yang telah lewat otomatis didisable</span>
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

                    <div class="md:col-span-2">
                        <label class="block text-slate-700 font-bold mb-2">Alamat Lokasi Pemotretan / Venue <span class="text-rose-500">*</span></label>
                        <input type="text" name="lokasi" required placeholder="Nama Gedung / Hotel / Studio / Alamat Lengkap"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-slate-700 font-bold mb-2">Kebutuhan Khusus / Catatan Tema (Opsional)</label>
                        <textarea name="kebutuhan_khusus" rows="3" placeholder="Misal: Tema Adat Minang, Butuh cetakan bingkai ukuran 40x60"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none transition-colors"></textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" 
                    :disabled="isPastTime(waktu)"
                    class="w-full py-4 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center justify-center gap-2"
                    :class="isPastTime(waktu) ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'bg-slate-900 hover:bg-slate-800 text-white shadow-slate-900/20'">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span x-text="isPastTime(waktu) ? 'Pilih Jam Sesi yang Belum Terlewat' : 'Kirim Booking Pemotretan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
