---
name: auto-commit-push
description: Mandatory rule to automatically commit and push all code changes to git remote immediately after every task.
trigger: always_on
---

# Aturan Wajib Auto-Commit & Push

Setiap kali agent melakukan perubahan, perbaikan, atau penambahan berkas dalam proyek ini:

1. **Verifikasi:**
   - Jalankan `php artisan test` atau validasi terkait untuk memastikan integritas kode.
2. **Commit:**
   - Lakukan `git add .` dan buat commit dengan pesan deskriptif (misal: `feat: ...`, `fix: ...`, `style: ...`).
3. **Push:**
   - Lakukan `git push origin <branch>` (cabang utama: `main`) secara langsung tanpa menunggu permintaan terpisah dari user.
