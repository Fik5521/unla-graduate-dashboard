<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/mahasiswa', [DashboardController::class, 'mahasiswa'])->name('mahasiswa.index');
Route::get('/api/top-cumlaude', [DashboardController::class, 'getTopCumlaude'])->name('api.top-cumlaude');
Route::get('/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export');
