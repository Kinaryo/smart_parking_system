<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;

class MqttService
{
    private static function getClient()
    {
        $server   = env('MQTT_HOST'); // Langsung ambil dari .env
        $port     = (int) env('MQTT_PORT', 1883);
        $clientId = 'laravel_client_' . uniqid();

        return new MqttClient($server, $port, $clientId);
    }

    private static function getSettings()
    {
        $settings = new ConnectionSettings();
        if (!empty(env('MQTT_AUTH_USERNAME'))) {
            $settings->setUsername(env('MQTT_AUTH_USERNAME'));
        }
        if (!empty(env('MQTT_AUTH_PASSWORD'))) {
            $settings->setPassword(env('MQTT_AUTH_PASSWORD'));
        }
        return $settings->setKeepAliveInterval(60);
    }

  public static function publish($topic, $payload, $qos = 0) // Tambahkan parameter qos
{
    try {
        $mqtt = self::getClient();
        $mqtt->connect(self::getSettings(), true);
        $message = is_array($payload) ? json_encode($payload) : $payload;
        $mqtt->publish($topic, $message, $qos); // Gunakan variabel qos
        $mqtt->disconnect();
        return true;
    } catch (\Exception $e) {
        Log::error("MQTT Publish Error: " . $e->getMessage());
        return false;
    }
}

public static function subscribe($topic, callable $callback, $qos = 0) // Tambahkan parameter qos
{
    try {
        $mqtt = self::getClient();
        $mqtt->connect(self::getSettings(), true);
        $mqtt->subscribe($topic, function ($topic, $message) use ($callback) {
            $callback($topic, $message);
        }, $qos); // Gunakan variabel qos
        $mqtt->loop(true);
    } catch (\Exception $e) {
        Log::error("MQTT Subscribe Error: " . $e->getMessage());
        throw $e;
    }
}
}
