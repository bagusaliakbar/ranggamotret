<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Preload unified robustness entity models to register all enclosed sub-entity classes
        require_once app_path('Models/User.php');
        require_once app_path('Models/Katalog.php');
        require_once app_path('Models/Kursus.php');
        require_once app_path('Models/Materi.php');
        require_once app_path('Models/Jadwal.php');
        require_once app_path('Models/Reservasi.php');
        require_once app_path('Models/Konsultasi.php');
        require_once app_path('Models/Portofolio.php');
        require_once app_path('Models/Pembayaran.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
