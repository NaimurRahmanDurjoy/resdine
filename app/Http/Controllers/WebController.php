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
            'table_number' => 'nullable|string', // If dine-in
            'subtotal' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:product_items,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            DB::transaction(function () use ($validated, &$orderMaster) {
                // Determine a default branch and user (e.g. system default) for public orders
                $branchId = 1;
                $systemUserId = 1;

                $orderMaster = OrderMaster::create([
                    'user_id' => $systemUserId,
                    'branch_id' => $branchId,
                    'order_type' => $validated['order_type'],
                    // Store guest info in notes or create a guest custom field (simplifying by putting in notes)
                    // Or preferably, we should have a 'guest_name' and 'guest_phone' columns, but lacking those, we will find/create a generic customer
                    'subtotal' => $validated['subtotal'],
                    'discount' => 0,
                    'total_amount' => $validated['total_amount'],
                    'collect_amount' => 0, // Unpaid
                    'due_amount' => $validated['total_amount'],
                    'order_status' => 0, // Pending
                ]);

                foreach ($validated['items'] as $itemData) {
                    OrderItem::create([
                        'order_id' => $orderMaster->id,
                        'item_id' => $itemData['item_id'],
                        'variant_id' => $itemData['variant_id'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['price'],
                        'total_price' => $itemData['price'] * $itemData['quantity'],
                        'preparation_status' => 'pending'
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Your order has been placed successfully!',
                'order_number' => $orderMaster->order_number,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
