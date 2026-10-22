@extends('layouts.app')

@section('title', 'Pilihan Pembayaran — RanggaMotret.id')

@push('head')
    @if(!empty($midtransSnapUrl) && !empty($midtransClientKey))
        <script type="text/javascript" src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    @endif
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8" 
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
        }
     }">

    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-[10px] font-extrabold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-credit-card text-slate-700"></i> Checkout Pembayaran
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Selesaikan Pembayaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Selesaikan transaksi sesi pemotretan atau akses akademi secara instan atau transfer manual.</p>
        </div>

        @if($booking)
            <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn-outline px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Reservasi
            </a>
        @endif
    </div>

    <!-- Order Summary Card -->
    <div class="clean-card p-6 sm:p-7 rounded-3xl space-y-4 border border-slate-200 shadow-sm bg-white">
        <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-md">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">Rincian Tagihan Resmi</h3>
                    <p class="text-[10px] text-slate-400 font-mono">Kode Tagihan: {{ $booking ? $booking->nomor_booking : ('KURSUS-' . ($kursus->id ?? 'INV')) }}</p>
                </div>
            </div>

            @if(config('midtrans.is_production') == false)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1 shadow-sm">
                    <i class="fa-solid fa-flask"></i> Sandbox Mode (Testing)
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Layanan / Program:</span>
                <span class="font-extrabold text-slate-900 text-sm block mt-0.5">{{ $booking ? $booking->layanan->nama : ($kursus->nama ?? '-') }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Paket Sesi / Akses:</span>
                <span class="font-bold text-slate-800 text-sm block mt-0.5">{{ $booking ? ('Paket ' . $booking->paket->nama_paket) : 'Akses Penuh Selamanya' }}</span>
            </div>
            <div class="sm:text-right">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Total Tagihan:</span>
                <span class="font-black text-slate-900 text-xl block mt-0.5">Rp {{ number_format($booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Methods Options -->
    <div class="space-y-6">
        <!-- Option 1: MIDTRANS SNAP (Otomatis) -->
        <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6 border-2 transition-all bg-white"
             :class="paymentMethod === 'midtrans' ? 'border-slate-900 ring-2 ring-slate-900/10 shadow-xl' : 'border-slate-200 hover:border-slate-400'">
            
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex items-start gap-4">
                    <input type="radio" id="method_midtrans" name="pay_choice" value="midtrans" x-model="paymentMethod" class="mt-1 text-slate-900 focus:ring-slate-900 w-4 h-4">
                    <label for="method_midtrans" class="cursor-pointer">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-slate-900 text-base">Pembayaran Otomatis Instan (Payment Gateway)</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Rekomendasi Instan
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1">Bayar langsung via QRIS (semua e-wallet), Virtual Account BCA / Mandiri / BNI / BRI, GoPay, ShopeePay, atau Kartu Kredit tanpa upload bukti bayar manual.</p>
                    </label>
                </div>
            </div>

            <!-- Payment Badges / Icons -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200 flex items-center gap-1">
                    <i class="fa-solid fa-qrcode text-emerald-600"></i> QRIS (GoPay, OVO, ShopeePay)
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">BCA Virtual Account</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">Mandiri Bill</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">BNI & BRI VA</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">Kartu Kredit / Debit</span>
            </div>

            <div x-show="paymentMethod === 'midtrans'" x-transition class="pt-2">
                @if(!empty($snapToken))
                    <button type="button" id="pay-button" @click="payWithSnap()" :disabled="isPaying"
                            class="w-full btn-dark py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-slate-900/20 transition-all flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95">
                        <i class="fa-solid fa-lock" x-show="!isPaying"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isPaying"></i>
                        <span>Buka Pembayaran Instan (Rp {{ number_format($booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0), 0, ',', '.') }})</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center mt-2.5 font-medium">
                        <i class="fa-solid fa-shield-halved text-slate-600 mr-1"></i> Transaksi terenkripsi SSL 256-bit berstandar Bank Indonesia & Midtrans Payment Gateway.
                    </p>
                @else
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-3">
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 text-sm"></i>
                            <div class="space-y-1">
                                <div class="font-extrabold text-slate-900">Konfigurasi Midtrans Sandbox</div>
                                <p class="text-amber-800 text-[11px] leading-relaxed">
                                    Token transaksi sedang disiapkan. Anda dapat menggunakan metode <strong>Transfer Bank Manual</strong> atau simulasi sukses di bawah.
                                </p>
                            </div>
                        </div>
                        <div class="pt-2 flex flex-wrap gap-2 border-t border-amber-200/60">
                            <button type="button" @click="paymentMethod = 'manual'" class="btn-dark px-4 py-2 rounded-xl text-xs font-bold">
                                <i class="fa-solid fa-arrow-right mr-1"></i> Gunakan Transfer Bank Manual
                            </button>
                            @if($booking)
                                <a href="{{ route('payments.finish', ['order_id' => $booking->nomor_booking, 'transaction_status' => 'settlement']) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-vial-circle-check"></i> Simulasi Bayar Sukses (Testing)
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Option 2: MANUAL TRANSFER (Konfirmasi Struk) -->
        <div class="clean-card p-6 sm:p-8 rounded-3xl space-y-6 border transition-all bg-white"
             :class="paymentMethod === 'manual' ? 'border-slate-900 ring-2 ring-slate-900/10 shadow-xl' : 'border-slate-200 hover:border-slate-400'">
            
            <div class="flex items-start gap-4">
                <input type="radio" id="method_manual" name="pay_choice" value="manual" x-model="paymentMethod" class="mt-1 text-slate-900 focus:ring-slate-900 w-4 h-4">
                <label for="method_manual" class="cursor-pointer">
                    <span class="font-extrabold text-slate-900 text-base">Transfer Bank Manual & Upload Struk</span>
                    <p class="text-xs text-slate-500 font-medium mt-1">Transfer langsung ke rekening resmi studio dan unggah berkas struk pembayaran untuk diverifikasi admin.</p>
                </label>
            </div>

            <div x-show="paymentMethod === 'manual'" x-transition class="space-y-6 pt-4 border-t border-slate-100">
                <!-- Studio Bank Accounts with 1-Click Copy -->
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3 text-xs">
                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-slate-700"></i> Rekening Resmi RanggaMotret Studio:
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- BCA Card -->
                        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-3">
                            <div>
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Bank BCA</span>
                                <span class="font-mono font-black text-slate-900 text-lg">8830192833</span>
                                <span class="text-slate-500 text-[10px] block mt-0.5">a.n. Rangga Motret Studio</span>
                            </div>
                            <button type="button" @click="copyToClipboard('8830192833', 'bca')" 
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5 shrink-0"
                                    :class="copiedBCA ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'">
                                <i class="fa-solid" :class="copiedBCA ? 'fa-check' : 'fa-copy'"></i>
                                <span x-text="copiedBCA ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>

                        <!-- Mandiri Card -->
                        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-3">
                            <div>
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Bank Mandiri</span>
                                <span class="font-mono font-black text-slate-900 text-lg">137009988112</span>
                                <span class="text-slate-500 text-[10px] block mt-0.5">a.n. Rangga Motret Studio</span>
                            </div>
                            <button type="button" @click="copyToClipboard('137009988112', 'mandiri')" 
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5 shrink-0"
                                    :class="copiedMandiri ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'">
                                <i class="fa-solid" :class="copiedMandiri ? 'fa-check' : 'fa-copy'"></i>
                                <span x-text="copiedMandiri ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Upload Struk with Live Thumbnail Preview -->
                <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="space-y-5 text-xs">
                    @csrf

                    @if($booking)
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    @endif

                    @if($kursus)
                        <input type="hidden" name="kursus_id" value="{{ $kursus->id }}">
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1.5">Jumlah Nominal Transfer (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" name="jumlah" value="{{ old('jumlah', round($booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0))) }}" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-black focus:bg-white focus:border-slate-900 focus:outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-1.5">Pilihan Bank Pengirim <span class="text-rose-500">*</span></label>
                            <select name="metode_pembayaran" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-all">
                                <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            </select>
                        </div>
                    </div>

                    <!-- Modern Drag & Drop / File Input with Preview -->
                    <div class="space-y-2">
                        <label class="block text-slate-700 font-bold mb-1.5">Unggah Bukti Struk Transfer (JPG/PNG/PDF max 5MB) <span class="text-rose-500">*</span></label>
                        
                        <div class="border-2 border-dashed border-slate-300 hover:border-slate-900 rounded-2xl p-6 text-center bg-slate-50 hover:bg-white transition-all cursor-pointer relative">
                            <input type="file" name="bukti_pembayaran" required accept="image/*,application/pdf"
                                   @change="handleFileSelect($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <template x-if="!fileName">
                                <div class="space-y-2 pointer-events-none">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center text-lg mx-auto shadow-sm">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <div class="font-extrabold text-slate-900 text-xs">Klik atau seret foto bukti struk transfer ke sini</div>
                                    <p class="text-[10px] text-slate-400 font-medium">Mendukung format JPG, PNG, atau dokumen PDF hingga ukuran 5MB</p>
                                </div>
                            </template>

                            <template x-if="fileName">
                                <div class="flex items-center justify-center gap-4">
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" alt="Preview Bukti" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shadow-md">
                                    </template>
                                    <div class="text-left">
                                        <div class="font-extrabold text-slate-900 text-xs truncate max-w-xs" x-text="fileName"></div>
                                        <div class="text-[10px] text-slate-500" x-text="fileSize"></div>
                                        <div class="text-[10px] text-emerald-600 font-bold mt-0.5"><i class="fa-solid fa-circle-check"></i> Siap diunggah</div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Tuliskan nama pemilik rekening pengirim atau nomor resi transfer..."
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-all leading-relaxed"></textarea>
                    </div>

                    <button type="submit" class="w-full btn-dark py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-slate-900/15 transition-all flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95">
                        <i class="fa-solid fa-paper-plane text-emerald-400"></i> Kirim Bukti Transfer Manual Untuk Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
<script>
    function triggerSnapPay(snapToken) {
        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment Success:', result);
                window.location.href = "{{ route('payments.finish') }}?order_id=" + (result.order_id || '{{ $booking->nomor_booking ?? "" }}') + "&transaction_status=settlement";
            },
            onPending: function(result) {
                console.log('Payment Pending:', result);
                window.location.href = "{{ route('payments.finish') }}?order_id=" + (result.order_id || '{{ $booking->nomor_booking ?? "" }}') + "&transaction_status=pending";
            },
            onError: function(result) {
                console.error('Payment Error:', result);
                alert('Pembayaran gagal atau dibatalkan.');
            },
            onClose: function() {
                console.log('Customer closed the popup without finishing the payment');
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

        // Dynamically load Snap if not already loaded
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
                alert('Gagal memuat sistem pembayaran Midtrans. Mohon periksa koneksi internet Anda atau gunakan metode transfer manual.');
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
                    alert('SDK Midtrans membutuhkan waktu lebih lama untuk memuat. Silakan refresh halaman dan coba kembali.');
                }
            }, 300);
        }
    }
</script>
@endpush
@endsection
