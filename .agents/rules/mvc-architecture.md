# Standar Arsitektur MVC & Traceability Robustness Diagram

Dokumen arsitektur ini mendefinisikan standar pemetaan **Model-View-Controller (MVC)** pada proyek **RanggaMotret.id** agar selaras 100% dengan **Robustness Diagram (ICONIX Process / UML Analysis)** untuk memudahkan pembuatan dokumen akademik (Skripsi/Tugas Akhir/SRS).

---

## 1. Prinsip Utama Arsitektur

1. **Keterlacakan Penuh (1-to-1 Traceability):**
   - Setiap berkas Controller, Model, dan View wajib memiliki korelasi langsung dan konsisten dengan komponen pada Robustness Diagram.
   - Menggunakan terminologi Bahasa Indonesia yang baku dan seragam.
2. **Unified Adaptive Dashboard (Satu Pintu untuk Semua Role):**
   - Route `/dashboard` (`DashboardController@index`) adalah gerbang utama dasbor setelah login.
   - Dasbor beradaptasi secara dinamis menyajikan ringkasan dan widget sesuai peran pengguna yang aktif (`admin`, `instruktur`, atau `customer`/`peserta`).
   - Tidak ada duplikasi halaman dasbor yang redundan (*DRY Principle*).
3. **Kompatibilitas Mundur (Backward Compatibility):**
   - Relasi database dan nama tabel yang sudah ada tetap dipertahankan melalui mekanisme pewarisan model (*Model Inheritance / Aliasing*).

---

## 2. Matriks Pemetaan Komponen (Traceability Matrix)

| Boundary (View / Halaman) | Control (Controller) | Entity (Model) | Tabel Database | Deskripsi Fungsional |
| :--- | :--- | :--- | :--- | :--- |
| `registrasi`, `login` | `AuthController` | `User` | `users` | Autentikasi sesi & registrasi pengguna baru |
| `profile` | `ProfileController` | `User` | `users`, `customers` | Pengelolaan data profil, kontak, & kata sandi |
| `dashboard` | `DashboardController` | `User`, `Reservasi`, `Pembayaran`, `Kursus` | Berbagai tabel | Dasbor terpadu adaptif multi-role |
| `katalog` | `KatalogController` | `Katalog` (`Layanan`), `Paket` | `layanan`, `pakets` | Eksplorasi layanan foto & paket harga |
| `kursus` | `KursusController` | `Kursus`, `Enrollment` | `kursus`, `enrollments` | Katalog akademi & ruang belajar siswa |
| `materi` | `MateriController` | `Materi`, `Modul`, `Tugas`, `Quiz` | `materis`, `moduls`, dll | Modul belajar, penyerahan tugas, & kuis |
| `jadwal` | `JadwalController` | `Jadwal` (`Booking`) | `booking` | Kalender jadwal pemotretan & jadwal mengajar |
| `reservasi_foto`, `reservasi_kursus` | `ReservasiController` | `Reservasi` (`Booking`) | `booking` | Alur reservasi pemotretan & pendaftaran kursus |
| `konsultasi` | `KonsultasiController` | `Konsultasi` | `konsultasis` | Form konsultasi konsep foto & tanggapan admin |
| `portofolio` | `PortofolioController` | `Portofolio` | `portofolios` | Galeri portofolio & studio lightbox viewer |
| `pembayaran` | `PembayaranController` | `Pembayaran`, `Invoice` | `pembayarans`, `invoices` | Checkout Midtrans Snap, transfer manual, & verifikasi |
| `laporan` | `LaporanController` | `Reservasi`, `Pembayaran`, `User` | `booking`, `pembayarans` | Laporan keuangan, sesi foto, & progres akademi |

## 3. Konsolidasi & Jembatan Kompatibilitas (Bridge Controllers)

Untuk menjaga kebersihan arsitektur MVC sekaligus memastikan kode lama/referensi tidak mengalami *breaking change*:
- **Primary Canonical Controllers (Mandatori untuk Pengembangan Baru):**
  - Seluruh logika bisnis inti diimplementasikan 100% pada Controller Utama: `AuthController`, `ProfileController`, `DashboardController`, `KatalogController`, `KursusController`, `MateriController`, `JadwalController`, `ReservasiController`, `KonsultasiController`, `PortofolioController`, `PembayaranController`, `LaporanController`, dan `UserController`.
- **Bridge Controllers (Inheritance Wrappers):**
  - Berkas lama seperti `BookingController`, `CourseController`, `PaymentController`, `ReportController`, `ConsultationController`, `AdminDashboardController`, `AssignmentController`, `QuizController`, `LayananController`, `KategoriLayananController`, `ProofingController`, dan `TestimonialController` mewarisi (*extends*) Controller Utama dengan anotasi `@deprecated`.
  - Hal ini menjamin bahwa setiap dependensi lama tetap berfungsi secara mulus sementara semua route di `routes/web.php` diarahkan langsung ke Controller Utama.

---

## 4. Pedoman Pengembangan Selanjutnya

Setiap kali menambahkan modul, fitur, atau perbaikan baru:
1. **Controller Baru:**
   - Gunakan nama Controller kanonikal sesuai domain bisnis pada tabel di atas.
   - Hindari membuat Controller baru dengan nama bahasa Inggris atau nama parsial yang redundant.
2. **Model Baru:**
   - Gunakan Model yang sudah terdefinisi. Jika memerlukan perluasan, inherit dari entitas inti.
3. **Route:**
   - Cantumkan route canonical sesuai nama modul (misal: `/katalog`, `/kursus`, `/reservasi`, `/pembayaran`, `/dashboard`).
4. **View:**
   - Letakkan template blade dalam folder yang mencerminkan nama boundary di atas.

