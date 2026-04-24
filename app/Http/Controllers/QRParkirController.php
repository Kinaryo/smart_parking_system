<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\QRParkir;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use App\Events\SlotUpdated;
use App\Services\MqttService;

class QRParkirController extends Controller
{

    private function getOrGenerateQR()
    {
        $qr = QRParkir::where('status', 'tersedia')
            ->where('aktif', true)
            ->latest()
            ->first();

        if (!$qr) {
            $qr = QRParkir::create([
                'kode'   => 'PKR-' . Str::upper(Str::random(8)),
                'status' => 'tersedia',
                'aktif'  => true
            ]);
        }

        QRParkir::where('id', '!=', $qr->id)
            ->where('status', 'tersedia')
            ->update(['aktif' => false]);

        return $qr;
    }


    public function generate()
    {
        $kode = 'PKR-' . Str::upper(Str::random(8));

        $qr = QRParkir::create([
            'kode'   => $kode,
            'status' => 'tersedia',
            'aktif'  => true
        ]);

        // Matikan QR lama
        QRParkir::where('id', '!=', $qr->id)
            ->where('status', 'tersedia')
            ->update(['aktif' => false]);

        $mqttPayload = [
            'command'   => 'UPDATE_QR',
            'qr'        => $qr->kode,
            'status'    => 1,
            'is_active' => 1 
        ];

        MqttService::publish("smartparking/univ123/commands", $mqttPayload);

        broadcast(new SlotUpdated([
            'type' => 'qr_update',
            'qr'   => $qr->kode
        ]));

        return response()->json([
            'success' => true,
            'kode'    => $qr->kode,
            'message' => 'QR baru berhasil dibuat dan dikirim'
        ]);
    }

    public function ajaxQRShow()
    {
        $qr = $this->getOrGenerateQR();

        $svg = QrCode::size(100)->generate($qr->kode);

        return response()->json([
            'success' => true,
            'svg'     => (string) $svg,
            'kode'    => $qr->kode,
            'status'  => $qr->status,
            'updated_at' => $qr->updated_at->toDateTimeString()
        ]);
    }


    public function showQR()
    {
        $qr = $this->getOrGenerateQR();

        // Kirim MQTT untuk sinkronisasi tampilan alat jika perlu
        MqttService::publish("smartparking/univ123/commands", [
            'command'   => 'QRData',
            'qr'        => $qr->kode,
            'status'    => 1,
            'is_active' => 1
        ]);

        return view('qr.show', compact('qr'));
    }

    public function handleMqttRequest($message)
    {
        $data = json_decode($message, true);

        if (isset($data['action']) && $data['action'] == 'GET_QR') {
            
            $qr = $this->getOrGenerateQR();

            MqttService::publish("smartparking/univ123/commands", [
                'command'   => 'QRData',
                'qr'        => $qr->kode,
                'status'    => 1,
                'is_active' => 1
            ]);
            
            Log::info("[MQTT Controller] Membalas request QR: " . $qr->kode);
        }
    }
}