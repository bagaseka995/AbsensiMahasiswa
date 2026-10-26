<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop unique constraints based on tanggal so multiple meetings can be opened
        try {
            DB::statement('DROP INDEX IF EXISTS sesi_absensi_mata_kuliah_id_tanggal_unique');
        } catch (\Throwable $e) {}

        try {
            DB::statement('DROP INDEX IF EXISTS absensi_user_id_mata_kuliah_id_tanggal_unique');
        } catch (\Throwable $e) {}

        // Add unique constraint per meeting number
        try {
            Schema::table('sesi_absensi', function (Blueprint $table) {
                $table->unique(['mata_kuliah_id', 'pertemuan_ke']);
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('absensi', function (Blueprint $table) {
                $table->unique(['user_id', 'mata_kuliah_id', 'pertemuan_ke']);
            });
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('DROP INDEX IF EXISTS sesi_absensi_mata_kuliah_id_pertemuan_ke_unique');
        } catch (\Throwable $e) {}

        try {
            DB::statement('DROP INDEX IF EXISTS absensi_user_id_mata_kuliah_id_pertemuan_ke_unique');
        } catch (\Throwable $e) {}
    }
};
