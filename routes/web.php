<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE PUBLIC (Guest & Admin bisa akses)
// ==========================================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/mahasiswa', [DashboardController::class, 'mahasiswas'])->name('mahasiswas'); // Halaman Terpisah Baru
Route::get('/api/top-cumlaude', [DashboardController::class, 'getTopCumlaude'])->name('api.top-cumlaude');
Route::get('/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export');
Route::get('/kinerja-prodi', [DashboardController::class, 'kinerjaProdi'])->name('kinerja.prodi');
// Route untuk Export Excel dan PDF (Pastikan letaknya di dalam grup middleware auth jika ada)
// Tambahkan ini di bawah route dashboard kamu yang sudah ada
Route::get('/kinerja-prodi/export/excel', [App\Http\Controllers\DashboardController::class, 'exportKinerjaExcel'])->name('kinerja.export.excel');
Route::get('/kinerja-prodi/export/pdf', [App\Http\Controllers\DashboardController::class, 'exportKinerjaPdf'])->name('kinerja.export.pdf');
// ==========================================
// ROUTE GUEST (Hanya untuk proses Login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login-admin', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login-proses', [AuthController::class, 'login'])->name('login.post');
});

// ==========================================
// ROUTE AUTH (Wajib login admin)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/pengaturan', [DashboardController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [DashboardController::class, 'updateProfile'])->name('settings.update.profile');
    Route::put('/settings/password', [DashboardController::class, 'updatePassword'])->name('settings.update.password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/import-data', [DashboardController::class, 'importData'])->name('import.data');
});
