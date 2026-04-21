<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    protected $orderService;

    public function __construct(\App\Services\OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display the digital menu.
     */
    public function menu()
    {
        $categories = ProductCategory::where('status', 1)->get();
        // Return active items
        $items = ProductItem::with('variants', 'category')
            ->where('status', 1)
            ->get();

        return Inertia::render('Web/Menu/Index', [
            'categories' => $categories,
            'items' => $items,
        ]);
    }

    /**
     * Process customer guest order.
     */
    public function submitOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:1,2', // 1=dine-in, 2=takeaway
            'table_number' => 'nullable|string', 
            'subtotal' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:product_items,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            // Determine a default branch and user for public orders
            $branchId = 1; 
            $systemUserId = 1;

            $notes = "Guest: " . $validated['customer_name'] . " (" . $validated['customer_phone'] . ")";

            $order = $this->orderService->createOrder([
                'user_id' => $systemUserId,
                'branch_id' => $branchId,
                'order_type' => $validated['order_type'],
                'items' => $validated['items'],
                'discount' => 0,
                'order_status' => 0, // Pending
                'collect_amount' => 0, // Unpaid
                'notes' => $notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your order has been placed successfully!',
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
