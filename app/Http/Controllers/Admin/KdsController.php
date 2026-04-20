<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class KdsController extends Controller
{
    /**
     * Display the Kitchen Display System dashboard.
     */
    public function index()
    {
        // Fetch active orders (Pending or Preparing)
        // Adjust order_status based on migration: 0=pending, 1=preparing, 2=ready
        $orders = OrderMaster::with(['items.product', 'table', 'customer'])
            ->whereIn('order_status', [0, 1]) 
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Admin/Kds/Index', [
            'orders' => $orders,
            'pageTitle' => 'Kitchen Display System'
        ]);
    }

    /**
     * Update the preparation status of a specific order item.
     */
    public function updateItemStatus(Request $request, OrderItem $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served'
        ]);

        $item->preparation_status = $validated['status'];
        $item->save();

        // If this was first item being prepared, move order status to 'preparing' (1)
        $order = $item->order;
        if ($order->order_status == 0 && $validated['status'] == 'preparing') {
            $order->order_status = 1;
            $order->save();
        }

        return back()->with('success', 'Item status updated.');
    }

    /**
     * Mark the entire order as ready.
     */
    public function readyOrder(OrderMaster $order)
    {
        DB::transaction(function () use ($order) {
            $order->order_status = 2; // ready
            $order->save();

            // Mark all items as ready if not already
            $order->items()->update(['preparation_status' => 'ready']);
        });

        return back()->with('success', 'Order marked as ready.');
    }
}
