@extends('layouts.app')

@section('title', 'Pilihan Pembayaran - RanggaMotret.id')

@push('head')
    @if(!empty($midtransSnapUrl) && !empty($midtransClientKey))
        <script type="text/javascript" src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    @endif
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8" x-data="{ paymentMethod: 'midtrans', isPaying: false }">
    <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Pilihan Pembayaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Selesaikan pembayaran sesi pemotretan Anda secara instan atau melalui transfer manual.</p>
        </div>
        @if($booking)
            <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn-outline px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Booking
            </a>
        @endif
    </div>

    <!-- Order Summary Card -->
    <div class="clean-card p-6 rounded-3xl space-y-4 border-slate-200">
        <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">Rincian Tagihan</h3>
                    <p class="text-[10px] text-slate-400">Kode Transaksi: {{ $booking ? $booking->nomor_booking : 'Kursus' }}</p>
                </div>
            </div>
            @if(config('midtrans.is_production') == false)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                    <i class="fa-solid fa-flask"></i> Sandbox Mode (Testing)
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Layanan:</span>
                <span class="font-extrabold text-slate-900 text-sm">{{ $booking ? $booking->layanan->nama : ($kursus->nama ?? '-') }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Paket Sesi:</span>
                <span class="font-bold text-slate-800">{{ $booking ? $booking->paket->nama_paket : 'Pendaftaran Kelas' }}</span>
            </div>
            <div class="sm:text-right">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Total Tagihan:</span>
                <span class="font-extrabold text-slate-900 text-lg">Rp {{ number_format($booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Methods Tabs / Cards -->
    <div class="space-y-6">
        <!-- Option 1: MIDTRANS SNAP (Otomatis) -->
        <div class="clean-card p-8 rounded-3xl space-y-6 border-2 transition-all"
             :class="paymentMethod === 'midtrans' ? 'border-slate-900 ring-2 ring-slate-900/10 shadow-xl' : 'border-slate-200 hover:border-slate-400'">
            
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex items-start gap-4">
                    <input type="radio" id="method_midtrans" name="pay_choice" value="midtrans" x-model="paymentMethod" class="mt-1 text-slate-900 focus:ring-slate-900">
                    <label for="method_midtrans" class="cursor-pointer">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-slate-900 text-base">Pembayaran Otomatis (Instant)</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Direkomendasikan
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1">Bayar langsung via QRIS, BCA/Mandiri/BRI/BNI Virtual Account, GoPay, ShopeePay, atau Kartu Kredit tanpa perlu upload struk manual.</p>
                    </label>
                </div>
            </div>

            <!-- Payment Badges / Icons -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200 flex items-center gap-1">
                    <i class="fa-solid fa-qrcode text-emerald-600"></i> QRIS (Semua E-Wallet)
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">BCA Virtual Account</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">Mandiri Bill</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">BNI & BRI VA</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">GoPay / ShopeePay</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200">Kartu Kredit</span>
            </div>

            <div x-show="paymentMethod === 'midtrans'" x-transition class="pt-2">
                @if(!empty($snapToken))
                    <button type="button" id="pay-button" @click="payWithSnap()" :disabled="isPaying"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-extrabold text-sm shadow-xl shadow-slate-900/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock" x-show="!isPaying"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isPaying"></i>
                        <span>Bayar Sekarang (Rp {{ number_format($booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0), 0, ',', '.') }})</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center mt-2">
                        <i class="fa-solid fa-shield-halved text-slate-500 mr-1"></i> Transaksi diamankan enkripsi SSL & verifikasi instan Payment Gateway.
                    </p>
                @else
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-3">
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 text-sm"></i>
                            <div class="space-y-1">
                                <div class="font-extrabold text-slate-900">Konfigurasi Kunci API Midtrans Sandbox</div>
                                <p class="text-amber-800 text-[11px] leading-relaxed">
                                    Untuk menghubungkan pop-up Midtrans secara live ke akun Anda, masukkan <strong>Server Key</strong> & <strong>Client Key</strong> dari dashboard Midtrans (<a href="https://dashboard.sandbox.midtrans.com" target="_blank" class="underline font-bold text-amber-900 hover:text-black">dashboard.sandbox.midtrans.com</a>) ke file <code>.env</code>.
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
        <div class="clean-card p-8 rounded-3xl space-y-6 border transition-all"
             :class="paymentMethod === 'manual' ? 'border-slate-900 ring-2 ring-slate-900/10 shadow-xl' : 'border-slate-200 hover:border-slate-400'">
            
            <div class="flex items-start gap-4">
                <input type="radio" id="method_manual" name="pay_choice" value="manual" x-model="paymentMethod" class="mt-1 text-slate-900 focus:ring-slate-900">
                <label for="method_manual" class="cursor-pointer">
                    <span class="font-extrabold text-slate-900 text-base">Transfer Bank Manual & Upload Struk</span>
                    <p class="text-xs text-slate-500 font-medium mt-1">Transfer langsung ke rekening BCA / Mandiri studio kami dan unggah foto bukti transfer untuk diverifikasi admin secara manual.</p>
                </label>
            </div>

            <div x-show="paymentMethod === 'manual'" x-transition class="space-y-6 pt-4 border-t border-slate-100">
                <!-- Studio Bank Accounts -->
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3 text-xs">
                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-slate-700"></i> Rekening Resmi RanggaMotret Studio:
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-sm">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Bank BCA</span>
                            <span class="font-mono font-extrabold text-slate-900 text-base">8830-1928-33</span>
                            <span class="text-slate-500 text-[10px] block mt-0.5">a.n. Rangga Motret Studio</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-sm">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Bank Mandiri</span>
                            <span class="font-mono font-extrabold text-slate-900 text-base">137-00-998811-2</span>
                            <span class="text-slate-500 text-[10px] block mt-0.5">a.n. Rangga Motret Studio</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf

                    @if($booking)
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    @endif

                    @if($kursus)
                        <input type="hidden" name="kursus_id" value="{{ $kursus->id }}">
                    @endif

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Jumlah Nominal Transfer (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah" value="{{ old('jumlah', round($booking ? ($booking->paket->harga ?? 0) : ($kursus->harga ?? 0))) }}" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Pilihan Bank Pengirim <span class="text-rose-500">*</span></label>
                        <select name="metode_pembayaran" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-all">
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Unggah Bukti Struk Transfer (JPG/PNG/PDF max 5MB) <span class="text-rose-500">*</span></label>
                        <input type="file" name="bukti_pembayaran" required accept="image/*,application/pdf"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition-all cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Nomor resi transfer atau atas nama pengirim..."
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-all"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white py-4 rounded-2xl font-bold text-sm shadow-md transition-all">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Bukti Transfer Manual
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
