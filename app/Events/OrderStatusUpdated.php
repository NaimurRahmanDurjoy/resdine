<?php

namespace App\Events;

use App\Models\OrderMaster;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $itemId;
    public $status;

    /**
     * Create a new event instance.
     * If $itemId is null, it means the whole order status changed.
     */
    public function __construct(OrderMaster $order, $itemId = null, $status = null)
    {
        $this->order = $order;
        $this->itemId = $itemId;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kds.orders'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_status' => $this->order->order_status,
            'item_id' => $this->itemId,
            'item_status' => $this->status,
        ];
    }
}
