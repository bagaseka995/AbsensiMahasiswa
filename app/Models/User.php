<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'nim', 'role', 'kelas', 'program_studi'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user adalah mahasiswa
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Cek apakah user adalah dosen
     */
    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah dosen atau admin
     */
    public function isDosenOrAdmin(): bool
    {
        return in_array($this->role, ['dosen', 'admin']);
    }

    /**
     * Relasi: Mata kuliah yang diikuti mahasiswa (many-to-many)
     */
    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'mahasiswa_mata_kuliah')
                    ->withTimestamps();
    }

    /**
     * Relasi: Mata kuliah yang diampu dosen (one-to-many)
     */
    public function mataKuliahDiampu(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'dosen_id');
    }

    /**
     * Relasi: Data absensi mahasiswa
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}
