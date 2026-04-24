<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlotUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $slots;

    public function __construct($slots)
    {
        $this->slots = $slots;
    }

    public function broadcastOn()
    {
        return new Channel('parking-channel'); // konsisten semua event
    }

    public function broadcastAs()
    {
        return 'SlotUpdated';
    }

    public function broadcastWith()
    {
        return [
            'slots' => $this->slots
        ];
    }
}