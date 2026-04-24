<?php

namespace App\Events;

use App\Models\ParkirTransaksi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransaksiSelesai implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaksi;

    public function __construct(ParkirTransaksi $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    public function broadcastOn()
    {
        // Broadcast ke channel khusus transaksi ini
        return new Channel('transaksi.' . $this->transaksi->id);
    }

    public function broadcastAs()
    {
        return 'selesai';
    }
}