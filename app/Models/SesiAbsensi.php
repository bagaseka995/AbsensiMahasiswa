<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAbsensi extends Model
{
    protected $table = 'sesi_absensi';

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'pertemuan_ke',
        'topik',
        'secret_key',
        'tanggal',
        'is_active',
        'started_at',
        'closed_at',
    ];

    /**
     * Relasi: Data absensi pada sesi ini
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'sesi_absensi_id');
    }

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'is_active' => 'boolean',
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Relasi: Mata kuliah
     */
    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    /**
     * Relasi: Dosen yang membuka sesi
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /**
     * Generate kode 6 digit berdasarkan waktu (berganti tiap 30 detik)
     * Menggunakan HMAC-based approach mirip TOTP
     */
    public function generateCode(): string
    {
        $timeSlot = intdiv(time(), 30); // Berganti setiap 30 detik
        $hash = hash_hmac('sha256', (string) $timeSlot, $this->secret_key);

        // Ambil 6 digit angka dari hash
        $numeric = hexdec(substr($hash, 0, 15)) % 1000000;
        $code = str_pad((string) $numeric, 6, '0', STR_PAD_LEFT);

        return $code;
    }

    /**
     * Validasi kode yang dimasukkan mahasiswa
     * Menerima kode saat ini DAN kode dari slot sebelumnya (toleransi)
     */
    public function validateCode(string $inputCode): bool
    {
        $inputCode = trim($inputCode);

        // Cek kode untuk time slot saat ini
        $currentSlot = intdiv(time(), 30);

        // Toleransi: cek slot sekarang dan slot sebelumnya (antisipasi delay)
        for ($i = 0; $i <= 1; $i++) {
            $slot = $currentSlot - $i;
            $hash = hash_hmac('sha256', (string) $slot, $this->secret_key);
            $numeric = hexdec(substr($hash, 0, 15)) % 1000000;
            $code = str_pad((string) $numeric, 6, '0', STR_PAD_LEFT);

            if ($inputCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hitung detik tersisa sebelum kode berganti
     */
    public function getSecondsRemaining(): int
    {
        return 30 - (time() % 30);
    }
}
