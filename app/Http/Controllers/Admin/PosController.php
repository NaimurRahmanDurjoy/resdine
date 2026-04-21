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
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    protected $orderService;
    protected $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $categories = ProductCategory::where('status', 1)->get();
        // Return items with variants to easily calculate pricing on frontend
        $items = ProductItem::with('variants', 'category')->where('status', 1)->get();
        $customers = Customer::where('status', 1)->get();
        $tables = RestaurantTable::where('status', 1)->get(); 

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
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'table_id' => 'nullable|exists:restaurant_tables,id',
                'order_type' => 'required|integer', 
                'subtotal' => 'required|numeric',
                'discount' => 'required|numeric',
                'total_amount' => 'required|numeric',
                'payment_method' => 'required|integer', 
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:product_items,id',
                'items.*.variant_id' => 'nullable|exists:product_variants,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric',
            ]);

            return DB::transaction(function () use ($validated) {
                $branchId = auth()->user()->branch_id ?? 1;

                // 1. Create Order via Service (Handles Stock Deduction & Invoicing)
                $order = $this->orderService->createOrder([
                    'customer_id' => $validated['customer_id'],
                    'table_id' => $validated['table_id'],
                    'branch_id' => $branchId,
                    'order_type' => $validated['order_type'],
                    'items' => $validated['items'],
                    'discount' => $validated['discount'],
                    'order_status' => 0, // Pending
                ]);

                // 2. Record Payment via Service
                $this->paymentService->processPayment($order, [
                    'amount' => $validated['total_amount'],
                    'payment_method' => $validated['payment_method'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order placed and paid successfully!',
                    'order_id' => $order->id,
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
