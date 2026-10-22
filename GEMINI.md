# Workspace Rules — RanggaMotret.id

## 1. Aturan Wajib Auto-Commit & Push (Git Workflow)
Setiap kali melakukan penambahan, perbaikan, atau perubahan pada kode atau berkas dalam repositori ini:
1. **Lakukan Pengujian / Verifikasi:**
   - Pastikan kode berjalan dengan benar (misal: `php artisan test` atau `npm run build` jika relevan).
2. **Langsung Commit:**
   - Tahapkan semua berkas yang diubah (`git add .`).
   - Buat pesan commit yang jelas dan deskriptif mengikuti format konvensional (misal: `feat: ...`, `fix: ...`, `style: ...`, `refactor: ...`).
3. **Langsung Push ke Remote:**
   - Eksekusi perintah `git push origin <branch>` (cabang saat ini: `main`) segera setelah commit dibuat sebelum menyelesaikan respon kepada pengguna.
   - Jangan tunda commit dan push; pastikan remote repository selalu tersinkronisasi secara otomatis.

## 2. Standar Icon Desain — Phosphor Icons (Duotone)
Seluruh antarmuka web wajib menggunakan **Phosphor Icons** sebagai sistem ikon standar proyek:
1. **Gaya Utama (Default):**
   - Gunakan varian **Duotone** (`<i class="ph-duotone ph-<nama-icon>"></i>`) untuk menghadirkan visual mewah dua-nada (*two-tone depth*) dengan layer primer dan sekunder ber-opacity lembut.
   - Gunakan varian **Fill** (`<i class="ph-fill ph-<nama-icon>"></i>`) khusus untuk status aktif terpilih (misal: bintang rating/favorit terisi).
   - Gunakan varian **Regular** (`<i class="ph ph-<nama-icon>"></i>`) untuk icon utilitarian kecil.
2. **Larangan SVG Mentah:**
   - Dilarang menuliskan raw inline `<svg>` secara langsung di dalam Blade template, kecuali untuk logo brand pihak ketiga khusus yang tidak tersedia di Phosphor.
3. **Daftar Ikon Acuan Sering Digunakan:**
   - *Studio & Kamera:* `ph-camera`, `ph-aperture`, `ph-image`, `ph-images`, `ph-film-strip`, `ph-camera-rotate`
   - *Kalender & Booking:* `ph-calendar-blank`, `ph-calendar-check`, `ph-clock`, `ph-map-pin`
   - *Akademi & Edukasi:* `ph-graduation-cap`, `ph-student`, `ph-book-open`, `ph-certificate`, `ph-chalkboard-teacher`
   - *Aksi & Navigasi:* `ph-plus`, `ph-arrow-left`, `ph-arrow-right`, `ph-check`, `ph-x`, `ph-download-simple`, `ph-upload-simple`, `ph-paper-plane-tilt`
   - *Status & Rating:* `ph-star`, `ph-check-circle`, `ph-warning-circle`, `ph-info`, `ph-sparkle`
   - *Keuangan:* `ph-credit-card`, `ph-wallet`, `ph-receipt`, `ph-bank`, `ph-qrcode`
   - *Komunikasi:* `ph-whatsapp-logo`, `ph-chats-circle`, `ph-envelope`, `ph-phone`

## 3. Standar UI/UX & Kualitas Kode
- Pertahankan standar desain mewah (Luxury Editorial Aesthetic) sesuai pedoman `ui-ux-pro-max`.
- Gunakan Tailwind CSS, Alpine.js, dan Blade templating dengan hierarki kontras yang tajam dan responsif.
- Seluruh teks antarmuka menggunakan Bahasa Indonesia yang profesional dan ramah pengguna.

## 4. Standar Arsitektur MVC & Unified Dashboard (Robustness Diagram)
Pengembangan fitur dan modul wajib selaras 100% dengan **Robustness Diagram (UML Analysis)**:
1. **Unified Adaptive Dashboard:**
   - Gunakan satu pintu `/dashboard` (`DashboardController@index`) untuk semua role pengguna (Admin, Instruktur, Member/Customer/Peserta) yang adaptif menampilkan data dan widget sesuai izin pengguna.
2. **Kesesuaian Boundary, Control, & Entity:**
   - *Boundary (View):* `registrasi`, `login`, `profile`, `katalog`, `kursus`, `materi`, `jadwal`, `reservasi_foto`, `reservasi_kursus`, `konsultasi`, `portofolio`, `pembayaran`, `laporan`, `dashboard`.
   - *Control (Controller):* `AuthController`, `ProfileController`, `DashboardController`, `KatalogController`, `KursusController`, `MateriController`, `JadwalController`, `ReservasiController`, `KonsultasiController`, `PortofolioController`, `PembayaranController`, `LaporanController`.
   - *Entity (Model):* `User`, `Katalog`, `Kursus`, `Materi`, `Jadwal`, `Reservasi`, `Konsultasi`, `Portofolio`, `Pembayaran`.
3. **Keterlacakan Akademik (Traceability):**
   - Pastikan setiap penambahan method dan model baru menjaga keterlacakan 1-to-1 dengan dokumen skripsi/perancangan.

