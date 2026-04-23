<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Halaman Mahasiswa
Route::get('/mahasiswa', [DashboardController::class, 'mahasiswa'])->name('mahasiswa.index');

// API & Export
Route::get('/api/top-cumlaude', [DashboardController::class, 'getTopCumlaude'])->name('api.top-cumlaude');
Route::get('/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export');

// Halaman Analisis Prodi (SEKARANG PAKAI DashboardController)
Route::get('/analisis-prodi', [DashboardController::class, 'analisisProdi'])->name('analisis.prodi');
