@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran — RanggaMotret.id')

@push('head')
    @if(!empty($midtransSnapUrl) && !empty($midtransClientKey))
        <script type="text/javascript" src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    @endif
@endpush

@php
    $isKursus = ($kursus !== null) || ($booking && $booking->isKursus());
    $totalHarga = $booking 
        ? ($booking->isKursus() ? ($booking->kursus?->harga ?? ($booking->pembayaran?->jumlah ?? 0)) : ($booking->paket?->harga ?? 0)) 
        : ($kursus?->harga ?? 0);
    $kodeTagihan = $booking ? $booking->nomor_booking : ('KURSUS-' . ($kursus?->id ?? 'INV'));
    $namaItem = $booking 
        ? ($booking->isKursus() ? ($booking->kursus?->nama ?? 'Kursus Akademi Fotografi') : ($booking->layanan?->nama ?? 'Sesi Pemotretan')) 
        : ($kursus?->nama ?? 'Akses Kursus Akademi');
    $subJudulItem = $booking 
        ? ($booking->isKursus() ? ('Level ' . ($booking->kursus?->level ?? 'Semua Level')) : ('Paket ' . ($booking->paket?->nama_paket ?? 'Fotografi'))) 
        : 'Akses Penuh Materi & Sertifikat';
    $thumbnailItem = $booking 
        ? ($booking->isKursus() ? ($booking->kursus?->thumbnail ?? null) : ($booking->layanan?->thumbnail ?? null)) 
        : ($kursus?->thumbnail ?? null);
    $isSandbox = config('midtrans.is_production') == false;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12" 
     x-data="{ 
        isPaying: false
     }">

    <!-- Page Header -->
    <div class="mb-8 border-b border-slate-200/80 pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-slate-700 transition-colors">Beranda</a>
            <span>/</span>
            @if($booking)
                <a href="{{ route('customer.bookings.index') }}" class="hover:text-slate-700 transition-colors">Reservasi Saya</a>
            @else
                <a href="{{ route('student.dashboard') }}" class="hover:text-slate-700 transition-colors">Kursus Saya</a>
            @endif
            <span>/</span>
            <span class="text-slate-700">Pembayaran Online</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Selesaikan Pembayaran</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Pembayaran aman, instan, dan terverifikasi otomatis 24/7 melalui Midtrans Payment Gateway.</p>
    </div>

    <!-- Main 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
        
        <!-- Left Column: Midtrans Gateway Information & Channel Badges (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- MIDTRANS SNAP GATEWAY CARD -->
            <div class="rounded-3xl border-2 border-slate-900 bg-white overflow-hidden shadow-xl shadow-slate-900/10 ring-4 ring-slate-900/5">
                
                <!-- Card Header -->
                <div class="p-6 sm:p-7 border-b border-slate-100 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 text-white">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md text-emerald-400 flex items-center justify-center text-2xl border border-white/10 shadow-sm">
                                <i class="ph-duotone ph-shield-check"></i>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">Midtrans Payment Gateway</h2>
                                <p class="text-xs text-slate-300 font-medium mt-0.5">Satu portal pembayaran untuk seluruh metode transaksi digital</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            <i class="ph-duotone ph-lightning text-sm text-emerald-400"></i> Instan & Otomatis
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 sm:p-7 space-y-6">
                    
                    <!-- Highlights Benefit Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                                <i class="ph-duotone ph-clock"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900">Verifikasi Instan</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">Transaksi terkonfirmasi otomatis seketika tanpa tunggu admin.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                                <i class="ph-duotone ph-file-x"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900">Tanpa Unggah Struk</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">Tidak perlu repot foto struk transfer manual.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base">
                                <i class="ph-duotone ph-lock-key"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-900">Keamanan Standar BI</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">Enkripsi 256-bit SSL & sertifikasi PCI-DSS Level 1.</p>
                        </div>
                    </div>

                    <!-- Pilihan Saluran Pembayaran yang Didukung -->
                    <div class="space-y-3 pt-2">
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="ph-duotone ph-wallet text-slate-600 text-sm"></i>
                            <span>Pilihan Metode Bayar di Midtrans Snap:</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <!-- QRIS -->
                            <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                                    <i class="ph-duotone ph-qrcode"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-slate-900 block">QRIS (Semua E-Wallet)</span>
                                    <p class="text-[11px] text-slate-500 font-medium truncate">GoPay, OVO, DANA, BCA, ShopeePay</p>
                                </div>
                            </div>

                            <!-- Virtual Account -->
                            <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                                    <i class="ph-duotone ph-bank"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-slate-900 block">Virtual Account (VA)</span>
                                    <p class="text-[11px] text-slate-500 font-medium truncate">BCA, Mandiri, BNI, BRI, Permata</p>
                                </div>
                            </div>

                            <!-- Kartu Kredit / Debit -->
                            <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
                                    <i class="ph-duotone ph-credit-card"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-slate-900 block">Kartu Kredit & Debit</span>
                                    <p class="text-[11px] text-slate-500 font-medium truncate">Visa, MasterCard, JCB (3D Secure)</p>
                                </div>
                            </div>

                            <!-- E-Wallet Langsung -->
                            <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                                    <i class="ph-duotone ph-device-mobile"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-slate-900 block">E-Wallet Direct</span>
                                    <p class="text-[11px] text-slate-500 font-medium truncate">GoPay, ShopeePay, AstraPay</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk Pembayaran 3 Langkah -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="ph-duotone ph-info text-slate-600 text-sm"></i>
                            <span>Cara Menyelesaikan Pembayaran:</span>
                        </h4>
                        <ol class="space-y-2 text-xs text-slate-600 font-medium list-decimal list-inside leading-relaxed">
                            <li>Klik tombol <strong class="text-slate-900">Bayar Sekarang via Midtrans</strong> di bawah atau pada Ringkasan Tagihan.</li>
                            <li>Jendela popup resmi Midtrans akan terbuka secara aman di layar Anda.</li>
                            <li>Pilih metode bayar favorit (QRIS, VA Bank, atau E-Wallet), lalu ikuti instruksi pembayaran di layar.</li>
                            <li>Setelah transfer selesai, sistem akan otomatis mendeteksi dan mengonfirmasi pesanan Anda secara langsung.</li>
                        </ol>
                    </div>

                    <!-- Action Trigger Buttons -->
                    <div class="pt-2 space-y-3">
                        @if(!empty($snapToken))
                            <button type="button" @click="payWithSnap()" :disabled="isPaying"
                                    class="w-full py-4 px-6 rounded-2xl bg-slate-900 hover:bg-black text-white font-extrabold text-sm sm:text-base tracking-wide uppercase shadow-xl shadow-slate-900/15 transition-all flex items-center justify-center gap-2.5 hover:scale-[1.01] active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                                <template x-if="!isPaying">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-duotone ph-lightning text-emerald-400 text-xl"></i>
                                        <span>Buka Jendela Bayar Midtrans</span>
                                        <i class="ph-duotone ph-arrow-right text-slate-400 text-sm"></i>
                                    </div>
                                </template>
                                <template x-if="isPaying">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-duotone ph-spinner ph-spin text-lg"></i>
                                        <span>Menghubungkan ke Gateway...</span>
                                    </div>
                                </template>
                            </button>
                        @else
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                                <div class="font-extrabold">Menyiapkan Token Midtrans...</div>
                                <p class="text-[11px] mt-1">{{ $snapError ?? 'Silakan refresh halaman ini untuk memuat ulang koneksi pembayaran instan.' }}</p>
                            </div>
                        @endif

                        @if($booking && $isSandbox)
                            <!-- Sandbox Test Simulation Button -->
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-mono">Mode Pengujian Sandbox:</span>
                                <a href="{{ route('payments.finish', ['order_id' => $booking->nomor_booking, 'transaction_status' => 'settlement']) }}" 
                                   class="px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                                    <i class="ph-duotone ph-test-tube text-sm text-emerald-600"></i>
                                    <span>Simulasi Bayar Berhasil (Sandbox)</span>
                                </a>
                            </div>
                        @endif
                    </div>

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

                <!-- Instant Action Button inside Summary -->
                <div class="pt-2">
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
                alert('Gagal memuat modul pembayaran instan. Silakan segarkan halaman.');
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
