<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Notification;

class SendOrderAlert
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        // Find all users who are admins
        $admins = User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();

        // Send a notification to all admins
        Notification::send($admins, new AdminAlert(
            'pending_order',
            'New Order #' . $event->order->order_number . ' pending',
            route('admin.orders.show', $event->order->id)
        ));
    }
}
