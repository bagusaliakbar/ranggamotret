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
        if (Schema::hasTable('users')) {
            // Update admin dan instruktur menjadi owner
            DB::table('users')
                ->whereIn('role', ['admin', 'instruktur'])
                ->update(['role' => 'owner']);

            // Update customer dan peserta menjadi pelanggan
            DB::table('users')
                ->whereIn('role', ['customer', 'peserta'])
                ->update(['role' => 'pelanggan']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('role', 'owner')
                ->update(['role' => 'admin']);

            DB::table('users')
                ->where('role', 'pelanggan')
                ->update(['role' => 'customer']);
        }
    }
};
