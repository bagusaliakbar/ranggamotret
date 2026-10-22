---
name: icon-system
description: Design system icon standard mandating Phosphor Icons (Duotone style) across all views.
trigger: always_on
---

# Design System Icon Standard — Phosphor Icons (Duotone)

Seluruh komponen UI dan tampilan Blade di proyek **RanggaMotret.id** wajib menggunakan pustaka **Phosphor Icons**:

## 1. Varian Gaya Utama
- **Duotone (Default):** `<i class="ph-duotone ph-<nama-ikon>"></i>`
  - Memberikan kedalaman visual dua nada (*duotone aesthetics*) di mana layer latar memiliki opacity 20% dan layer garis outline berbobot tajam. Sangat cocok untuk tema fotografi mewah.
- **Fill (Status Terpilih/Aktif):** `<i class="ph-fill ph-<nama-ikon>"></i>`
  - Digunakan saat tombol sedang aktif, bintang favorit terpilih di proofing, atau bookmark tersimpan.
- **Regular:** `<i class="ph ph-<nama-ikon>"></i>`
  - Digunakan untuk indikator fungsional kecil (seperti panah dropdown atau tombol tutup modal).

## 2. Larangan Inline SVG
- Hindari menuliskan tag `<svg ...>` mentah berbaris-baris dalam template Blade.
- Gunakan tag ringkas `<i class="ph-duotone ph-..."></i>` dengan kelas ukuran (`text-xs`, `text-sm`, `text-lg`, `text-xl`) dan warna teks Tailwind CSS (`text-slate-900`, `text-emerald-600`, dll).

## 3. Kamus Pemetaan Ikon Populer

| Kategori | Ikon Phosphor Duotone | Penggunaan |
| :--- | :--- | :--- |
| **Fotografi** | `ph-duotone ph-camera` | Layanan foto, menu booking |
| | `ph-duotone ph-aperture` | Exposure, shutter speed, setting kamera |
| | `ph-duotone ph-image` / `ph-images` | Galeri portofolio, proofing |
| | `ph-duotone ph-sparkle` / `ph-magic-wand` | Retouching & color grading |
| **Jadwal & Lokasi** | `ph-duotone ph-calendar-blank` / `ph-calendar-check` | Tanggal sesi, kalender |
| | `ph-duotone ph-clock` | Jam & durasi pemotretan |
| | `ph-duotone ph-map-pin` | Lokasi venue acara |
| **Akademi & LMS** | `ph-duotone ph-graduation-cap` | Kursus akademi |
| | `ph-duotone ph-book-open` | Materi bacaan modul |
| | `ph-duotone ph-play-circle` | Materi video pembelajaran |
| | `ph-duotone ph-list-checks` | Kuis evaluasi kompetensi |
| | `ph-duotone ph-pen-nib` / `ph-pencil-simple` | Tugas praktik siswa |
| | `ph-duotone ph-certificate` | Sertifikat kelulusan digital |
| **Transaksi & Checkout**| `ph-duotone ph-receipt` | Rincian tagihan, invoice |
| | `ph-duotone ph-credit-card` | Pembayaran Midtrans Snap |
| | `ph-duotone ph-bank` | Transfer bank manual |
| | `ph-duotone ph-qrcode` | Pembayaran QRIS |
| | `ph-duotone ph-wallet` | Saldo & pendapatan |
| **Aksi & Navigasi** | `ph-duotone ph-plus` | Buat baru, tambah item |
| | `ph-duotone ph-arrow-left` / `ph-arrow-right` | Navigasi halaman, prev/next |
| | `ph-duotone ph-paper-plane-tilt` | Kirim formulir, submit booking |
| | `ph-duotone ph-download-simple` | Unduh foto / materi PDF |
| | `ph-duotone ph-upload-simple` | Unggah bukti transfer / tugas |
| | `ph-duotone ph-copy` | Salin rekening / link |
| **Komunikasi** | `ph-duotone ph-whatsapp-logo` | Bantuan chat WhatsApp |
| | `ph-duotone ph-chats-circle` | Diskusi konsultasi konsep |
| | `ph-duotone ph-envelope` | Email notifikasi |
