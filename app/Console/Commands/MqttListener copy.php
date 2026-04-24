<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Events\SlotUpdated;
use App\Models\QRParkir;
use Illuminate\Support\Str;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Smart Parking: MQTT Master (Slot & QR Boolean)';

    public function handle()
    {
        $server   = env('MQTT_HOST', 'broker.hivemq.com');
        $port     = (int) env('MQTT_PORT', 1883);
        $clientId = 'laravel_parking_master_' . Str::random(5);

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $settings = (new ConnectionSettings)->setKeepAliveInterval(60);

            if (!empty(env('MQTT_AUTH_USERNAME'))) {
                $settings->setUsername(env('MQTT_AUTH_USERNAME'))
                         ->setPassword(env('MQTT_AUTH_PASSWORD'));
            }

            $mqtt->connect($settings, true);
            $this->info("MQTT Connected! Listening...");

            /**
             * 1. HANDLER UPDATE SLOT (ESP -> LARAVEL -> MQTT CONFIRM)
             */
            $mqtt->subscribe('smartparking/univ123/slots', function ($topic, $message) use ($mqtt) {
                $data = json_decode($message, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Simpan data
                    Cache::put('esp_slots_status', $data);
                    broadcast(new SlotUpdated(['type' => 'status_update', 'slots' => $data]));

                    // KIRIM KONFIRMASI AGAR MUNCUL DI LOG MQTT
                    $confirm = json_encode([
                        "command"   => "SlotData", // Nama command berbeda
                        "status"    => true,       // Boolean
                        "time"      => date('Y-m-d H:i:s')
                    ]);

                    $mqtt->publish('smartparking/univ123/commands', $confirm, 1, false);
                    $this->info("\n[SLOT] Berhasil diupdate & Konfirmasi dikirim.");
                }
            }, 1);

            /**
             * 2. HANDLER REQUEST QR (ESP -> LARAVEL -> MQTT QR)
             */
            $mqtt->subscribe('smartparking/univ123/request_qr', function ($topic, $message) use ($mqtt) {
                $request = json_decode($message, true);
                
                if (isset($request['action']) && $request['action'] === 'GET_QR') {
                    
                    $qr = QRParkir::where('status', 'tersedia')->where('aktif', true)->latest()->first();

                    if (!$qr) {
                        $kode = 'PKR-' . Str::upper(Str::random(8));
                        QRParkir::create(['kode' => $kode, 'status' => 'tersedia', 'aktif' => true]);
                    } else {
                        $kode = $qr->kode;
                    }

                    // RESPONSE QR DATA
                    $payload = json_encode([
                        "command"   => "QRData",
                        "qr"        => $kode,
                        "status"    => true, 
                        "is_active" => true
                    ]);

                    $mqtt->publish('smartparking/univ123/commands', $payload, 1, false);
                    $this->info("\n[QR] Request diproses. Kode: $kode");
                }
            }, 1);

            $mqtt->loop(true);

        } catch (\Exception $e) {
            $this->error("MQTT ERROR: " . $e->getMessage());
            sleep(5);
            return $this->handle();
        }
    }
}