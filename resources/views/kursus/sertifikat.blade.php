<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $sertifikat->nomor_sertifikat }} — RanggaMotret.id</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-square.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f1f5f9; 
            color: #0f172a; 
        }
        .font-serif-cert { 
            font-family: 'Cinzel', Georgia, serif; 
        }
        .cert-outer-frame {
            border: 14px solid #0f172a;
        }
        .cert-inner-frame {
            border: 2px solid #cbd5e1;
        }
        .cert-corner-accent {
            position: absolute;
            width: 32px;
            height: 32px;
            border-color: #0f172a;
        }
        
        /* Landscape Print Rules */
        @page {
            size: A4 landscape;
            margin: 0;
        }
        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { 
                display: none !important; 
            }
            .cert-card-container {
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 12mm !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="py-8 px-4 sm:px-6 lg:px-8 min-h-screen flex flex-col items-center justify-center"
      x-data="{ 
        copied: false,
        verifyUrl: '{{ route('public.certificate.verify') }}?code={{ $sertifikat->nomor_sertifikat }}',
        copyLink() {
            navigator.clipboard.writeText(this.verifyUrl);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        }
      }">

    <!-- Top Action Bar -->
    <div class="max-w-[1100px] w-full mx-auto mb-5 flex flex-wrap justify-between items-center gap-3 no-print">
        <a href="{{ route('student.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-5 py-2.5 rounded-xl border border-slate-200 shadow-sm transition-all flex items-center gap-2 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali Ke Dashboard
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Print / PDF -->
            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Authentic Landscape Certificate Document (A4 Ratio ~ 1.414) -->
    <div class="cert-card-container max-w-[1100px] w-full bg-white rounded-2xl cert-outer-frame shadow-2xl p-4 sm:p-6 relative overflow-hidden">
        
        <!-- Inner Certificate Border Frame with Golden Corner Accents -->
        <div class="cert-inner-frame rounded-xl p-6 sm:p-8 lg:p-10 relative flex flex-col justify-between min-h-[580px] lg:min-h-[620px] bg-white">
            
            <!-- Corner Decorative Elements -->
            <div class="cert-corner-accent top-2 left-2 border-t-2 border-l-2"></div>
            <div class="cert-corner-accent top-2 right-2 border-t-2 border-r-2"></div>
            <div class="cert-corner-accent bottom-2 left-2 border-b-2 border-l-2"></div>
            <div class="cert-corner-accent bottom-2 right-2 border-b-2 border-r-2"></div>

            <!-- Watermark Icon Background -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.025] pointer-events-none">
                <i class="fa-solid fa-award text-[320px] text-slate-900"></i>
            </div>

            <!-- Top Header Section -->
            <div class="text-center space-y-1.5 relative z-10">
                <div class="flex flex-col items-center justify-center mb-1">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RanggaMotret.id" class="w-14 h-14 sm:w-16 sm:h-16 object-contain drop-shadow-sm mb-2">
                    <span class="inline-flex items-center gap-1.5 text-slate-800 text-[10px] uppercase font-extrabold tracking-widest border border-slate-300 px-4 py-1 rounded-full bg-slate-50 shadow-sm">
                        <i class="fa-solid fa-certificate text-slate-900"></i> Certificate of Accomplishment
                    </span>
                </div>

                <h1 class="font-serif-cert text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-wider">
                    RANGGAMOTRET ACADEMY
                </h1>

                <p class="text-[10px] sm:text-[11px] text-slate-500 tracking-widest font-mono font-semibold uppercase">
                    NOMOR SERTIFIKAT: <span class="text-slate-900 font-bold">{{ $sertifikat->nomor_sertifikat }}</span>
                </p>
            </div>

            <!-- Recipient & Body Section (Centered & Majestic) -->
            <div class="text-center space-y-2.5 py-3 max-w-3xl mx-auto relative z-10">
                <p class="text-[11px] text-slate-500 uppercase tracking-widest font-bold">
                    Sertifikat Kelulusan Ini Diberikan Kepada:
                </p>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight underline decoration-slate-900 decoration-2 underline-offset-8">
                    {{ $sertifikat->user->name }}
                </h2>

                <p class="text-xs text-slate-600 max-w-xl mx-auto leading-relaxed pt-2 font-medium">
                    Telah berhasil menyelesaikan seluruh kurikulum pembelajaran dan lulus ujian evaluasi kuis pada kursus fotografi:
                </p>

                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                    "{{ $sertifikat->kursus->nama }}"
                </h3>
            </div>

            <!-- Bottom Verification & Signature Grid (3 Columns) -->
            <div class="grid grid-cols-3 gap-4 items-end pt-4 border-t border-slate-200 relative z-10 text-xs">
                <!-- 1. Left: QR & Verification -->
                <div class="text-left space-y-1">
                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider">Verifikasi Keaslian:</span>
                    <div class="font-mono text-slate-900 font-bold text-[11px] bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 inline-block shadow-sm">
                        <i class="fa-solid fa-qrcode mr-1 text-slate-600"></i> {{ $sertifikat->kode_verifikasi }}
                    </div>
                    <span class="block text-[9px] text-emerald-700 font-bold">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Terverifikasi Resmi di RanggaMotret.id
                    </span>
                </div>

                <!-- 2. Middle: Score Badge -->
                <div class="text-center">
                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider mb-1">Nilai Kelulusan</span>
                    <div class="inline-block px-6 py-1.5 rounded-xl bg-slate-900 text-white font-extrabold text-xl shadow-sm">
                        {{ $sertifikat->nilai_akhir }} <span class="text-xs font-normal text-slate-400">/ 100</span>
                    </div>
                </div>

                <!-- 3. Right: Date & Signatures -->
                <div class="text-right space-y-1">
                    <span class="text-[10px] text-slate-500 block font-medium">
                        Diterbitkan: <strong class="text-slate-800">{{ date('d F Y', strtotime($sertifikat->tanggal_terbit)) }}</strong>
                    </span>
                    <div class="pt-2">
                        <p class="font-extrabold text-slate-900 text-xs">{{ $sertifikat->kursus->instruktur->name ?? 'Rangga Akbar Wibawa' }}</p>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Owner RanggaMotret.id</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
