<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class breakDown implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $order_id;
    public $shipment;
    public $origin;
    public function __construct($order_id, $shipment, $origin)
    {
        $this->order_id = $order_id;
        $this->shipment = $shipment;
        $this->origin = $origin;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('breakdown');
    }
    public function broadcastAs()
    {
        return 'sendBreakdown';
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order_id,
            'shipment' => $this->shipment,
            'origin' => $this->origin
        ];
    }
}
