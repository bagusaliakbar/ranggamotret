@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran — RanggaMotret.id')

@push('head')
    @if(!empty($midtransSnapUrl) && !empty($midtransClientKey))
        <script type="text/javascript" src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    @endif
@endpush

@php
    $totalHarga = $booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0);
    $kodeTagihan = $booking ? $booking->nomor_booking : ('KURSUS-' . ($kursus->id ?? 'INV'));
    $namaItem = $booking ? ($booking->layanan->nama ?? 'Sesi Pemotretan') : ($kursus->nama ?? 'Akses Kursus Akademi');
    $subJudulItem = $booking ? ('Paket ' . ($booking->paket->nama_paket ?? 'Fotografi')) : 'Akses Penuh Materi & Sertifikat';
    $thumbnailItem = $booking ? ($booking->layanan->thumbnail ?? null) : ($kursus->thumbnail ?? null);
    $isSandbox = config('midtrans.is_production') == false;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12" 
     x-data="{ 
        paymentMethod: 'midtrans', 
        isPaying: false,
        copiedBCA: false,
        copiedMandiri: false,
        previewUrl: null,
        fileName: '',
        fileSize: '',
        copyToClipboard(text, type) {
            navigator.clipboard.writeText(text);
            if (type === 'bca') {
                this.copiedBCA = true;
                setTimeout(() => this.copiedBCA = false, 2500);
            } else if (type === 'mandiri') {
                this.copiedMandiri = true;
                setTimeout(() => this.copiedMandiri = false, 2500);
            }
        },
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                if (file.type.startsWith('image/')) {
                    this.previewUrl = URL.createObjectURL(file);
                } else {
                    this.previewUrl = null;
                }
            }
        },
        resetFile() {
            this.fileName = '';
            this.fileSize = '';
            this.previewUrl = null;
            const input = document.getElementById('bukti_pembayaran_file');
            if (input) input.value = '';
        }
     }">

    <!-- Page Header -->
    <div class="mb-8 border-b border-slate-200/80 pb-6">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Selesaikan Pembayaran</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Pilih metode pembayaran instan otomatis atau transfer manual ke rekening resmi studio.</p>
    </div>

    <!-- Main 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
        
        <!-- Left Column: Payment Options (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div>
                <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="ph-duotone ph-wallet text-emerald-600 text-xl"></i>
                    <span>Pilih Metode Pembayaran</span>
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Semua transaksi diproses dalam mata uang Rupiah (IDR).</p>
            </div>

            <!-- OPTION 1: MIDTRANS SNAP (OTOMATIS & INSTAN) -->
            <div class="rounded-3xl border-2 transition-all duration-300 bg-white overflow-hidden cursor-pointer"
                 :class="paymentMethod === 'midtrans' 
                    ? 'border-slate-900 shadow-xl shadow-slate-900/10 ring-4 ring-slate-900/5' 
                    : 'border-slate-200/90 hover:border-slate-300 shadow-sm opacity-90'"
                 @click="paymentMethod = 'midtrans'">
                
                <!-- Card Header -->
                <div class="p-5 sm:p-6 flex items-start gap-4">
                    <!-- Custom Radio Indicator -->
                    <div class="mt-1 flex items-center justify-center w-6 h-6 rounded-full border-2 transition-all shrink-0"
                         :class="paymentMethod === 'midtrans' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white'">
                        <div class="w-2.5 h-2.5 rounded-full bg-white" x-show="paymentMethod === 'midtrans'"></div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-900 text-base sm:text-lg">Pembayaran Otomatis Instan</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wide uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="ph-duotone ph-lightning text-xs text-emerald-600"></i> Rekomendasi Instan
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-relaxed">
                            Verifikasi otomatis 24/7 tanpa perlu unggah struk manual. Akses kursus atau status reservasi Anda langsung aktif seketika.
                        </p>

                        <!-- Available Channels Badges -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-slate-700 text-[11px] font-bold">
                                <i class="ph-duotone ph-qrcode text-emerald-600 text-sm"></i> QRIS (Semua E-Wallet)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-slate-700 text-[11px] font-bold">
                                <i class="ph-duotone ph-bank text-indigo-600 text-sm"></i> Virtual Account Bank
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-slate-700 text-[11px] font-bold">
                                <i class="ph-duotone ph-credit-card text-sky-600 text-sm"></i> Kartu Kredit / Debit
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-slate-700 text-[11px] font-bold">
                                <i class="ph-duotone ph-device-mobile text-amber-600 text-sm"></i> GoPay & ShopeePay
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Midtrans Action Section (Visible when active) -->
                <div x-show="paymentMethod === 'midtrans'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-5 sm:px-6 pb-6 pt-2 border-t border-slate-100 bg-slate-50/60 space-y-4"
                     @click.stop>
                    
                    @if(!empty($snapToken))
                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-emerald-950 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0 mt-0.5">
                                <i class="ph-duotone ph-shield-check"></i>
                            </div>
                            <div class="space-y-0.5 text-xs">
                                <div class="font-extrabold text-slate-900">Metode Pembayaran Instan Terpilih</div>
                                <p class="text-emerald-900 text-[11px] font-medium leading-relaxed">
                                    Silakan klik tombol <strong>Bayar Sekarang (Instan)</strong> pada Ringkasan Tagihan di sebelah kanan untuk membuka jendela pembayaran QRIS, Virtual Account, atau Kartu Kredit.
                                </p>
                                <p class="text-[10px] text-slate-500 font-medium pt-1 flex items-center gap-1">
                                    <i class="ph-duotone ph-lock-key text-emerald-600"></i>
                                    <span>Terenkripsi 256-bit SSL berstandar Bank Indonesia & PCI-DSS Midtrans Gateway.</span>
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Fallback / Sandbox Token Alert -->
                        <div class="p-5 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                    <i class="ph-duotone ph-warning-circle text-lg"></i>
                                </div>
                                <div class="space-y-1 text-xs">
                                    <div class="font-extrabold text-slate-900">Koneksi Midtrans Gateway Sedang Disiapkan</div>
                                    <p class="text-amber-800 text-[11px] leading-relaxed">
                                        {{ $snapError ?? 'Token transaksi instan belum tersedia. Anda dapat beralih ke metode Transfer Bank Manual di bawah ini.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2 flex flex-wrap gap-2 border-t border-amber-200/60">
                                <button type="button" @click="paymentMethod = 'manual'" 
                                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-sm transition-all">
                                    <i class="ph-duotone ph-bank text-sm"></i>
                                    <span>Pilih Transfer Bank Manual</span>
                                </button>

                                @if($booking && $isSandbox)
                                    <a href="{{ route('payments.finish', ['order_id' => $booking->nomor_booking, 'transaction_status' => 'settlement']) }}" 
                                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-sm transition-all">
                                        <i class="ph-duotone ph-test-tube text-sm"></i>
                                        <span>Simulasi Bayar Sukses (Sandbox)</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- OPTION 2: TRANSFER BANK MANUAL & UPLOAD STRUK -->
            <div class="rounded-3xl border-2 transition-all duration-300 bg-white overflow-hidden cursor-pointer"
                 :class="paymentMethod === 'manual' 
                    ? 'border-slate-900 shadow-xl shadow-slate-900/10 ring-4 ring-slate-900/5' 
                    : 'border-slate-200/90 hover:border-slate-300 shadow-sm opacity-90'"
                 @click="paymentMethod = 'manual'">
                
                <!-- Card Header -->
                <div class="p-5 sm:p-6 flex items-start gap-4">
                    <!-- Custom Radio Indicator -->
                    <div class="mt-1 flex items-center justify-center w-6 h-6 rounded-full border-2 transition-all shrink-0"
                         :class="paymentMethod === 'manual' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white'">
                        <div class="w-2.5 h-2.5 rounded-full bg-white" x-show="paymentMethod === 'manual'"></div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="font-black text-slate-900 text-base sm:text-lg">Transfer Bank Manual & Unggah Struk</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wide uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="ph-duotone ph-clock text-xs text-slate-500"></i> Verifikasi Tim Admin
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-relaxed">
                            Kirim langsung ke rekening bank studio RanggaMotret dan lampirkan foto struk bukti pembayaran untuk validasi cepat.
                        </p>
                    </div>
                </div>

                <!-- Manual Transfer Content Section (Visible when active) -->
                <div x-show="paymentMethod === 'manual'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-5 sm:px-6 pb-7 pt-4 border-t border-slate-100 bg-slate-50/60 space-y-6"
                     @click.stop>
                    
                    <!-- 3-Step Guide Banner -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-start gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">1</span>
                            <div>
                                <h4 class="text-xs font-black text-slate-900">Salin Rekening</h4>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Pilih salah satu nomor rekening resmi studio di bawah.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-start gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                            <div>
                                <h4 class="text-xs font-black text-slate-900">Transfer Nominal Pas</h4>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Kirim tepat Rp {{ number_format($totalHarga, 0, ',', '.') }} via m-Banking/ATM.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-start gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">3</span>
                            <div>
                                <h4 class="text-xs font-black text-slate-900">Unggah Bukti</h4>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Lampirkan struk transfer agar segera disetujui tim.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Luxury Bank Account Cards with 1-Click Copy -->
                    <div class="space-y-3">
                        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="ph-duotone ph-bank text-slate-600 text-sm"></i>
                            <span>Rekening Resmi RanggaMotret Studio:</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- BCA Card -->
                            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-slate-400 transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-black text-[11px] flex items-center justify-center shadow-xs">
                                            BCA
                                        </div>
                                        <span class="text-xs font-extrabold text-slate-800">Bank Central Asia</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">M-Banking / ATM</span>
                                </div>

                                <div class="space-y-1">
                                    <span class="font-mono font-black text-slate-900 text-xl tracking-wider block">8830192833</span>
                                    <p class="text-[11px] text-slate-500 font-medium">Atas Nama: <strong class="text-slate-700">Rangga Motret Studio</strong></p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end">
                                    <button type="button" @click="copyToClipboard('8830192833', 'bca')"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-xs"
                                            :class="copiedBCA 
                                                ? 'bg-emerald-600 text-white border border-emerald-600 ring-2 ring-emerald-500/20' 
                                                : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200'">
                                        <i class="ph-duotone text-sm" :class="copiedBCA ? 'ph-check-circle text-white' : 'ph-copy text-slate-600'"></i>
                                        <span x-text="copiedBCA ? 'Berhasil Disalin!' : 'Salin Nomor'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Mandiri Card -->
                            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-slate-400 transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-amber-500 text-white font-black text-[11px] flex items-center justify-center shadow-xs">
                                            BMRI
                                        </div>
                                        <span class="text-xs font-extrabold text-slate-800">Bank Mandiri</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Livin' / ATM</span>
                                </div>

                                <div class="space-y-1">
                                    <span class="font-mono font-black text-slate-900 text-xl tracking-wider block">137009988112</span>
                                    <p class="text-[11px] text-slate-500 font-medium">Atas Nama: <strong class="text-slate-700">Rangga Motret Studio</strong></p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end">
                                    <button type="button" @click="copyToClipboard('137009988112', 'mandiri')"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-xs"
                                            :class="copiedMandiri 
                                                ? 'bg-emerald-600 text-white border border-emerald-600 ring-2 ring-emerald-500/20' 
                                                : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200'">
                                        <i class="ph-duotone text-sm" :class="copiedMandiri ? 'ph-check-circle text-white' : 'ph-copy text-slate-600'"></i>
                                        <span x-text="copiedMandiri ? 'Berhasil Disalin!' : 'Salin Nomor'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="space-y-5 pt-2">
                        @csrf

                        @if($booking)
                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        @endif

                        @if($kursus)
                            <input type="hidden" name="kursus_id" value="{{ $kursus->id }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jumlah Nominal Ditransfer (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="jumlah" value="{{ old('jumlah', round($totalHarga)) }}" required
                                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 font-black text-sm focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 focus:outline-none transition-all shadow-xs">
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Pastikan nominal transfer sesuai tagihan untuk kelancaran verifikasi.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Rekening Tujuan Transfer <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="metode_pembayaran" required
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 font-bold text-xs focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 focus:outline-none transition-all shadow-xs appearance-none">
                                        <option value="Transfer Bank BCA">Bank BCA — 8830192833 (Rangga Motret)</option>
                                        <option value="Transfer Bank Mandiri">Bank Mandiri — 137009988112 (Rangga Motret)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-500">
                                        <i class="ph ph-caret-down text-xs"></i>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Pilih rekening yang Anda gunakan saat mentransfer.</span>
                            </div>
                        </div>

                        <!-- Drag & Drop / Interactive Upload Dropzone -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Foto Struk Bukti Transfer <span class="text-rose-500">*</span>
                            </label>
                            
                            <div class="rounded-2xl border-2 border-dashed transition-all relative overflow-hidden"
                                 :class="fileName ? 'border-emerald-500 bg-emerald-50/20' : 'border-slate-300 hover:border-slate-800 bg-white hover:bg-slate-50/50'">
                                
                                <input type="file" id="bukti_pembayaran_file" name="bukti_pembayaran" required accept="image/*,application/pdf"
                                       @change="handleFileSelect($event)"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                <!-- Empty Dropzone State -->
                                <div x-show="!fileName" class="p-6 sm:p-8 text-center space-y-3 pointer-events-none">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 mx-auto flex items-center justify-center text-2xl shadow-xs">
                                        <i class="ph-duotone ph-cloud-arrow-up text-slate-800"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-extrabold text-slate-900">
                                            Klik untuk pilih berkas <span class="text-slate-400 font-medium">atau seret ke area ini</span>
                                        </p>
                                        <p class="text-[11px] text-slate-400 font-medium mt-1">
                                            Format file yang didukung: JPG, JPEG, PNG, atau PDF (Ukuran maksimal 5MB)
                                        </p>
                                    </div>
                                </div>

                                <!-- Selected File Preview State -->
                                <div x-show="fileName" class="p-4 sm:p-5 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <template x-if="previewUrl">
                                            <img :src="previewUrl" alt="Preview Bukti" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm shrink-0">
                                        </template>
                                        <template x-if="!previewUrl">
                                            <div class="w-14 h-14 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-2xl border border-slate-200 shrink-0">
                                                <i class="ph-duotone ph-file-text text-slate-700"></i>
                                            </div>
                                        </template>

                                        <div class="min-w-0">
                                            <div class="text-xs font-black text-slate-900 truncate" x-text="fileName"></div>
                                            <div class="text-[11px] text-slate-500 font-medium" x-text="fileSize"></div>
                                            <div class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 mt-0.5">
                                                <i class="ph-duotone ph-check-circle"></i> Berkas siap diunggah
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" @click.stop="resetFile()"
                                            class="z-20 p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all text-sm shrink-0"
                                            title="Ganti Berkas">
                                        <i class="ph-duotone ph-trash text-base"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Field -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Catatan Pengirim (Opsional)
                            </label>
                            <input type="text" name="catatan" placeholder="Contoh: Transfer atas nama Budi Santoso / Bank BCA..."
                                   class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-medium focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 focus:outline-none transition-all shadow-xs">
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-4 px-6 rounded-2xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs sm:text-sm tracking-wider uppercase shadow-xl shadow-slate-900/15 transition-all duration-200 flex items-center justify-center gap-2 hover:scale-[1.008] active:scale-[0.98]">
                                <i class="ph-duotone ph-paper-plane-tilt text-emerald-400 text-lg"></i>
                                <span>Kirim Bukti Pembayaran Untuk Diverifikasi</span>
                            </button>
                            <p class="text-[10px] text-slate-400 text-center mt-2.5 font-medium">
                                Tim RanggaMotret akan memeriksa dan mengonfirmasi bukti bayar dalam kurun waktu 1x24 jam kerja.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Sticky Order Summary & Trust Card (5 Cols) -->
        <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
            
            <!-- Summary Card -->
            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-xl shadow-slate-900/5 p-6 sm:p-7 space-y-6">
                <!-- Card Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            <i class="ph-duotone ph-receipt text-emerald-400"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-sm text-slate-900">Ringkasan Tagihan</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Rincian pesanan resmi Anda</p>
                        </div>
                    </div>

                    <span class="font-mono text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200/70">
                        #{{ $kodeTagihan }}
                    </span>
                </div>

                <!-- Product Preview Card -->
                <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-slate-200 shrink-0 border border-slate-200/90 relative">
                        @if($thumbnailItem)
                            <img src="{{ asset('storage/' . $thumbnailItem) }}" alt="{{ $namaItem }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 text-white">
                                <i class="ph-duotone {{ $booking ? 'ph-camera' : 'ph-graduation-cap' }} text-2xl text-emerald-400"></i>
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1 space-y-1">
                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-white border border-slate-200 text-slate-700">
                            {{ $booking ? 'Sesi Pemotretan Studio' : 'Kursus Akademi Online' }}
                        </span>
                        <h4 class="font-black text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2">
                            {{ $namaItem }}
                        </h4>
                        <p class="text-[11px] text-slate-500 font-medium truncate">
                            {{ $subJudulItem }}
                        </p>
                    </div>
                </div>

                <!-- Booking Metadata Details (if booking) -->
                @if($booking)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/60 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="ph-duotone ph-calendar-blank text-slate-400"></i> Tanggal Sesi
                            </span>
                            <span class="font-bold text-slate-900">
                                {{ $booking->tanggal ? \Carbon\Carbon::parse($booking->tanggal)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="ph-duotone ph-clock text-slate-400"></i> Waktu / Sesi
                            </span>
                            <span class="font-bold text-slate-900">
                                {{ $booking->waktu ?? '-' }} WIB
                            </span>
                        </div>
                        @if($booking->lokasi)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="ph-duotone ph-map-pin text-slate-400"></i> Lokasi Studio
                                </span>
                                <span class="font-bold text-slate-900 truncate max-w-[150px]">
                                    {{ $booking->lokasi }}
                                </span>
                            </div>
                        @endif
                    </div>
                @elseif($kursus)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/60 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="ph-duotone ph-chart-bar text-slate-400"></i> Tingkat Kursus
                            </span>
                            <span class="font-bold text-slate-900">
                                {{ ucfirst($kursus->level ?? 'Semua Tingkat') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="ph-duotone ph-clock text-slate-400"></i> Durasi Pembelajaran
                            </span>
                            <span class="font-bold text-slate-900">
                                {{ $kursus->durasi ?? 'Akses Fleksibel' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="ph-duotone ph-certificate text-slate-400"></i> Sertifikat
                            </span>
                            <span class="font-bold text-emerald-600">
                                Termasuk Resmi Digital
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Pricing Breakdown -->
                <div class="space-y-3 pt-3 border-t border-slate-100 text-xs">
                    <div class="flex justify-between items-center text-slate-500 font-medium">
                        <span>Biaya Program / Layanan</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-500 font-medium">
                        <span>Biaya Penanganan Platform</span>
                        <span class="font-bold text-emerald-600">Rp 0 (Gratis)</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-500 font-medium">
                        <span>Pajak & Layanan (PPN)</span>
                        <span class="font-bold text-emerald-600">Termasuk</span>
                    </div>

                    <div class="pt-4 border-t-2 border-dashed border-slate-200 flex justify-between items-baseline">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 block">Total Tagihan</span>
                            <span class="text-[10px] text-slate-400 font-medium">Semua biaya sudah termasuk</span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight block">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Instant Action Trigger Helper inside Summary (Desktop quick-pay button) -->
                <div class="pt-2">
                    <template x-if="paymentMethod === 'midtrans'">
                        @if(!empty($snapToken))
                            <button type="button" id="pay-button" @click="payWithSnap()" :disabled="isPaying"
                                    class="w-full py-4 px-5 rounded-2xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider shadow-xl shadow-slate-900/15 transition-all flex items-center justify-center gap-2.5 hover:scale-[1.01] active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                                <template x-if="!isPaying">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-duotone ph-lightning text-emerald-400 text-lg"></i>
                                        <span>Bayar Sekarang (Instan)</span>
                                        <i class="ph-duotone ph-arrow-right text-slate-400 text-sm"></i>
                                    </div>
                                </template>
                                <template x-if="isPaying">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-duotone ph-spinner ph-spin text-base"></i>
                                        <span>Menghubungkan ke Gateway...</span>
                                    </div>
                                </template>
                            </button>
                        @endif
                    </template>
                    <template x-if="paymentMethod === 'manual'">
                        <div class="p-3 rounded-xl bg-slate-100 text-center text-[11px] font-semibold text-slate-600">
                            Lengkapi form transfer manual di sebelah kiri untuk menyelesaikan.
                        </div>
                    </template>
                </div>
            </div>

            <!-- Trust & Assurance Badges Card -->
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 space-y-4 shadow-sm">
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="ph-duotone ph-shield-check text-emerald-600 text-base"></i>
                    <span>Jaminan Keamanan & Bantuan</span>
                </h4>

                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="ph-duotone ph-lock-key text-sm"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 block">Enkripsi Tingkat Perbankan</span>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Seluruh data pembayaran terlindungi sertifikasi SSL 256-bit standar Bank Indonesia.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="ph-duotone ph-sparkle text-sm"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 block">Garansi Kepuasan Studio</span>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Layanan profesional terstandar dengan dukungan fotografer berpengalaman RanggaMotret.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 pt-2 border-t border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="ph-duotone ph-whatsapp-logo text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-slate-800 block">Perlu Bantuan Pembayaran?</span>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Hubungi admin resmi studio melalui WhatsApp jika ada kendala transaksi.</p>
                            <a href="https://wa.me/628123456789?text={{ urlencode('Halo Studio RanggaMotret, saya ingin konfirmasi pembayaran untuk tagihan #' . $kodeTagihan . ' sebesar Rp ' . number_format($totalHarga, 0, ',', '.')) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline mt-1.5">
                                <i class="ph-bold ph-arrow-up-right text-xs"></i>
                                <span>Chat WhatsApp Admin Studio</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function triggerSnapPay(snapToken) {
        if (!window.snap) {
            alert('SDK Pembayaran Midtrans belum siap. Mohon refresh halaman dan coba kembali.');
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment Success:', result);
                window.location.href = "{{ route('payments.finish') }}?order_id=" + (result.order_id || '{{ $kodeTagihan }}') + "&transaction_status=settlement";
            },
            onPending: function(result) {
                console.log('Payment Pending:', result);
                window.location.href = "{{ route('payments.finish') }}?order_id=" + (result.order_id || '{{ $kodeTagihan }}') + "&transaction_status=pending";
            },
            onError: function(result) {
                console.error('Payment Error:', result);
                alert('Pembayaran gagal atau dibatalkan.');
            },
            onClose: function() {
                console.log('Customer closed the payment popup');
            }
        });
    }

    function payWithSnap() {
        const snapToken = '{{ $snapToken ?? "" }}';
        if (!snapToken) {
            alert('Token transaksi tidak valid.');
            return;
        }

        if (typeof window.snap !== 'undefined') {
            triggerSnapPay(snapToken);
            return;
        }

        // Fallback dynamic script loader
        const snapUrl = "{{ $midtransSnapUrl }}";
        const clientKey = "{{ $midtransClientKey }}";
        
        let existingScript = document.querySelector('script[src="' + snapUrl + '"]');
        if (!existingScript) {
            let script = document.createElement('script');
            script.src = snapUrl;
            script.setAttribute('data-client-key', clientKey);
            script.onload = function() {
                if (typeof window.snap !== 'undefined') {
                    triggerSnapPay(snapToken);
                }
            };
            script.onerror = function() {
                alert('Gagal memuat modul pembayaran instan. Silakan gunakan metode transfer manual.');
            };
            document.body.appendChild(script);
        } else {
            let attempts = 0;
            let checkInterval = setInterval(function() {
                attempts++;
                if (typeof window.snap !== 'undefined') {
                    clearInterval(checkInterval);
                    triggerSnapPay(snapToken);
                } else if (attempts > 15) {
                    clearInterval(checkInterval);
                    alert('SDK Midtrans memerlukan waktu lebih lama. Silakan segarkan halaman dan coba lagi.');
                }
            }, 300);
        }
    }
</script>
@endpush
@endsection
