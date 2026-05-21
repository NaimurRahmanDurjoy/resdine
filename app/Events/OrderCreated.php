<?php

namespace App\Events;

use App\Models\OrderMaster;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderMaster $order)
    {
        $this->order = $order->loadMissing(['items.product', 'table', 'customer']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
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
        return 'order.created';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'order_status' => $this->order->order_status,
            'created_at' => $this->order->created_at,
            'table' => $this->order->table ? ['name' => $this->order->table->name] : null,
            'customer' => $this->order->customer ? ['name' => $this->order->customer->name] : null,
            'items' => $this->order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'preparation_status' => $item->preparation_status,
                    'modifiers' => $item->modifiers,
                    'product' => $item->product ? [
                        'name' => $item->product->name,
                        'department_id' => $item->product->department_id,
                    ] : null,
                ];
            })->toArray()
        ];
    }
}
