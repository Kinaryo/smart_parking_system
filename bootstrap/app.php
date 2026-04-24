<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // <--- PASTIKAN INI ADA untuk memuat routes/api.php
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // --- EXCLUDE CSRF UNTUK ESP ---
        $middleware->validateCsrfTokens(except: [
            'esp/*',     // Mengecualikan semua yang berawalan esp/ (jika di web.php)
            'api/esp/*', // Mengecualikan semua yang berawalan api/esp/ (jika di api.php)
        ]);
        // ------------------------------

        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class, 
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'guest.custom' => \App\Http\Middleware\RedirectIfAuthenticatedCustom::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();