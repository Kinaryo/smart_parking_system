<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SlotService
{
    public static function getSlots()
    {
        return Cache::get('esp_slots', []);
    }

    public static function getEmptySlot()
    {
        $slots = self::getSlots();

        foreach ($slots as $key => $slot) {
            if (isset($slot['status']) && $slot['status'] === 'kosong') {
                return $key;
            }
        }

        return null;
    }

    public static function updateSlots($data)
    {
        Cache::put('esp_slots', $data, now()->addMinutes(5));
    }
}