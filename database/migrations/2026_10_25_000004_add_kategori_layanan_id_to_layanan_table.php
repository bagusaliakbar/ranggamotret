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
        if (Schema::hasTable('layanan')) {
            Schema::table('layanan', function (Blueprint $table) {
                if (!Schema::hasColumn('layanan', 'kategori_layanan_id')) {
                    $table->unsignedBigInteger('kategori_layanan_id')->nullable()->after('kategori');
                }
            });

            // Sinkronisasi data awal: hubungkan layanan eksisting dengan ID kategori_layanan
            if (Schema::hasTable('kategori_layanan')) {
                $categories = DB::table('kategori_layanan')->get();
                foreach ($categories as $cat) {
                    DB::table('layanan')
                        ->where('kategori', $cat->nama)
                        ->update(['kategori_layanan_id' => $cat->id]);
                }

                // Tambahkan relasi foreign key resmi ke tabel kategori_layanan
                Schema::table('layanan', function (Blueprint $table) {
                    $table->foreign('kategori_layanan_id')
                        ->references('id')
                        ->on('kategori_layanan')
                        ->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('layanan') && Schema::hasColumn('layanan', 'kategori_layanan_id')) {
            Schema::table('layanan', function (Blueprint $table) {
                $driver = DB::getDriverName();
                if ($driver !== 'sqlite') {
                    $table->dropForeign(['kategori_layanan_id']);
                }
                $table->dropColumn('kategori_layanan_id');
            });
        }
    }
};
