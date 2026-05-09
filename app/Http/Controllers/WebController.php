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
    protected $paymentService;

    public function __construct(\App\Services\OrderService $orderService, \App\Services\PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
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
            'payment_method' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:product_items,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // Determine a default branch and user for public orders
                $branchId = 1; 
                $systemUserId = 1;
                $tableId = null;

                // Resolve table number to table_id if it's a dine-in order
                if ($validated['order_type'] == 1 && !empty($validated['table_number'])) {
                    $table = \App\Models\RestaurantTable::where('name', $validated['table_number'])->first();
                    if ($table) {
                        $tableId = $table->id;
                        $branchId = $table->branch_id; // Inherit branch from table
                    }
                }

                $notes = "Guest: " . $validated['customer_name'] . " (" . $validated['customer_phone'] . ")";
                if ($validated['table_number']) {
                    $notes .= " - Table: " . $validated['table_number'];
                }

                // 1. Create Order
                $order = $this->orderService->createOrder([
                    'user_id' => $systemUserId,
                    'branch_id' => $branchId,
                    'table_id' => $tableId,
                    'order_type' => $validated['order_type'],
                    'items' => $validated['items'],
                    'discount' => 0,
                    'order_status' => 0, // Pending
                    'collect_amount' => 0, 
                    'notes' => $notes,
                ]);

                // 2. Handle Instant Payment for Takeaway/Delivery
                if ($validated['payment_method']) {
                    $this->paymentService->processPayment(
                        $order->id,
                        $validated['total_amount'],
                        $validated['payment_method'],
                        'WEB-' . strtoupper(uniqid())
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Your order has been placed successfully!',
                    'order_number' => $order->order_number,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
