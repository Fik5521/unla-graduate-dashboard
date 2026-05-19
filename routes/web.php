<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE PUBLIC (Guest & Admin bisa akses)
// ==========================================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/top-cumlaude', [DashboardController::class, 'getTopCumlaude'])->name('api.top-cumlaude');
Route::get('/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export');
Route::get('/kinerja-prodi', [\App\Http\Controllers\DashboardController::class, 'kinerjaProdi'])->name('kinerja.prodi');

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
    // Halaman Pengaturan (Hanya bisa lewat URL jika menu di-hidden)
    Route::get('/pengaturan', [DashboardController::class, 'settings'])->name('settings');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tambahkan di dalam grup auth
    Route::post('/import-json', [DashboardController::class, 'importJson'])->name('import.json');
});
