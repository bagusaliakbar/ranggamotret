@extends('layouts.app')

@section('title', 'FAQ — RanggaMotret.id')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Pusat Bantuan</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900">Pertanyaan Sering Diajukan</h1>
        <p class="text-xs sm:text-sm text-slate-600">Informasi praktis seputar pemesanan sesi foto, galeri proofing, dan sertifikasi akademi.</p>
    </div>

    <div class="space-y-4 text-xs" x-data="{ activeFaq: 1 }">
        <div class="clean-card rounded-2xl overflow-hidden">
            <button @click="activeFaq = (activeFaq === 1 ? 0 : 1)" class="w-full text-left p-5 font-bold text-sm text-slate-900 flex justify-between items-center bg-white hover:bg-slate-50">
                <span>Bagaimana alur pemesanan sesi foto di RanggaMotret?</span>
                <i class="fa-solid" :class="activeFaq === 1 ? 'fa-chevron-up text-slate-900' : 'fa-chevron-down text-slate-400'"></i>
            </button>
            <div x-show="activeFaq === 1" class="px-5 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50">
                1. Pilih kategori layanan dan paket pada halaman Layanan.<br>
                2. Tentukan tanggal, jam, serta lokasi pemotretan.<br>
                3. Tunggu konfirmasi pesanan dari Admin.<br>
                4. Upload bukti pembayaran transfer bank / QRIS.<br>
                5. Pemotretan dilaksanakan sesuai jadwal terkonfirmasi.
            </div>
        </div>

        <div class="clean-card rounded-2xl overflow-hidden">
            <button @click="activeFaq = (activeFaq === 2 ? 0 : 2)" class="w-full text-left p-5 font-bold text-sm text-slate-900 flex justify-between items-center bg-white hover:bg-slate-50">
                <span>Apa fungsi fitur Proofing Galeri Foto?</span>
                <i class="fa-solid" :class="activeFaq === 2 ? 'fa-chevron-up text-slate-900' : 'fa-chevron-down text-slate-400'"></i>
            </button>
            <div x-show="activeFaq === 2" class="px-5 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50">
                Fitur Proofing Galeri Foto memberikan kemudahan bagi Anda untuk melihat berkas foto hasil jepretan secara online, memilih foto favorit yang ingin dicetak/diretouch, serta mengunduh foto resolusi tinggi.
            </div>
        </div>

        <div class="clean-card rounded-2xl overflow-hidden">
            <button @click="activeFaq = (activeFaq === 3 ? 0 : 3)" class="w-full text-left p-5 font-bold text-sm text-slate-900 flex justify-between items-center bg-white hover:bg-slate-50">
                <span>Kapan sertifikat kelulusan dapat diterbitkan?</span>
                <i class="fa-solid" :class="activeFaq === 3 ? 'fa-chevron-up text-slate-900' : 'fa-chevron-down text-slate-400'"></i>
            </button>
            <div x-show="activeFaq === 3" class="px-5 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50">
                Sertifikat terbit secara otomatis di dashboard siswa setelah Anda menyelesaikan seluruh materi modul, mengunggah tugas praktik, dan lulus ujian kuis dengan batas skor minimal 70.
            </div>
        </div>

        <div class="clean-card rounded-2xl overflow-hidden">
            <button @click="activeFaq = (activeFaq === 4 ? 0 : 4)" class="w-full text-left p-5 font-bold text-sm text-slate-900 flex justify-between items-center bg-white hover:bg-slate-50">
                <span>Bagaimana cara memverifikasi keabsahan dokumen Sertifikat?</span>
                <i class="fa-solid" :class="activeFaq === 4 ? 'fa-chevron-up text-slate-900' : 'fa-chevron-down text-slate-400'"></i>
            </button>
            <div x-show="activeFaq === 4" class="px-5 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3 bg-slate-50">
                Setiap sertifikat terbitan RanggaMotret dilengkapi Kode Unik dan QR Code Verifikasi. Pihak ketiga dapat melakukan scan QR Code atau memasukkan kode tersebut pada menu Verifikasi Sertifikat untuk memastikan keabsahan data penerima.
            </div>
        </div>
    </div>
</div>
@endsection
