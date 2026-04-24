<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\MqttListener; // Pastikan import ini ada

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Registrasi Command MQTT Listener
 */
Artisan::command('mqtt:listen', function () {
    $this->call(MqttListener::class);
})->purpose('Mendengarkan data dari ESP8266 (Status Slot)');