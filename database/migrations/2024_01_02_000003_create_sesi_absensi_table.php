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
        Schema::create('sesi_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->string('secret_key', 64); // Secret untuk generate kode
            $table->date('tanggal');
            $table->boolean('is_active')->default(true);
            $table->timestamp('started_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            // Satu sesi per mata kuliah per pertemuan
            $table->unique(['mata_kuliah_id', 'tanggal']); // fallback
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_absensi');
    }
};
