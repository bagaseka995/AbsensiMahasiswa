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
        Schema::table('sesi_absensi', function (Blueprint $table) {
            $table->unsignedInteger('pertemuan_ke')->default(1);
            $table->string('topik')->nullable();
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->foreignId('sesi_absensi_id')->nullable()->constrained('sesi_absensi')->nullOnDelete();
            $table->unsignedInteger('pertemuan_ke')->default(1);
            $table->string('bukti_foto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropForeign(['sesi_absensi_id']);
            $table->dropColumn(['sesi_absensi_id', 'pertemuan_ke', 'bukti_foto']);
        });

        Schema::table('sesi_absensi', function (Blueprint $table) {
            $table->dropColumn(['pertemuan_ke', 'topik']);
        });
    }
};
