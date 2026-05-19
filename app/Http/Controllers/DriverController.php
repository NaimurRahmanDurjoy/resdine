<?php

namespace App\Http\Controllers;

use App\Models\OrderMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DriverController extends Controller
{
    /**
     * Display driver dashboard with assigned orders.
     */
    public function dashboard(Request $request)
    {
        $driverId = auth()->id();

        // Get orders assigned to this driver
        $orders = OrderMaster::whereHas('delivery', function ($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })
        ->with(['customer', 'delivery'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => (int) $order->order_status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->toIso8601String(),
                'customer_name' => $order->customer ? $order->customer->name : ($order->customer_name ?: 'Guest Customer'),
                'customer_phone' => $order->customer ? $order->customer->phone : ($order->customer_phone ?: 'N/A'),
                'delivery_address' => $order->delivery ? $order->delivery->delivery_address : 'N/A',
                'special_instructions' => $order->delivery ? $order->delivery->special_instructions : '',
            ];
        });

        return Inertia::render('Driver/Dashboard', [
            'orders' => $orders,
            'pageTitle' => 'Driver Dashboard'
        ]);
    }

    /**
     * Update status of assigned delivery order.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:3,4', // Out for Delivery, Completed
        ]);

        $order = OrderMaster::whereHas('delivery', function ($query) {
            $query->where('driver_id', auth()->id());
        })->findOrFail($id);

        $order->order_status = $request->status;
        $order->save();

        if ($request->status == 4 && $order->delivery) {
            $order->delivery->delivered_at = now();
            $order->delivery->save();
        }

        return back()->with('success', 'Order status updated successfully.');
    }
}
