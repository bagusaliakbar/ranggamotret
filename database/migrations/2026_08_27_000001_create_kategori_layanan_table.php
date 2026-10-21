<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('slug')->unique();
            $table->string('icon')->default('fa-camera');
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
        });

        // Seed initial default categories and existing categories from 'layanan'
        $defaultCategories = [
            [
                'nama' => 'Wedding',
                'slug' => 'wedding',
                'icon' => 'fa-ring',
                'deskripsi' => 'Dokumentasi momen pernikahan sakral dan resepsi dengan gaya visual cinematic dan elegan.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Prewedding',
                'slug' => 'prewedding',
                'icon' => 'fa-heart',
                'deskripsi' => 'Sesi foto romantis pranikah outdoor maupun studio dengan arahan gaya dan konsep personal.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Engagement',
                'slug' => 'engagement',
                'icon' => 'fa-gem',
                'deskripsi' => 'Abadikan momen pertunangan dan lamaran penuh kehangatan bersama keluarga besar.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Graduation',
                'slug' => 'graduation',
                'icon' => 'fa-graduation-cap',
                'deskripsi' => 'Foto wisuda kelulusan individu, bersama sahabat, dan keluarga dengan sentuhan profesional.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Product',
                'slug' => 'product',
                'icon' => 'fa-box-open',
                'deskripsi' => 'Foto komersial produk makanan, busana, kosmetik, dan barang ritel untuk katalog dan marketplace.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Portrait',
                'slug' => 'portrait',
                'icon' => 'fa-user',
                'deskripsi' => 'Sesi foto personal branding, headshot profesional, model fashion, dan profil eksekutif.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Event',
                'slug' => 'event',
                'icon' => 'fa-calendar-check',
                'deskripsi' => 'Liputan dokumentasi acara gathering kantor, seminar, konser musik, ulang tahun, dan pesta keluarga.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Commercial',
                'slug' => 'commercial',
                'icon' => 'fa-building',
                'deskripsi' => 'Fotografi arsitektur interior, hotel, kafe, profil perusahaan, dan materi iklan komersial.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($defaultCategories as $cat) {
            DB::table('kategori_layanan')->updateOrInsert(
                ['nama' => $cat['nama']],
                $cat
            );
        }

        // Also check if any existing category from layanan table is not in list
        if (Schema::hasTable('layanan')) {
            $existingCategories = DB::table('layanan')->distinct()->pluck('kategori');
            foreach ($existingCategories as $catName) {
                if ($catName && !DB::table('kategori_layanan')->where('nama', $catName)->exists()) {
                    DB::table('kategori_layanan')->insert([
                        'nama' => $catName,
                        'slug' => Str::slug($catName),
                        'icon' => 'fa-camera',
                        'deskripsi' => "Kategori layanan {$catName}",
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_layanan');
    }
};
