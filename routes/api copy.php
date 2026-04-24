<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Esp\EspSyncController;

Route::prefix('esp')->group(function () {

    // =========================
    // COMMAND POLLING (ESP → SERVER)
    // =========================
    Route::get('/get-command', [EspSyncController::class, 'getCommand']);

    // =========================
    // SLOT UPDATE (ESP → SERVER)
    // =========================
    Route::post('/update-slot', [EspSyncController::class, 'updateSlot']);

    // =========================
    // ACK COMMAND
    // =========================
    Route::post('/ack-command', [EspSyncController::class, 'ackCommand']);
});