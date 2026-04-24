<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QRUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $qr;

    public function __construct($qr)
    {
        $this->qr = $qr;
    }

    public function broadcastOn()
    {
        return new Channel('parking-channel');
    }

    public function broadcastAs()
    {
        return 'QRUpdated';
    }
}
