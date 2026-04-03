<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\Customer;
use App\Models\RestaurantTable;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::where('status', 1)->get();
        // Return items with variants to easily calculate pricing on frontend
        $items = ProductItem::with('variants', 'category')->where('status', 1)->get();
        $customers = Customer::where('status', 1)->get();
        $tables = RestaurantTable::where('status', 1)->get(); // Assume status 1 means available or active

        return Inertia::render('Admin/Pos/Index', [
            'categories' => $categories,
            'items' => $items,
            'customers' => $customers,
            'tables' => $tables,
            'pageTitle' => 'Point of Sale'
        ]);
    }

    public function submitOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'table_id' => 'nullable|exists:restaurant_tables,id',
            'order_type' => 'required|integer', // 1=Dine-in, 2=Takeaway, etc.
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'payment_type' => 'required|string', // cash, card, etc.
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:product_items,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            DB::transaction(function () use ($validated, &$orderMaster) {
                // Determine branch_id from logged in admin (assuming simplified context, or fetch branch 1)
                $branchId = 1; 

                // Create Order Master
                $orderMaster = OrderMaster::create([
                    'user_id' => auth()->id(),
                    'branch_id' => $branchId,
                    'member_id' => $validated['customer_id'] ?? null,
                    'table_id' => $validated['table_id'] ?? null,
                    'order_type' => $validated['order_type'],
                    'subtotal' => $validated['subtotal'],
                    'discount' => $validated['discount'],
                    'total_amount' => $validated['total_amount'],
                    'collect_amount' => $validated['total_amount'], // Assuming fully paid
                    'due_amount' => 0,
                    'order_status' => 0, // Pending (feeds to KDS automatically)
                ]);

                // Create Order Items
                foreach ($validated['items'] as $itemData) {
                    OrderItem::create([
                        'order_id' => $orderMaster->id,
                        'item_id' => $itemData['item_id'],
                        'variant_id' => $itemData['variant_id'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'total' => $itemData['price'] * $itemData['quantity'],
                        'preparation_status' => 'pending' // Feeds KDS
                    ]);
                }

                // Create Payment Record (Simplified)
                OrderPayment::create([
                    'order_master_id' => $orderMaster->id,
                    'type' => $validated['payment_type'],
                    'status' => 'paid',
                    'amount' => $validated['total_amount'],
                    'date' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $orderMaster->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
