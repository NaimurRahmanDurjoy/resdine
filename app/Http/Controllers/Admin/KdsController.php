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
    public function index(Request $request)
    {
        $deptId = $request->input('department_id');

        // Fetch active orders (Pending or Preparing)
        $query = OrderMaster::with(['items' => function ($q) use ($deptId) {
            $q->with('product');
            if ($deptId) {
                $q->whereHas('product', function ($pq) use ($deptId) {
                    $pq->where('department_id', $deptId);
                });
            }
        }, 'table', 'customer'])
            ->whereIn('order_status', [0, 1]);

        if ($deptId) {
            // Only show orders that have at least one item in this department
            $query->whereHas('items.product', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $orders = $query->orderBy('created_at', 'asc')->get();

        // Determine active branch (fall back to first branch)
        $activeBranchId = null;
        if (auth()->check()) {
            $user = auth()->user();
            $activeBranchId = method_exists($user, 'getActiveBranchId') ? $user->getActiveBranchId() : null;
        }

        if (!$activeBranchId) {
            $activeBranchId = \App\Models\Branch::first()?->id;
        }

        $departments = \App\Models\ResDepartment::when($activeBranchId, function ($q) use ($activeBranchId) {
            $q->where('branch_id', $activeBranchId);
        })->get();

        return Inertia::render('Admin/Kds/Index', [
            'orders' => $orders,
            'departments' => $departments,
            'currentDepartment' => $deptId,
            'pageTitle' => 'Kitchen Display System'
        ]);
    }

    /**
     * Display the Expo screen for waiters.
     */
    public function expo()
    {
        return Inertia::render('Admin/Kds/Expo', [
            'pageTitle' => 'Waiter Expo Display'
        ]);
    }

    /**
     * Fetch all ready items for the expo screen.
     */
    public function fetchReadyItems()
    {
        $items = OrderItem::with(['product', 'order.table', 'order.customer'])
            ->where('preparation_status', 'ready')
            ->orderBy('updated_at', 'asc')
            ->get();

        return response()->json($items);
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

        \App\Events\OrderStatusUpdated::dispatch($order, $item->id, $item->preparation_status);

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

        \App\Events\OrderStatusUpdated::dispatch($order, null, 'ready');

        return back()->with('success', 'Order marked as ready.');
    }
}
