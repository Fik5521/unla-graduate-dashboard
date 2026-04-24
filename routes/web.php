<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController; // Pastikan Controller ini sudah dibuat
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/

// Halaman Utama (Dashboard Utama)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Link Khusus Login (Tanpa tombol di UI)
Route::get('/login-admin', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login-proses', [AuthController::class, 'login'])->name('login.post');

// API tetap publik agar Chart di dashboard utama jalan
Route::get('/api/top-cumlaude', [DashboardController::class, 'getTopCumlaude'])->name('api.top-cumlaude');


/*
|--------------------------------------------------------------------------
| PRIVATE ROUTES (Hanya untuk yang sudah Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Halaman Mahasiswa
    Route::get('/mahasiswa', [DashboardController::class, 'mahasiswa'])->name('mahasiswa.index');

    // Halaman Analisis Prodi
    Route::get('/analisis-prodi', [DashboardController::class, 'analisisProdi'])->name('analisis.prodi');

    // Halaman Perbandingan
    Route::get('/perbandingan-prodi', [DashboardController::class, 'perbandinganProdi'])->name('perbandingan.prodi');

    // Export Data
    Route::get('/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});