# Dokumentasi Perancangan Database — RanggaMotret.id

Dokumen ini berisi rancangan fisik basis data (*Database Schema*) yang digunakan pada sistem informasi layanan jasa fotografi dan akademi online **RanggaMotret.id**. Struktur dirancang berbasis MySQL / MariaDB dengan dukungan arsitektur MVC (Model-View-Controller) serta integritas relasional (*referential integrity*) yang utuh.

---

## Daftar Tabel Basis Data

1. [users](#1-tabel-users) — Entitas akun pengguna (Owner & Pelanggan)
2. [customers](#2-tabel-customers) — Profil detail pelanggan
3. [layanan](#3-tabel-layanan) — Katalog layanan fotografi studio
4. [kategori_layanan](#4-tabel-kategori_layanan) — Master kategori tema layanan pemotretan
5. [paket](#5-tabel-paket) — Paket harga dan fasilitas layanan foto
6. [booking](#6-tabel-booking) — Reservasi jadwal sesi pemotretan
7. [pembayaran](#7-tabel-pembayaran) — Transaksi keuangan & integrasi Midtrans Snap
8. [invoice](#8-tabel-invoice) — Faktur tagihan resmi (pelunasan)
9. [kursus](#9-tabel-kursus) — Akademi & kursus fotografi
10. [modul](#10-tabel-modul) — Modul bab silabus kursus
11. [materi](#11-tabel-materi) — Materi pembelajaran (video/dokumen/teks)
12. [materi_progress](#12-tabel-materi_progress) — Riwayat progres belajar siswa
13. [tugas](#13-tabel-tugas) — Tugas praktik siswa
14. [tugas_submission](#14-tabel-tugas_submission) — Pengumpulan hasil tugas siswa & penilaian
15. [quiz](#15-tabel-quiz) — Kuis evaluasi pemahaman modul
16. [quiz_soal](#16-tabel-quiz_soal) — Bank soal pilihan ganda kuis
17. [quiz_attempt](#17-tabel-quiz_attempt) — Hasil riwayat pengerjaan kuis siswa
18. [enrollment](#18-tabel-enrollment) — Pendaftaran & hak akses kursus siswa
19. [sertifikat](#19-tabel-sertifikat) — Sertifikat kelulusan digital & verifikasi
20. [portofolio](#20-tabel-portofolio) — Galeri album karya fotografi
21. [portofolio_foto](#21-tabel-portofolio_foto) — Rincian multi-foto dalam album karya
22. [galeri_foto](#22-tabel-galeri_foto) — Galeri foto hasil sesi klien (proofing)
23. [konsultasi](#23-tabel-konsultasi) — Tiket konsultasi konsep foto klien
24. [testimoni](#24-tabel-testimoni) — Ulasan dan rating kepuasan pelanggan
25. [promo](#25-tabel-promo) — Kupon kode diskon promosi
26. [notifikasi](#26-tabel-notifikasi) — Notifikasi sistem real-time internal

---

### 1. Tabel: `users`
- **Nama Tabel:** `users`
- **Primary Key:** `id`
- **Foreign Key:** Tidak ada
- **Deskripsi Tabel:** Menyimpan data kredensial akun pengguna sistem, baik akun Administrator/Owner (`role: admin`) maupun akun Pelanggan/Klien/Peserta Kursus (`role: customer`).

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik akun pengguna (Primary Key, Auto Increment) |
| `name` | VARCHAR | 255 | Nama lengkap pengguna |
| `email` | VARCHAR | 255 | Alamat email unik untuk otentikasi login (Unique) |
| `email_verified_at` | TIMESTAMP | - | Waktu verifikasi email (Nullable) |
| `password` | VARCHAR | 255 | Kata sandi pengguna yang telah dienkripsi (Hash bcrypt/argon2) |
| `role` | VARCHAR | 50 | Peran akses sistem ('admin' = Owner, 'customer' = Pelanggan) |
| `status` | VARCHAR | 20 | Status akun pengguna ('active', 'inactive') |
| `phone` | VARCHAR | 20 | Nomor telepon / WhatsApp pengguna (Nullable) |
| `address` | TEXT | - | Alamat tempat tinggal / domisili pengguna (Nullable) |
| `avatar` | VARCHAR | 255 | Path atau URL berkas foto profil pengguna (Nullable) |
| `remember_token` | VARCHAR | 100 | Token penyimpanan sesi masuk otomatis (Nullable) |
| `created_at` | TIMESTAMP | - | Waktu data akun dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data akun terakhir diperbarui |

---

### 2. Tabel: `customers`
- **Nama Tabel:** `customers`
- **Primary Key:** `id`
- **Foreign Key:** `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan data pelengkap profil khusus untuk entitas pelanggan/klien pemesan sesi foto dan peserta kursus.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik record customer (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key mengacu ke `users.id` |
| `phone` | VARCHAR | 20 | Nomor kontak telepon / WhatsApp aktif (Nullable) |
| `address` | TEXT | - | Alamat domisili lengkap pelanggan (Nullable) |
| `bio` | TEXT | - | Deskripsi ringkas profil pelanggan (Nullable) |
| `created_at` | TIMESTAMP | - | Waktu data profil dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data profil terakhir diperbarui |

---

### 3. Tabel: `layanan`
- **Nama Tabel:** `layanan`
- **Primary Key:** `id`
- **Foreign Key:** Tidak ada
- **Deskripsi Tabel:** Menyimpan katalog induk jenis layanan jasa fotografi yang disediakan oleh studio RanggaMotret (misal: Wedding, Prewedding, Wisuda, Studio Portrait).

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik jenis layanan (Primary Key, Auto Increment) |
| `nama` | VARCHAR | 255 | Nama layanan fotografi |
| `kategori` | VARCHAR | 100 | Kategori layanan pemotretan |
| `deskripsi` | TEXT | - | Uraian penjelasan deskriptif layanan (Nullable) |
| `harga_mulai` | DECIMAL | 12,2 | Estimasi harga awal penawaran layanan (Rp) |
| `thumbnail` | VARCHAR | 255 | Path atau URL gambar banner sampul layanan (Nullable) |
| `status` | VARCHAR | 20 | Status ketersediaan layanan ('aktif', 'nonaktif') |
| `created_at` | TIMESTAMP | - | Waktu data layanan dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data layanan terakhir diperbarui |

---

### 4. Tabel: `kategori_layanan`
- **Nama Tabel:** `kategori_layanan`
- **Primary Key:** `id`
- **Foreign Key:** Tidak ada
- **Deskripsi Tabel:** Menyimpan daftar master kategori untuk pengelompokan layanan fotografi serta mempermudah filtering portofolio dan pemesanan.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik kategori (Primary Key, Auto Increment) |
| `nama` | VARCHAR | 100 | Nama kategori layanan (Unique) |
| `slug` | VARCHAR | 100 | Slug URL ramah SEO (Unique) |
| `icon` | VARCHAR | 50 | Nama identifier icon (Phosphor Icons / FontAwesome) |
| `deskripsi` | TEXT | - | Penjelasan singkat cakupan kategori (Nullable) |
| `status` | VARCHAR | 20 | Status operasional kategori ('aktif', 'nonaktif') |
| `created_at` | TIMESTAMP | - | Waktu data kategori dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data kategori terakhir diperbarui |

---

### 5. Tabel: `paket`
- **Nama Tabel:** `paket`
- **Primary Key:** `id`
- **Foreign Key:** `layanan_id` (mengacu ke `layanan.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan paket-paket spesifik yang ditawarkan di bawah suatu layanan fotografi, lengkap dengan rincian harga, durasi, dan fasilitas.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik paket (Primary Key, Auto Increment) |
| `layanan_id` | BIGINT (UNSIGNED) | 20 | Foreign Key mengacu ke `layanan.id` |
| `nama_paket` | VARCHAR | 255 | Nama paket penawaran (e.g. Bronze, Silver, Premium) |
| `harga` | DECIMAL | 12,2 | Tarif harga resmi paket fotografi (Rp) |
| `durasi` | VARCHAR | 50 | Estimasi alokasi waktu pemotretan (e.g. "2 Jam", "Full Day") |
| `jumlah_fotografer` | INT | 11 | Jumlah fotografer yang bertugas di lapangan |
| `jumlah_foto` | INT | 11 | Jumlah file foto jadi yang diterima klien |
| `fasilitas` | TEXT | - | Rincian fasilitas, cetak album, frame, dsb (Nullable) |
| `deskripsi` | TEXT | - | Penjelasan detail ketentuan paket (Nullable) |
| `status` | VARCHAR | 20 | Status penjualan paket ('aktif', 'nonaktif') |
| `created_at` | TIMESTAMP | - | Waktu paket didaftarkan |
| `updated_at` | TIMESTAMP | - | Waktu data paket diperbarui |

---

### 6. Tabel: `booking`
- **Nama Tabel:** `booking`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `layanan_id` (mengacu ke `layanan.id` dengan relasi `ON DELETE CASCADE`)
  - `paket_id` (mengacu ke `paket.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan rekaman transaksi pemesanan (reservasi) sesi pemotretan oleh pelanggan, meliputi tanggal, waktu, lokasi, dan status operasional.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik reservasi (Primary Key, Auto Increment) |
| `nomor_booking` | VARCHAR | 50 | Kode booking unik pemesanan (Unique, e.g. BK-20261025-001) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key pelanggan pemesan (`users.id`) |
| `layanan_id` | BIGINT (UNSIGNED) | 20 | Foreign Key layanan yang dipesan (`layanan.id`) |
| `paket_id` | BIGINT (UNSIGNED) | 20 | Foreign Key paket yang dipilih (`paket.id`) |
| `tanggal` | DATE | - | Tanggal sesi pemotretan dijadwalkan |
| `waktu` | TIME | - | Jam dimulainya sesi pemotretan |
| `lokasi` | VARCHAR | 255 | Tempat atau alamat venue pemotretan |
| `kebutuhan_khusus` | TEXT | - | Catatan permintaan khusus dari klien (Nullable) |
| `status` | VARCHAR | 50 | Status proses reservasi ('Menunggu Konfirmasi', 'Disetujui', 'Ditolak', 'Menunggu Pembayaran', 'Dibayar', 'Terjadwal', 'Selesai', 'Dibatalkan') |
| `created_at` | TIMESTAMP | - | Waktu reservasi diajukan |
| `updated_at` | TIMESTAMP | - | Waktu status reservasi diperbarui |

---

### 7. Tabel: `pembayaran`
- **Nama Tabel:** `pembayaran`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `booking_id` (mengacu ke `booking.id` dengan relasi `ON DELETE CASCADE`, Nullable)
  - `kursus_id` (mengacu ke `kursus.id` dengan relasi `ON DELETE SET NULL`, Nullable)
- **Deskripsi Tabel:** Menyimpan data transaksi finansial untuk tagihan reservasi pemotretan maupun pendaftaran kelas kursus fotografi, terintegrasi dengan Payment Gateway Midtrans.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik pembayaran (Primary Key, Auto Increment) |
| `nomor_transaksi` | VARCHAR | 50 | Nomor transaksi unik (Unique, e.g. TRX-20261025-001) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key pembayar (`users.id`) |
| `booking_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi sesi foto (`booking.id`, Nullable) |
| `kursus_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi kursus (`kursus.id`, Nullable) |
| `jumlah` | DECIMAL | 12,2 | Total nominal uang pembayaran (Rp) |
| `metode_pembayaran` | VARCHAR | 100 | Saluran bayar (Midtrans Snap, Transfer Bank, QRIS) |
| `tanggal_pembayaran` | TIMESTAMP | - | Waktu penyelesaian pembayaran (Nullable) |
| `bukti_pembayaran` | VARCHAR | 255 | Path berkas slip/bukti transfer manual (Nullable) |
| `catatan` | TEXT | - | Catatan keterangan transaksi (Nullable) |
| `status` | VARCHAR | 50 | Status pembayaran ('Belum Dibayar', 'Menunggu Verifikasi', 'Dibayar', 'Ditolak', 'Dikembalikan') |
| `snap_token` | VARCHAR | 255 | Token transaksi Midtrans Snap untuk popup checkout (Nullable) |
| `payment_type` | VARCHAR | 50 | Jenis metode bayar Midtrans (qris, bank_transfer, dll) (Nullable) |
| `payload_response` | JSON | - | Payload utuh respon callback webhook Midtrans (Nullable) |
| `created_at` | TIMESTAMP | - | Waktu data transaksi dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data transaksi terakhir diperbarui |

---

### 8. Tabel: `invoice`
- **Nama Tabel:** `invoice`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `pembayaran_id` (mengacu ke `pembayaran.id` dengan relasi `ON DELETE CASCADE`)
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `booking_id` (mengacu ke `booking.id` dengan relasi `ON DELETE CASCADE`, Nullable)
  - `kursus_id` (mengacu ke `kursus.id` dengan relasi `ON DELETE CASCADE`, Nullable)
- **Deskripsi Tabel:** Menyimpan faktur resmi digital sebagai bukti penagihan dan kwitansi pelunasan untuk keperluan pembukuan studio dan arsip pelanggan.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik faktur (Primary Key, Auto Increment) |
| `nomor_invoice` | VARCHAR | 50 | Kode seri faktur unik (Unique, e.g. INV-20261025-001) |
| `pembayaran_id` | BIGINT (UNSIGNED) | 20 | Foreign Key transaksi pelunasan (`pembayaran.id`) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key pihak pembayar (`users.id`) |
| `booking_id` | BIGINT (UNSIGNED) | 20 | Foreign Key pesanan foto terkait (`booking.id`, Nullable) |
| `kursus_id` | BIGINT (UNSIGNED) | 20 | Foreign Key pendaftaran kursus terkait (`kursus.id`, Nullable) |
| `subtotal` | DECIMAL | 12,2 | Jumlah biaya kotor sebelum potongan diskon |
| `diskon` | DECIMAL | 12,2 | Potongan harga kupon promo (Default 0) |
| `total` | DECIMAL | 12,2 | Nilai akhir tagihan yang wajib dibayarkan (Rp) |
| `status_pembayaran` | VARCHAR | 30 | Status tagihan ('LUNAS', 'PENDING') |
| `created_at` | TIMESTAMP | - | Waktu penerbitan faktur |
| `updated_at` | TIMESTAMP | - | Waktu pembaruan data faktur |

---

### 9. Tabel: `kursus`
- **Nama Tabel:** `kursus`
- **Primary Key:** `id`
- **Foreign Key:** `instruktur_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan data paket kursus fotografi pada platform LMS (Learning Management System) RanggaMotret.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik kursus (Primary Key, Auto Increment) |
| `nama` | VARCHAR | 255 | Judul program kursus fotografi |
| `deskripsi` | TEXT | - | Penjelasan silabus dan capaian kursus (Nullable) |
| `harga` | DECIMAL | 12,2 | Biaya keikutsertaan kursus (Rp) |
| `durasi` | VARCHAR | 50 | Estimasi durasi pembelajaran (e.g. "4 Minggu", "12 Jam") |
| `level` | VARCHAR | 50 | Tingkat keahlian ('Pemula', 'Menengah', 'Mahir') |
| `instruktur_id` | BIGINT (UNSIGNED) | 20 | Foreign Key pengajar/instruktur penanggung jawab (`users.id`) |
| `thumbnail` | VARCHAR | 255 | Path atau URL gambar sampul poster kursus (Nullable) |
| `status` | VARCHAR | 20 | Status ketersediaan kursus ('aktif', 'draft') |
| `created_at` | TIMESTAMP | - | Waktu kursus didaftarkan |
| `updated_at` | TIMESTAMP | - | Waktu data kursus diperbarui |

---

### 10. Tabel: `modul`
- **Nama Tabel:** `modul`
- **Primary Key:** `id`
- **Foreign Key:** `kursus_id` (mengacu ke `kursus.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan daftar modul (bab/kurikulum silabus) yang terorganisir di dalam suatu kursus fotografi.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik modul (Primary Key, Auto Increment) |
| `kursus_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi kursus induk (`kursus.id`) |
| `judul` | VARCHAR | 255 | Judul bab/modul pembelajaran |
| `urutan` | INT | 11 | Nomor urutan susunan bab silabus (Default 1) |
| `deskripsi` | TEXT | - | Ringkasan materi yang dibahas dalam bab (Nullable) |
| `created_at` | TIMESTAMP | - | Waktu modul dibuat |
| `updated_at` | TIMESTAMP | - | Waktu modul diperbarui |

---

### 11. Tabel: `materi`
- **Nama Tabel:** `materi`
- **Primary Key:** `id`
- **Foreign Key:** `modul_id` (mengacu ke `modul.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan rincian konten pembelajaran per materi di dalam modul, dapat berupa video embed, dokumen PDF unduhan, maupun teks artikel.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik materi (Primary Key, Auto Increment) |
| `modul_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi modul induk (`modul.id`) |
| `judul` | VARCHAR | 255 | Judul materi sesi pembelajaran |
| `tipe` | VARCHAR | 20 | Format materi ('teks', 'pdf', 'foto', 'video', 'link') |
| `file_path` | VARCHAR | 255 | Path berkas lampiran pendukung materi (Nullable) |
| `video_url` | VARCHAR | 255 | URL video pembelajaran YouTube / Vimeo embed (Nullable) |
| `isi` | LONGTEXT | - | Isi lengkap teks penjelasan materi (Nullable) |
| `urutan` | INT | 11 | Nomor urut pemutaran materi dalam bab (Default 1) |
| `created_at` | TIMESTAMP | - | Waktu materi ditambahkan |
| `updated_at` | TIMESTAMP | - | Waktu materi terakhir disunting |

---

### 12. Tabel: `materi_progress`
- **Nama Tabel:** `materi_progress`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `materi_id` (mengacu ke `materi.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Mencatat jejak progres belajar setiap siswa terhadap tiap-tiap materi pembelajaran untuk mengkalkulasi persentase kelulusan (0-100%).

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik data progres (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key akun siswa (`users.id`) |
| `materi_id` | BIGINT (UNSIGNED) | 20 | Foreign Key materi yang dipelajari (`materi.id`) |
| `is_completed` | TINYINT (BOOLEAN) | 1 | Status penyelesaian materi (1 = Selesai, 0 = Belum) |
| `created_at` | TIMESTAMP | - | Waktu pertama kali materi dibuka |
| `updated_at` | TIMESTAMP | - | Waktu materi ditandai telah selesai |

---

### 13. Tabel: `tugas`
- **Nama Tabel:** `tugas`
- **Primary Key:** `id`
- **Foreign Key:** `modul_id` (mengacu ke `modul.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan tugas praktik fotografi yang wajib dikerjakan oleh siswa pada suatu modul tertentu.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik tugas (Primary Key, Auto Increment) |
| `modul_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi modul (`modul.id`) |
| `judul` | VARCHAR | 255 | Judul penugasan praktik |
| `instruksi` | TEXT | - | Petunjuk detail, teknik kamera, dan kriteria tugas |
| `deadline` | DATETIME | - | Batas waktu terakhir pengumpulan tugas (Nullable) |
| `file_pendukung` | VARCHAR | 255 | Path berkas referensi/panduan penugasan (Nullable) |
| `created_at` | TIMESTAMP | - | Waktu tugas diterbitkan |
| `updated_at` | TIMESTAMP | - | Waktu instruksi tugas disunting |

---

### 14. Tabel: `tugas_submission`
- **Nama Tabel:** `tugas_submission`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `tugas_id` (mengacu ke `tugas.id` dengan relasi `ON DELETE CASCADE`)
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan berkas pengumpulan tugas hasil bidikan foto dari siswa serta nilai dan masukan evaluasi dari instruktur.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik pengumpulan tugas (Primary Key, Auto Increment) |
| `tugas_id` | BIGINT (UNSIGNED) | 20 | Foreign Key tugas yang dikumpulkan (`tugas.id`) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key siswa pengumpul (`users.id`) |
| `file_path` | VARCHAR | 255 | Path berkas karya foto tugas siswa (Nullable) |
| `catatan` | TEXT | - | Catatan penjelasan konsep foto dari siswa (Nullable) |
| `tanggal_submit` | TIMESTAMP | - | Waktu penyerahan berkas tugas |
| `nilai` | INT | 11 | Skor evaluasi tugas dari instruktur skala 0-100 (Nullable) |
| `feedback` | TEXT | - | Ulasan masukan perbaikan dari instruktur (Nullable) |
| `status` | VARCHAR | 30 | Status verifikasi tugas ('submitted', 'graded', 'revision_requested') |
| `created_at` | TIMESTAMP | - | Waktu data dicatat |
| `updated_at` | TIMESTAMP | - | Waktu pemberian nilai/umpan balik |

---

### 15. Tabel: `quiz`
- **Nama Tabel:** `quiz`
- **Primary Key:** `id`
- **Foreign Key:** `modul_id` (mengacu ke `modul.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan kuis evaluasi pemahaman teori dasar fotografi di setiap modul pembelajaran.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik kuis (Primary Key, Auto Increment) |
| `modul_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi modul (`modul.id`) |
| `judul` | VARCHAR | 255 | Judul sesi kuis evaluasi |
| `durasi_menit` | INT | 11 | Alokasi waktu pengerjaan kuis dalam menit (Default 30) |
| `passing_grade` | INT | 11 | Nilai ambang batas minimum kelulusan kuis (Default 70) |
| `created_at` | TIMESTAMP | - | Waktu kuis dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data kuis diperbarui |

---

### 16. Tabel: `quiz_soal`
- **Nama Tabel:** `quiz_soal`
- **Primary Key:** `id`
- **Foreign Key:** `quiz_id` (mengacu ke `quiz.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan butir-butir pertanyaan pilihan ganda beserta opsi pilihan jawaban dan kunci jawaban yang benar.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik butir soal (Primary Key, Auto Increment) |
| `quiz_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi kuis induk (`quiz.id`) |
| `pertanyaan` | TEXT | - | Teks naskah pertanyaan kuis |
| `pilihan_a` | VARCHAR | 255 | Pilihan teks jawaban opsi A |
| `pilihan_b` | VARCHAR | 255 | Pilihan teks jawaban opsi B |
| `pilihan_c` | VARCHAR | 255 | Pilihan teks jawaban opsi C |
| `pilihan_d` | VARCHAR | 255 | Pilihan teks jawaban opsi D |
| `jawaban_benar` | ENUM | ('A','B','C','D') | Kunci pilihan jawaban yang bernilai benar |
| `pembahasan` | TEXT | - | Penjelasan teori dari jawaban yang benar (Nullable) |
| `bobot_nilai` | INT | 11 | Nilai poin jika dijawab dengan benar (Default 10) |
| `created_at` | TIMESTAMP | - | Waktu soal dibuat |
| `updated_at` | TIMESTAMP | - | Waktu soal diperbarui |

---

### 17. Tabel: `quiz_attempt`
- **Nama Tabel:** `quiz_attempt`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `quiz_id` (mengacu ke `quiz.id` dengan relasi `ON DELETE CASCADE`)
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan rekaman riwayat percobaan pengerjaan kuis oleh siswa beserta nilai skor akhir dan status kelulusan.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik sesi pengerjaan (Primary Key, Auto Increment) |
| `quiz_id` | BIGINT (UNSIGNED) | 20 | Foreign Key kuis yang dikerjakan (`quiz.id`) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key akun siswa pengerja (`users.id`) |
| `nilai` | INT | 11 | Skor perolehan nilai akhir kuis |
| `is_passed` | TINYINT (BOOLEAN) | 1 | Status kelulusan (1 = Lulus, 0 = Belum Lulus) |
| `created_at` | TIMESTAMP | - | Waktu mulai pengerjaan kuis |
| `updated_at` | TIMESTAMP | - | Waktu selesai evaluasi kuis |

---

### 18. Tabel: `enrollment`
- **Nama Tabel:** `enrollment`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `kursus_id` (mengacu ke `kursus.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan status hak akses keanggotaan siswa yang telah mendaftar dan lunas pada suatu kursus fotografi.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik pendaftaran (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key akun siswa (`users.id`) |
| `kursus_id` | BIGINT (UNSIGNED) | 20 | Foreign Key kelas kursus (`kursus.id`) |
| `tanggal_daftar` | TIMESTAMP | - | Tanggal aktivasi kepesertaan siswa |
| `status` | VARCHAR | 20 | Status kepesertaan ('aktif', 'selesai', 'pending') |
| `created_at` | TIMESTAMP | - | Waktu data enrollment dibuat |
| `updated_at` | TIMESTAMP | - | Waktu data enrollment diperbarui |

---

### 19. Tabel: `sertifikat`
- **Nama Tabel:** `sertifikat`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `kursus_id` (mengacu ke `kursus.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan sertifikat kelulusan digital yang diterbitkan otomatis bagi siswa yang telah menyelesaikan seluruh modul kursus.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik sertifikat (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key penerima sertifikat (`users.id`) |
| `kursus_id` | BIGINT (UNSIGNED) | 20 | Foreign Key kursus yang diselesaikan (`kursus.id`) |
| `nomor_sertifikat` | VARCHAR | 100 | Nomor seri resmi sertifikat (Unique, e.g. CERT-RM-2026-0001) |
| `kode_verifikasi` | VARCHAR | 100 | Kode keamanan unik untuk validasi keaslian publik (Unique) |
| `tanggal_terbit` | DATE | - | Tanggal resmi penerbitan sertifikat |
| `nilai_akhir` | DECIMAL | 5,2 | Nilai rata-rata akumulatif kelulusan siswa |
| `created_at` | TIMESTAMP | - | Waktu sertifikat di-generate sistem |
| `updated_at` | TIMESTAMP | - | Waktu pembaruan data sertifikat |

---

### 20. Tabel: `portofolio`
- **Nama Tabel:** `portofolio`
- **Primary Key:** `id`
- **Foreign Key:** Tidak ada
- **Deskripsi Tabel:** Menyimpan data induk album galeri karya fotografi karya fotografer RanggaMotret untuk dipamerkan kepada calon klien.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik album karya (Primary Key, Auto Increment) |
| `judul` | VARCHAR | 255 | Judul album portofolio karya foto |
| `kategori` | VARCHAR | 100 | Klasifikasi kategori karya foto (Wedding, Portrait, dll) |
| `foto` | VARCHAR | 255 | Path atau URL berkas foto sampul album utama |
| `deskripsi` | TEXT | - | Cerita konsep visual dan arahan gaya karya (Nullable) |
| `tanggal` | DATE | - | Tanggal sesi pemotretan karya berlangsung (Nullable) |
| `status_publikasi` | VARCHAR | 20 | Status penayangan ('published', 'draft', 'archived') |
| `created_at` | TIMESTAMP | - | Waktu album dibuat |
| `updated_at` | TIMESTAMP | - | Waktu informasi album diperbarui |

---

### 21. Tabel: `portofolio_foto`
- **Nama Tabel:** `portofolio_foto`
- **Primary Key:** `id`
- **Foreign Key:** `portofolio_id` (mengacu ke `portofolio.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan relasi multi-foto yang ada di dalam setiap album karya portofolio.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik foto album (Primary Key, Auto Increment) |
| `portofolio_id` | BIGINT (UNSIGNED) | 20 | Foreign Key referensi album karya (`portofolio.id`) |
| `foto` | VARCHAR | 255 | Path lokasi berkas gambar foto |
| `caption` | VARCHAR | 255 | Keterangan teks penjelas foto (Nullable) |
| `urutan` | INT | 11 | Urutan tata letak foto dalam galeri album (Default 0) |
| `created_at` | TIMESTAMP | - | Waktu foto ditambahkan ke album |
| `updated_at` | TIMESTAMP | - | Waktu data foto diperbarui |

---

### 22. Tabel: `galeri_foto`
- **Nama Tabel:** `galeri_foto`
- **Primary Key:** `id`
- **Foreign Key:** `booking_id` (mengacu ke `booking.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan foto-foto hasil sesi pemotretan pesanan klien untuk proses seleksi (*photo proofing*), revisi retouching, dan pengunduhan hasil akhir.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik foto hasil sesi (Primary Key, Auto Increment) |
| `booking_id` | BIGINT (UNSIGNED) | 20 | Foreign Key nomor pemesanan pemotretan (`booking.id`) |
| `foto_path` | VARCHAR | 255 | Path berkas foto pada media penyimpanan server |
| `is_selected` | TINYINT (BOOLEAN) | 1 | Penanda pilihan foto yang disukai/dipilih klien untuk dicetak (1 = Ya, 0 = Tidak) |
| `catatan` | TEXT | - | Permintaan khusus klien pada foto terkait (Nullable) |
| `created_at` | TIMESTAMP | - | Waktu foto diunggah oleh studio |
| `updated_at` | TIMESTAMP | - | Waktu pemilihan/catatan diperbarui klien |

---

### 23. Tabel: `konsultasi`
- **Nama Tabel:** `konsultasi`
- **Primary Key:** `id`
- **Foreign Key:** `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`, Nullable)
- **Deskripsi Tabel:** Menyimpan permohonan konsultasi konsep pemotretan dari calon klien beserta tanggapan rekomendasi dari fotografer.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik konsultasi (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key akun pengguna penanya jika login (`users.id`, Nullable) |
| `nama` | VARCHAR | 255 | Nama lengkap calon klien pemohon |
| `email` | VARCHAR | 255 | Alamat email calon klien |
| `phone` | VARCHAR | 25 | Nomor telepon / WhatsApp calon klien |
| `jenis_layanan` | VARCHAR | 100 | Bidang fotografi yang ingin dikonsultasikan |
| `konsep` | VARCHAR | 255 | Gambaran tema konsep visual yang diinginkan (Nullable) |
| `lokasi` | VARCHAR | 255 | Rencana lokasi atau spot pemotretan (Nullable) |
| `tanggal` | DATE | - | Estimasi tanggal rencana pemotretan (Nullable) |
| `budget` | DECIMAL | 12,2 | Estimasi kesiapan anggaran dana klien (Nullable) |
| `kebutuhan` | TEXT | - | Uraian lengkap kebutuhan dan pertanyaan klien (Nullable) |
| `referensi_foto` | VARCHAR | 255 | Path atau tautan berkas foto inspirasi/moodboard (Nullable) |
| `tanggapan_admin` | TEXT | - | Jawaban dan arahan konsep resmi dari fotografer (Nullable) |
| `status` | VARCHAR | 20 | Status penanganan tiket ('pending', 'replied') |
| `created_at` | TIMESTAMP | - | Waktu pesan konsultasi dikirim klien |
| `updated_at` | TIMESTAMP | - | Waktu tanggapan admin dikirimkan |

---

### 24. Tabel: `testimoni`
- **Nama Tabel:** `testimoni`
- **Primary Key:** `id`
- **Foreign Key:** 
  - `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
  - `booking_id` (mengacu ke `booking.id` dengan relasi `ON DELETE CASCADE`, Nullable)
  - `kursus_id` (mengacu ke `kursus.id` dengan relasi `ON DELETE CASCADE`, Nullable)
- **Deskripsi Tabel:** Menyimpan ulasan feedback, rating bintang kepuasan, dan testimoni dari pelanggan sesi foto maupun peserta kursus.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik ulasan testimoni (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key akun pemberi ulasan (`users.id`) |
| `booking_id` | BIGINT (UNSIGNED) | 20 | Foreign Key sesi foto yang diulas (`booking.id`, Nullable) |
| `kursus_id` | BIGINT (UNSIGNED) | 20 | Foreign Key kursus yang diulas (`kursus.id`, Nullable) |
| `rating` | INT | 11 | Skor kepuasan penilaian bintang skala 1 s/d 5 (Default 5) |
| `komentar` | TEXT | - | Testimoni naratif pengalaman menggunakan jasa studio |
| `foto` | VARCHAR | 255 | Lampiran foto hasil karya yang dipamerkan klien (Nullable) |
| `status` | VARCHAR | 20 | Status moderasi ulasan ('pending', 'approved', 'rejected') |
| `created_at` | TIMESTAMP | - | Waktu pengiriman ulasan |
| `updated_at` | TIMESTAMP | - | Waktu moderasi persetujuan status |

---

### 25. Tabel: `promo`
- **Nama Tabel:** `promo`
- **Primary Key:** `id`
- **Foreign Key:** Tidak ada
- **Deskripsi Tabel:** Menyimpan kupon kode diskon promosi untuk pemotongan biaya tagihan reservasi jasa foto maupun pendaftaran kelas kursus.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik kupon promo (Primary Key, Auto Increment) |
| `kode_promo` | VARCHAR | 50 | Kode kupon unik yang diinputkan pengguna (Unique, e.g. DISKON50) |
| `deskripsi` | TEXT | - | Uraian syarat dan ketentuan pemakaian kupon (Nullable) |
| `diskon_persen` | INT | 11 | Besaran persentase diskon potongan harga (%) (Default 0) |
| `diskon_nominal` | DECIMAL | 12,2 | Besaran nominal potongan langsung (Rp) (Default 0) |
| `berlaku_dari` | DATE | - | Tanggal awal kupon promo dapat digunakan |
| `berlaku_sampai` | DATE | - | Tanggal masa kedaluwarsa kupon promo |
| `status` | VARCHAR | 20 | Status keaktifan kupon ('aktif', 'nonaktif') |
| `created_at` | TIMESTAMP | - | Waktu kupon promo dibuat |
| `updated_at` | TIMESTAMP | - | Waktu kupon promo diperbarui |

---

### 26. Tabel: `notifikasi`
- **Nama Tabel:** `notifikasi`
- **Primary Key:** `id`
- **Foreign Key:** `user_id` (mengacu ke `users.id` dengan relasi `ON DELETE CASCADE`)
- **Deskripsi Tabel:** Menyimpan riwayat notifikasi sistem *in-app* untuk menginformasikan status pemesanan, verifikasi pembayaran, materi baru, dan tanggapan konsultasi.

| Field | Type | Size | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT (UNSIGNED) | 20 | Identifikator unik pesan notifikasi (Primary Key, Auto Increment) |
| `user_id` | BIGINT (UNSIGNED) | 20 | Foreign Key akun pengguna penerima notifikasi (`users.id`) |
| `judul` | VARCHAR | 255 | Judul singkat subjek pemberitahuan |
| `pesan` | TEXT | - | Rincian teks isi pesan pemberitahuan |
| `link` | VARCHAR | 255 | URL tujuan redirect saat notifikasi diklik pengguna (Nullable) |
| `is_read` | TINYINT (BOOLEAN) | 1 | Status keterbacaan pesan (1 = Sudah Dibaca, 0 = Belum Dibaca) |
| `created_at` | TIMESTAMP | - | Waktu notifikasi dipicu oleh sistem |
| `updated_at` | TIMESTAMP | - | Waktu status pesan diperbarui |
