<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        // 2. Layanan (Photography Services)
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori'); // Wedding, Prewedding, Graduation, Portrait, Product, Event, Commercial
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_mulai', 12, 2)->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
        });

        // 3. Paket (Service Packages)
        Schema::create('paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanan')->onDelete('cascade');
            $table->string('nama_paket');
            $table->decimal('harga', 12, 2);
            $table->string('durasi')->nullable(); // e.g. "4 Jam", "Full Day"
            $table->integer('jumlah_fotografer')->default(1);
            $table->integer('jumlah_foto')->default(50);
            $table->text('fasilitas')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
        });

        // 4. Booking
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_booking')->unique(); // e.g. BK-20260809-001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Customer
            $table->foreignId('layanan_id')->constrained('layanan')->onDelete('cascade');
            $table->foreignId('paket_id')->constrained('paket')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('lokasi');
            $table->text('kebutuhan_khusus')->nullable();
            $table->string('status')->default('Menunggu Konfirmasi'); 
            // Status: Menunggu Konfirmasi, Disetujui, Ditolak, Menunggu Pembayaran, Dibayar, Terjadwal, Selesai, Dibatalkan
            $table->timestamps();
        });

        // 5. Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique(); // TRX-20260809-001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('booking')->onDelete('cascade');
            $table->unsignedBigInteger('kursus_id')->nullable(); // Foreign key to kursus created later
            $table->decimal('jumlah', 12, 2);
            $table->string('metode_pembayaran'); // Transfer Bank, QRIS, E-Wallet, Cash
            $table->timestamp('tanggal_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status')->default('Menunggu Verifikasi');
            // Status: Belum Dibayar, Menunggu Verifikasi, Dibayar, Ditolak, Dikembalikan
            $table->timestamps();
        });

        // 6. Kursus
        Schema::create('kursus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('durasi')->nullable(); // e.g. "4 Minggu", "12 Jam"
            $table->string('level')->default('Pemula'); // Pemula, Menengah, Mahir
            $table->foreignId('instruktur_id')->constrained('users')->onDelete('cascade');
            $table->string('thumbnail')->nullable();
            $table->string('status')->default('aktif'); // aktif, draft
            $table->timestamps();
        });

        // Add foreign key constraint for pembayaran.kursus_id
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('kursus_id')->references('id')->on('kursus')->onDelete('set null');
        });

        // 7. Invoice
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique(); // INV-20260809-001
            $table->foreignId('pembayaran_id')->constrained('pembayaran')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('booking')->onDelete('cascade');
            $table->foreignId('kursus_id')->nullable()->constrained('kursus')->onDelete('cascade');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('status_pembayaran');
            $table->timestamps();
        });

        // 8. Modul Kursus
        Schema::create('modul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->string('judul');
            $table->integer('urutan')->default(1);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 9. Materi Pembelajaran
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul')->onDelete('cascade');
            $table->string('judul');
            $table->string('tipe')->default('teks'); // teks, pdf, foto, video, link
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->longText('isi')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        // 10. Progress Materi Peserta
        Schema::create('materi_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materi_id')->constrained('materi')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'materi_id']);
        });

        // 11. Tugas Praktik
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul')->onDelete('cascade');
            $table->string('judul');
            $table->text('instruksi');
            $table->dateTime('deadline')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->timestamps();
        });

        // 12. Submission Tugas
        Schema::create('tugas_submission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_submit');
            $table->integer('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->string('status')->default('submitted'); // submitted, graded, revision_requested
            $table->timestamps();
        });

        // 13. Quiz
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul')->onDelete('cascade');
            $table->string('judul');
            $table->integer('durasi_menit')->default(30);
            $table->integer('passing_grade')->default(70);
            $table->timestamps();
        });

        // 14. Soal Quiz
        Schema::create('quiz_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->string('pilihan_a');
            $table->string('pilihan_b');
            $table->string('pilihan_c');
            $table->string('pilihan_d');
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D']);
            $table->text('pembahasan')->nullable();
            $table->integer('bobot_nilai')->default(10);
            $table->timestamps();
        });

        // 15. Attempt Quiz
        Schema::create('quiz_attempt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('nilai');
            $table->boolean('is_passed')->default(false);
            $table->timestamps();
        });

        // 16. Enrollment
        Schema::create('enrollment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->timestamp('tanggal_daftar');
            $table->string('status')->default('aktif'); // aktif, selesai, pending
            $table->timestamps();

            $table->unique(['user_id', 'kursus_id']);
        });

        // 17. Sertifikat
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->string('nomor_sertifikat')->unique();
            $table->string('kode_verifikasi')->unique();
            $table->date('tanggal_terbit');
            $table->decimal('nilai_akhir', 5, 2);
            $table->timestamps();
        });

        // 18. Portofolio
        Schema::create('portofolio', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori'); // Wedding, Portrait, Product, Landscape, Event, Commercial
            $table->string('foto');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('status_publikasi')->default('published'); // published, draft
            $table->timestamps();
        });

        // 19. Galeri Foto & Proofing
        Schema::create('galeri_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('booking')->onDelete('cascade');
            $table->string('foto_path');
            $table->boolean('is_selected')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 20. Konsultasi
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('email');
            $table->string('phone');
            $table->string('jenis_layanan');
            $table->string('konsep')->nullable();
            $table->string('lokasi')->nullable();
            $table->date('tanggal')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->text('kebutuhan')->nullable();
            $table->string('referensi_foto')->nullable();
            $table->text('tanggapan_admin')->nullable();
            $table->string('status')->default('pending'); // pending, replied
            $table->timestamps();
        });

        // 21. Testimoni
        Schema::create('testimoni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('booking')->onDelete('cascade');
            $table->foreignId('kursus_id')->nullable()->constrained('kursus')->onDelete('cascade');
            $table->integer('rating')->default(5);
            $table->text('komentar');
            $table->string('foto')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });

        // 22. Promo
        Schema::create('promo', function (Blueprint $table) {
            $table->id();
            $table->string('kode_promo')->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('diskon_persen')->default(0);
            $table->decimal('diskon_nominal', 12, 2)->default(0);
            $table->date('berlaku_dari');
            $table->date('berlaku_sampai');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        // 23. Notifikasi
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('pesan');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('promo');
        Schema::dropIfExists('testimoni');
        Schema::dropIfExists('konsultasi');
        Schema::dropIfExists('galeri_foto');
        Schema::dropIfExists('portofolio');
        Schema::dropIfExists('sertifikat');
        Schema::dropIfExists('enrollment');
        Schema::dropIfExists('quiz_attempt');
        Schema::dropIfExists('quiz_soal');
        Schema::dropIfExists('quiz');
        Schema::dropIfExists('tugas_submission');
        Schema::dropIfExists('tugas');
        Schema::dropIfExists('materi_progress');
        Schema::dropIfExists('materi');
        Schema::dropIfExists('modul');
        Schema::dropIfExists('invoice');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('kursus');
        Schema::dropIfExists('booking');
        Schema::dropIfExists('paket');
        Schema::dropIfExists('layanan');
        Schema::dropIfExists('customers');
    }
};
