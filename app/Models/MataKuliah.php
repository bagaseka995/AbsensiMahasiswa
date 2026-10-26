<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'dosen_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'sks',
    ];

    /**
     * Relasi: Dosen pengampu mata kuliah
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /**
     * Relasi: Mahasiswa yang mengikuti mata kuliah ini (many-to-many)
     */
    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mahasiswa_mata_kuliah')
                    ->withTimestamps();
    }

    /**
     * Relasi: Data absensi untuk mata kuliah ini
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Format jadwal: "Senin, 08:00 - 10:00"
     */
    public function getJadwalAttribute(): string
    {
        return $this->hari . ', ' . substr($this->jam_mulai, 0, 5) . ' - ' . substr($this->jam_selesai, 0, 5);
    }
}
