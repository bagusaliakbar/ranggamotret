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
        Schema::dropIfExists('testimoni');
        Schema::dropIfExists('tugas_submission');
        Schema::dropIfExists('tugas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tugas')) {
            Schema::create('tugas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('modul_id')->constrained('modul')->onDelete('cascade');
                $table->string('judul');
                $table->text('instruksi');
                $table->dateTime('deadline')->nullable();
                $table->string('file_pendukung')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tugas_submission')) {
            Schema::create('tugas_submission', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('file_path')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamp('tanggal_submit');
                $table->integer('nilai')->nullable();
                $table->text('feedback')->nullable();
                $table->string('status')->default('submitted');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('testimoni')) {
            Schema::create('testimoni', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('booking_id')->nullable()->constrained('booking')->onDelete('cascade');
                $table->foreignId('kursus_id')->nullable()->constrained('kursus')->onDelete('cascade');
                $table->integer('rating')->default(5);
                $table->text('komentar');
                $table->string('foto')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }
};
