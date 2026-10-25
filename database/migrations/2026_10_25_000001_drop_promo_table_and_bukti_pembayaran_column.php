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
        // 1. Drop tabel promo yang tidak terpakai
        Schema::dropIfExists('promo');

        // 2. Drop kolom bukti_pembayaran pada tabel pembayaran (sistem beralih 100% ke Midtrans Gateway)
        if (Schema::hasTable('pembayaran') && Schema::hasColumn('pembayaran', 'bukti_pembayaran')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->dropColumn('bukti_pembayaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pembayaran') && !Schema::hasColumn('pembayaran', 'bukti_pembayaran')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->string('bukti_pembayaran')->nullable()->after('tanggal_pembayaran');
            });
        }

        if (!Schema::hasTable('promo')) {
            Schema::create('promo', function (Blueprint $table) {
                $table->id();
                $table->string('kode_promo')->unique();
                $table->text('deskripsi')->nullable();
                $table->integer('diskon_persen')->default(0);
                $table->decimal('diskon_nominal', 12, 2)->default(0);
                $table->date('berlaku_dari')->nullable();
                $table->date('berlaku_sampai')->nullable();
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }
    }
};
