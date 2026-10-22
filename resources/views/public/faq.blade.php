@extends('layouts.app')

@section('title', 'Pusat Bantuan & FAQ — RanggaMotret.id')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12"
     x-data="{
        search: '',
        activeCategory: 'all',
        activeFaq: 1,
        faqs: [
            {
                id: 1,
                category: 'fotografi',
                q: 'Bagaimana alur lengkap pemesanan sesi foto di RanggaMotret?',
                a: '1. Pilih jenis layanan dan paket yang diinginkan melalui menu Layanan.<br>2. Tentukan tanggal, jam, dan lokasi pemotretan pada formulir booking.<br>3. Konfirmasi pesanan dan selesaikan pembayaran DP/Lunas via Midtrans otomatis atau Transfer Bank.<br>4. Tim fotografer kami akan hadir di venue sesuai jadwal terkonfirmasi.'
            },
            {
                id: 2,
                category: 'proofing',
                q: 'Apa fungsi fitur Proofing Galeri Foto dan bagaimana cara kerjanya?',
                a: 'Fitur Proofing adalah portal khusus klien untuk meninjau seluruh foto hasil jepretan pemotretan secara online. Anda dapat menandai foto bintang (favorit) untuk dipilih masuk ke dalam album fisik atau retouching lanjutan, serta mengunduh berkas resolusi tinggi langsung dari dashboard akun Anda.'
            },
            {
                id: 3,
                category: 'fotografi',
                q: 'Berapa lama waktu yang dibutuhkan hingga foto selesai di-retouch?',
                a: 'Foto mentah pilihan (preview proofing) diunggah dalam kurun waktu 48 jam setelah sesi selesai. Setelah Anda mengunci foto pilihan di galeri proofing, proses retouching warna dan kulit (*color grading & skin retouch*) membutuhkan waktu 7-14 hari kerja.'
            },
            {
                id: 4,
                category: 'akademi',
                q: 'Kapan sertifikat kelulusan kursus akademi dapat diterbitkan?',
                a: 'Sertifikat kompetensi resmi terbit otomatis di dashboard ruang belajar Anda setelah Anda menyelesaikan 100% materi modul, mengunggah tugas praktik, dan lulus kuis evaluasi dengan nilai memenuhi passing grade.'
            },
            {
                id: 5,
                category: 'akademi',
                q: 'Bagaimana cara memverifikasi keabsahan dokumen Sertifikat?',
                a: 'Setiap sertifikat kelulusan dilengkapi Nomor Seri Unik dan QR Code Verifikasi. Siapapun (termasuk klien atau institusi) dapat memindai QR code atau memasukkan kode pada menu Verifikasi Sertifikat untuk membuktikan keaslian penerima sertifikat.'
            },
            {
                id: 6,
                category: 'pembayaran',
                q: 'Metode pembayaran apa saja yang didukung oleh sistem?',
                a: 'Kami mendukung pembayaran instan otomatis melalui Midtrans Snap (QRIS untuk semua bank & e-wallet seperti GoPay/ShopeePay, Virtual Account BCA/Mandiri/BRI/BNI, serta Kartu Kredit) maupun Transfer Manual dengan unggah bukti bayar.'
            }
        ],
        matches(faq) {
            const matchesCat = this.activeCategory === 'all' || faq.category === this.activeCategory;
            const matchesSearch = !this.search || faq.q.toLowerCase().includes(this.search.toLowerCase()) || faq.a.toLowerCase().includes(this.search.toLowerCase());
            return matchesCat && matchesSearch;
        }
     }">

    <!-- Header Section -->
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-slate-900"></span> Pusat Bantuan Terpadu
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
            Pertanyaan <span class="font-serif italic font-normal text-slate-600">Sering</span> Diajukan.
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 font-medium">
            Temukan jawaban cepat seputar pemesanan sesi fotografi, galeri digital proofing, pembayaran, dan sertifikasi akademi.
        </p>

        <!-- Live Search Input -->
        <div class="relative max-w-md mx-auto pt-2">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-5 text-slate-400 text-sm"></i>
            <input type="text" x-model="search" placeholder="Cari pertanyaan... (contoh: proofing, sertifikat)"
                   class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border border-slate-200 shadow-sm text-xs font-medium text-slate-900 focus:border-slate-900 focus:outline-none transition-colors">
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="flex items-center justify-center gap-2 flex-wrap text-xs font-bold">
        <button @click="activeCategory = 'all'" 
                class="px-4 py-2 rounded-xl transition-all shadow-sm"
                :class="activeCategory === 'all' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'">
            Semua Topik
        </button>
        <button @click="activeCategory = 'fotografi'" 
                class="px-4 py-2 rounded-xl transition-all shadow-sm"
                :class="activeCategory === 'fotografi' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'">
            Sesi Fotografi & Jadwal
        </button>
        <button @click="activeCategory = 'proofing'" 
                class="px-4 py-2 rounded-xl transition-all shadow-sm"
                :class="activeCategory === 'proofing' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'">
            Galeri Proofing Foto
        </button>
        <button @click="activeCategory = 'akademi'" 
                class="px-4 py-2 rounded-xl transition-all shadow-sm"
                :class="activeCategory === 'akademi' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'">
            Akademi & Sertifikat
        </button>
        <button @click="activeCategory = 'pembayaran'" 
                class="px-4 py-2 rounded-xl transition-all shadow-sm"
                :class="activeCategory === 'pembayaran' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'">
            Metode Pembayaran
        </button>
    </div>

    <!-- FAQ Accordion List -->
    <div class="space-y-4 text-xs">
        <template x-for="faq in faqs" :key="faq.id">
            <div x-show="matches(faq)" class="clean-card rounded-2xl overflow-hidden shadow-sm border border-slate-200 transition-all">
                <button @click="activeFaq = (activeFaq === faq.id ? 0 : faq.id)" 
                        class="w-full text-left p-5 sm:p-6 font-extrabold text-sm sm:text-base text-slate-900 flex justify-between items-center gap-4 bg-white hover:bg-slate-50 transition-colors">
                    <span x-text="faq.q"></span>
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-xs">
                        <i class="fa-solid transition-transform duration-200" 
                           :class="activeFaq === faq.id ? 'fa-chevron-up text-slate-900' : 'fa-chevron-down text-slate-400'"></i>
                    </div>
                </button>
                <div x-show="activeFaq === faq.id" x-transition class="px-5 sm:px-6 pb-6 text-slate-600 font-medium leading-relaxed border-t border-slate-100 pt-4 bg-slate-50/50">
                    <div x-html="faq.a"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Still Have Questions Banner -->
    <div class="clean-card p-8 rounded-3xl bg-slate-900 text-white text-center space-y-4 shadow-xl">
        <h3 class="text-xl font-black">Masih Memiliki Pertanyaan Lain?</h3>
        <p class="text-xs text-slate-300 font-medium max-w-md mx-auto">
            Tim konsultan kami siap membantu menjawab pertanyaan teknis seputar konsep pemotretan atau silabus materi pelatihan Anda.
        </p>
        <div class="flex items-center justify-center gap-3 pt-2">
            <a href="{{ route('public.consultation') }}" class="btn-dark bg-white hover:bg-slate-100 text-slate-900 px-6 py-3 rounded-xl text-xs font-bold shadow-md">
                Ajukan Konsultasi
            </a>
            <a href="https://wa.me/628123456789" target="_blank" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-sm"></i> Chat WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection
