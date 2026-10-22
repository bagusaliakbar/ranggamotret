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

## 2. Standar UI/UX & Kualitas Kode
- Pertahankan standar desain mewah (Luxury Editorial Aesthetic) sesuai pedoman `ui-ux-pro-max`.
- Gunakan Tailwind CSS, Alpine.js, dan Blade templating dengan hierarki kontras yang tajam dan responsif.
- Seluruh teks antarmuka menggunakan Bahasa Indonesia yang profesional dan ramah pengguna.
