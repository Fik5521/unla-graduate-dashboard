<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. SENSOR UNTUK MENDETEKSI LOGIN BERHASIL
        Event::listen(function (Login $event) {
            AuditLog::create([
                'aksi' => 'Login Sistem',
                'keterangan' => "Pengguna {$event->user->name} ({$event->user->email}) berhasil masuk ke dalam sistem."
            ]);
        });

        // 2. SENSOR UNTUK MENDETEKSI LOGOUT
        Event::listen(function (Logout $event) {
            // Pastikan user terdeteksi sebelum mencatat log
            if ($event->user) {
                AuditLog::create([
                    'aksi' => 'Logout Sistem',
                    'keterangan' => "Pengguna {$event->user->name} telah keluar dari sistem."
                ]);
            }
        });

        \Illuminate\Pagination\Paginator::useTailwind();
    }
}
