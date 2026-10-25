<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        $dropFkIfExists = function ($table, $fkName) use ($driver) {
            if ($driver === 'sqlite') {
                return;
            }

            $exists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = ? 
                  AND CONSTRAINT_NAME = ?
            ", [$table, $fkName]);

            if (!empty($exists)) {
                Schema::table($table, function (Blueprint $t) use ($fkName) {
                    $t->dropForeign($fkName);
                });
            }
        };

        $dropFkIfExists('galeri_foto', 'galeri_foto_booking_id_foreign');
        $dropFkIfExists('invoice', 'invoice_booking_id_foreign');
        $dropFkIfExists('pembayaran', 'pembayaran_booking_id_foreign');

        // 2. Modifikasi struktur tabel `booking` dan rename menjadi `reservasi`
        Schema::rename('booking', 'reservasi');

        Schema::table('reservasi', function (Blueprint $table) use ($driver) {
            // Rename kolom nomor_booking menjadi nomor_reservasi
            $table->renameColumn('nomor_booking', 'nomor_reservasi');

            // Tambahkan kolom tipe reservasi ('fotografi' atau 'kursus')
            $table->string('tipe', 20)->default('fotografi')->after('id');

            // Jadikan kolom khusus fotografi menjadi nullable untuk mendukung kursus
            if ($driver !== 'sqlite') {
                $table->unsignedBigInteger('layanan_id')->nullable()->change();
                $table->unsignedBigInteger('paket_id')->nullable()->change();
                $table->date('tanggal')->nullable()->change();
                $table->time('waktu')->nullable()->change();
                $table->string('lokasi')->nullable()->change();
            }

            // Tambahkan kolom kursus_id untuk reservasi kursus
            $table->unsignedBigInteger('kursus_id')->nullable()->after('paket_id');
            $table->foreign('kursus_id', 'reservasi_kursus_id_foreign')
                  ->references('id')->on('kursus')
                  ->onDelete('cascade');
        });

        // 3. Migrasikan data dari tabel `enrollment` ke tabel `reservasi`
        if (Schema::hasTable('enrollment')) {
            $enrollments = DB::table('enrollment')->get();
            foreach ($enrollments as $enr) {
                $statusReservasi = ($enr->status === 'selesai') ? 'Selesai' : 'Dibayar';
                $nomorReservasi = 'RSV-KRS-' . date('Ymd', strtotime($enr->created_at ?? now())) . '-' . str_pad($enr->id, 4, '0', STR_PAD_LEFT);

                // Cek apakah sudah pernah ada
                $existingId = DB::table('reservasi')
                    ->where('user_id', $enr->user_id)
                    ->where('kursus_id', $enr->kursus_id)
                    ->value('id');

                if (!$existingId) {
                    $newReservasiId = DB::table('reservasi')->insertGetId([
                        'tipe' => 'kursus',
                        'nomor_reservasi' => $nomorReservasi,
                        'user_id' => $enr->user_id,
                        'layanan_id' => null,
                        'paket_id' => null,
                        'kursus_id' => $enr->kursus_id,
                        'tanggal' => date('Y-m-d', strtotime($enr->tanggal_daftar ?? now())),
                        'waktu' => date('H:i:s', strtotime($enr->tanggal_daftar ?? now())),
                        'lokasi' => 'Online Classroom LMS',
                        'kebutuhan_khusus' => 'Pendaftaran Kursus Akademi',
                        'status' => $statusReservasi,
                        'created_at' => $enr->created_at ?? now(),
                        'updated_at' => $enr->updated_at ?? now(),
                    ]);

                    // Update pembayaran kursus terkait agar terhubung ke reservasi baru
                    DB::table('pembayaran')
                        ->where('user_id', $enr->user_id)
                        ->where('kursus_id', $enr->kursus_id)
                        ->whereNull('booking_id')
                        ->update(['booking_id' => $newReservasiId]);

                    // Update invoice kursus terkait jika ada
                    DB::table('invoice')
                        ->where('user_id', $enr->user_id)
                        ->where('kursus_id', $enr->kursus_id)
                        ->whereNull('booking_id')
                        ->update(['booking_id' => $newReservasiId]);
                }
            }

            // Drop tabel enrollment
            Schema::dropIfExists('enrollment');
        }

        // 4. Ubah kolom `booking_id` menjadi `reservasi_id` pada tabel berelasi & pasang FK baru
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->renameColumn('booking_id', 'reservasi_id');
            $table->foreign('reservasi_id', 'pembayaran_reservasi_id_foreign')
                  ->references('id')->on('reservasi')
                  ->onDelete('cascade');
        });

        Schema::table('invoice', function (Blueprint $table) {
            $table->renameColumn('booking_id', 'reservasi_id');
            $table->foreign('reservasi_id', 'invoice_reservasi_id_foreign')
                  ->references('id')->on('reservasi')
                  ->onDelete('cascade');
        });

        Schema::table('galeri_foto', function (Blueprint $table) {
            $table->renameColumn('booking_id', 'reservasi_id');
            $table->foreign('reservasi_id', 'galeri_foto_reservasi_id_foreign')
                  ->references('id')->on('reservasi')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert foreign keys
        Schema::table('galeri_foto', function (Blueprint $table) {
            $table->dropForeign('galeri_foto_reservasi_id_foreign');
            $table->renameColumn('reservasi_id', 'booking_id');
        });

        Schema::table('invoice', function (Blueprint $table) {
            $table->dropForeign('invoice_reservasi_id_foreign');
            $table->renameColumn('reservasi_id', 'booking_id');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign('pembayaran_reservasi_id_foreign');
            $table->renameColumn('reservasi_id', 'booking_id');
        });

        // Recreate enrollment table
        Schema::create('enrollment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->string('status')->default('aktif');
            $table->timestamps();
            $table->unique(['user_id', 'kursus_id']);
        });

        // Revert reservasi table
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropForeign('reservasi_kursus_id_foreign');
            $table->dropColumn(['tipe', 'kursus_id']);
            $table->renameColumn('nomor_reservasi', 'nomor_booking');
        });

        Schema::rename('reservasi', 'booking');

        // Restore foreign keys ke booking
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
        });

        Schema::table('invoice', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
        });

        Schema::table('galeri_foto', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
        });
    }
};
