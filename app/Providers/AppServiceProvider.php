<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // Tambahkan ini

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
        // Deteksi jika menggunakan ngrok atau environment bukan local
        if (str_contains(request()->header('host'), 'ngrok-free.app') || config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}