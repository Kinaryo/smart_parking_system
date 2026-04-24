<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\QRParkir;

class EspService
{
    /**
     * Ambil command untuk ESP
     */
    public function getCommand()
    {
        $command = Cache::pull('esp_data');

        $qr = QRParkir::where('aktif', true)
            ->where('status', 'tersedia')
            ->latest()
            ->first();

        return [
            'command' => $command['command'] ?? null,
            'payload' => $command['payload'] ?? null,
            'qr'      => $command['qr'] ?? ($qr->kode ?? null),
            'time'    => now()->format('H:i:s')
        ];
    }

    /**
     * Simpan command ke ESP
     */
    public function sendCommand($command, $payload = [], $extra = [])
    {
        Cache::put('esp_data', array_merge([
            'command' => $command,
            'payload' => $payload,
            'created_at' => now()->toDateTimeString()
        ], $extra), now()->addSeconds(10));
    }

    /**
     * Update slot dari ESP
     */
    public function updateSlot($newData)
    {
        $existingData = Cache::get('esp_slots_status', []);

        $updatedData = array_merge($existingData, $newData);

        Cache::put('esp_slots_status', $updatedData, now()->addHours(24));

        return $updatedData;
    }

    /**
     * Ambil slot
     */
    public function getSlots()
    {
        return Cache::get('esp_slots_status', []);
    }
}
