<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderSoundAlert implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vendorId;
    public $message;
    public $location;

    public function __construct($vendorId, $location, $message)
    {
        $this->vendorId = $vendorId;
        $this->location = $location;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('kitchen-channel.'.$this->vendorId);
    }

    public function broadcastAs()
    {
        return 'order.updated';
    }
}