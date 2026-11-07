<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICE - {{ $invoice->nomor_invoice }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-square.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .stamp-watermark {
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            user-select: none;
            pointer-events: none;
        }
        @media print {
            .no-print { display: none !important; }
            body { background-color: #ffffff; }
            .invoice-card { border: none !important; shadow: none !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12">

    <!-- Print Action Header -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ url()->previous() }}" class="text-xs font-semibold text-gray-600 hover:text-black">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md">
            Cetak / Simpan PDF Invoice
        </button>
    </div>

    <!-- Invoice Card -->
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-3xl border border-gray-200 shadow-xl invoice-card space-y-8 relative overflow-hidden">
        
        @php
            $isPaid = strtoupper(trim($invoice->status_pembayaran ?? '')) === 'LUNAS' || strtoupper(trim($invoice->pembayaran?->status ?? '')) === 'DIBAYAR';
        @endphp

        @if($isPaid)
            <!-- Elegant Luxury PAID / LUNAS Watermark Stamp -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
                <div class="stamp-watermark transform -rotate-12 border-8 border-dashed border-emerald-600/15 rounded-3xl px-12 py-6 text-center shadow-xs">
                    <div class="text-6xl sm:text-7xl font-black text-emerald-600/15 leading-none tracking-widest">
                        LUNAS
                    </div>
                    <div class="text-xs sm:text-sm font-extrabold text-emerald-700/20 tracking-widest mt-2 border-t border-dashed border-emerald-600/20 pt-1">
                        PAID & VERIFIED • RANGGAMOTRET.ID
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Header Company & Invoice Title -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-gray-200 pb-8 gap-4 relative z-10">
            <div class="flex items-start gap-4">
                <img src="{{ asset('images/logo.png') }}" alt="RanggaMotret.id" class="w-16 h-16 rounded-2xl object-contain border border-slate-200 p-1">
                <div>
                    <h1 class="font-bold text-2xl tracking-wider text-slate-900">RANGGAMOTRET.ID</h1>
                    <p class="text-xs text-slate-500 mt-1">Layanan & Edukasi Fotografi</p>
                    <p class="text-xs text-slate-500">Perumahan RSS Sidodadi Blok A No 78, Pasirkareumbi, Subang</p>
                    <p class="text-xs text-slate-500">Email: ranggaakbarwibawa@gmail.com | WA: +62895631983766</p>
                </div>
            </div>
            <div class="sm:text-right">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-600 block">OFFICIAL INVOICE</span>
                <h2 class="font-mono text-xl font-bold text-slate-900 mt-1">{{ $invoice->nomor_invoice }}</h2>
                @if($isPaid)
                    <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-black uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>TERVERIFIKASI LUNAS</span>
                    </div>
                @else
                    <div class="mt-2 inline-block px-4 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase">
                        STATUS: {{ $invoice->status_pembayaran }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Pelanggan & Meta Info -->
        <div class="grid grid-cols-2 gap-6 text-xs">
            <div>
                <span class="text-slate-400 uppercase font-bold text-[10px] block">Diterbitkan Untuk Pelanggan:</span>
                <div class="font-bold text-slate-900 text-sm mt-1">{{ $invoice->user?->name ?? 'Pelanggan' }}</div>
                <div class="text-slate-600">{{ $invoice->user?->email ?? '-' }}</div>
                <div class="text-slate-600">{{ $invoice->user?->phone ?? '-' }}</div>
                <div class="text-slate-600">{{ $invoice->user?->address ?? '-' }}</div>
            </div>
            <div class="text-right">
                <span class="text-slate-400 uppercase font-bold text-[10px] block">Rincian Transaksi & Reservasi:</span>
                <div class="text-slate-700 mt-1">Tanggal Invoice: <strong>{{ $invoice->created_at ? date('d F Y', strtotime($invoice->created_at)) : date('d F Y') }}</strong></div>
                @php
                    $kodeReservasi = $invoice->booking?->nomor_booking ?? $invoice->reservasi?->nomor_reservasi;
                    $channel = $invoice->pembayaran?->channel_pembayaran;
                @endphp
                @if($kodeReservasi)
                    <div class="text-slate-700">Kode Reservasi: <strong class="font-mono text-slate-900">{{ $kodeReservasi }}</strong></div>
                @endif
                <div class="text-slate-700">Nomor Transaksi: <strong class="font-mono">{{ $invoice->pembayaran?->nomor_transaksi ?? '-' }}</strong></div>
                <div class="text-slate-700">
                    Metode Bayar: 
                    @if($channel)
                        <strong>{{ $channel['label'] }}</strong>
                        @if(!empty($channel['detail']))
                            <span class="text-slate-500 font-mono">({{ $channel['detail'] }})</span>
                        @endif
                        <span class="text-[11px] text-slate-400 font-medium">via Midtrans</span>
                    @else
                        <strong>{{ $invoice->pembayaran?->metode_pembayaran ?? 'Online Payment (Midtrans)' }}</strong>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-gray-200 rounded-2xl overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 font-bold uppercase text-[10px] text-slate-600 border-b border-gray-200">
                    <tr>
                        <th class="p-4">Deskripsi Item Layanan / Kursus</th>
                        <th class="p-4 text-center">Qty</th>
                        <th class="p-4 text-right">Harga Satuan</th>
                        <th class="p-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if($invoice->booking && $invoice->booking->isFotografi())
                        <tr>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $invoice->booking->layanan?->nama ?? 'Layanan Fotografi' }}</div>
                                <div class="text-[11px] text-slate-500">
                                    Paket: {{ $invoice->booking->paket?->nama_paket ?? 'Paket Foto' }} 
                                    @if($invoice->booking->tanggal)
                                        (Jadwal: {{ date('d M Y', strtotime($invoice->booking->tanggal)) }})
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-center">1</td>
                            <td class="p-4 text-right font-mono">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            <td class="p-4 text-right font-mono font-bold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @elseif($invoice->kursus || ($invoice->booking && $invoice->booking->isKursus()))
                        @php
                            $kursusItem = $invoice->kursus ?? $invoice->booking?->kursus ?? $invoice->pembayaran?->kursus;
                        @endphp
                        <tr>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">Pendaftaran Akademi: {{ $kursusItem?->nama ?? 'Kursus Fotografi Akademi' }}</div>
                                <div class="text-[11px] text-slate-500">Level: {{ $kursusItem?->level ?? 'Semua Level' }} | Akses Modul Lengkap</div>
                            </td>
                            <td class="p-4 text-center">1</td>
                            <td class="p-4 text-right font-mono">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            <td class="p-4 text-right font-mono font-bold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $invoice->booking?->layanan?->nama ?? ($invoice->pembayaran?->catatan ?? 'Layanan RanggaMotret') }}</div>
                                <div class="text-[11px] text-slate-500">{{ $invoice->booking?->paket?->nama_paket ?? 'Paket Pilihan' }}</div>
                            </td>
                            <td class="p-4 text-center">1</td>
                            <td class="p-4 text-right font-mono">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            <td class="p-4 text-right font-mono font-bold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Totals Summary -->
        <div class="flex justify-end text-xs">
            <div class="w-64 space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Diskon:</span>
                    <span class="font-mono text-emerald-600 font-semibold">- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-gray-300 font-bold text-sm text-slate-900">
                    <span>TOTAL BAYAR:</span>
                    <span class="font-mono text-base text-amber-600">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Sign Off -->
        <div class="border-t border-gray-200 pt-8 flex justify-between items-end text-xs text-slate-500">
            <div>
                <p class="font-semibold text-slate-800">Syarat & Ketentuan:</p>
                <p>Invoice ini sah dan diterbitkan otomatis oleh sistem RanggaMotret.id sebagai bukti pembayaran pelunasan resmi.</p>
            </div>
            <div class="text-center">
                <p class="mb-8">Tim Administrasi RanggaMotret</p>
                <p class="font-bold text-slate-900">( Matrik Stempel Digital )</p>
            </div>
        </div>
    </div>
</body>
</html>
