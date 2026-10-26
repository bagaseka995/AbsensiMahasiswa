<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Absensi Mahasiswa
|--------------------------------------------------------------------------
*/

// Redirect root sesuai status login & role user
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.mahasiswa.index');
        }
        return auth()->user()->isDosen()
            ? redirect()->route('dosen.dashboard')
            : redirect()->route('mahasiswa.dashboard');
    }
    return redirect()->route('login');
});

// ===================== AUTH ROUTES =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ===================== MAHASISWA ROUTES =====================
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/jadwal', [MahasiswaController::class, 'jadwal'])->name('jadwal');
    Route::get('/absensi', [MahasiswaController::class, 'absensi'])->name('absensi');
    Route::post('/absensi', [MahasiswaController::class, 'storeAbsensi'])->name('absensi.store');
});

// ===================== DOSEN/ADMIN ROUTES =====================
Route::middleware(['auth', 'role:dosen,admin'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
    Route::get('/sesi-absensi', [DosenController::class, 'sesiAbsensi'])->name('sesi-absensi');
    Route::post('/sesi-absensi/buka', [DosenController::class, 'bukaAbsensi'])->name('sesi-absensi.buka');
    Route::post('/sesi-absensi/{sesi}/tutup', [DosenController::class, 'tutupAbsensi'])->name('sesi-absensi.tutup');
    Route::get('/rekap', [DosenController::class, 'rekap'])->name('rekap');
    Route::get('/laporan', [DosenController::class, 'laporan'])->name('laporan');
});

// ===================== ADMIN KHUSUS ROUTES =====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/mahasiswa', [AdminController::class, 'index'])->name('mahasiswa.index');
    Route::post('/mahasiswa', [AdminController::class, 'store'])->name('mahasiswa.store');
    Route::put('/mahasiswa/{mahasiswa}', [AdminController::class, 'update'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}', [AdminController::class, 'destroy'])->name('mahasiswa.destroy');
});
