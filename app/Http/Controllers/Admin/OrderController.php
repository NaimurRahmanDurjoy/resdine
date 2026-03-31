<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\RestaurantTable;
use App\Models\SalesInvoice;
use App\Services\LoyaltyService;
use App\Services\RecipeService;
use App\Http\Requests\Admin\Order\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller {
    protected $loyaltyService;
    protected $recipeService;

    public function __construct(LoyaltyService $loyaltyService, RecipeService $recipeService)
    {
        $this->loyaltyService = $loyaltyService;
        $this->recipeService = $recipeService;
    }

    public function index()
    {
        $orders = OrderMaster::with(['customer', 'table'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'pageTitle' => 'Orders'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Orders/Create', [
            'customers' => Customer::where('status', 1)->get(),
            'tables' => RestaurantTable::where('status', 1)->get(),
            'branches' => \App\Models\Branch::all(),
            'products' => \App\Models\ProductItem::with('variants')->get(),
            'pageTitle' => 'Create Order'
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $totalAmount = $subtotal - ($request->discount ?? 0);
            
            // Create Order Master
            $order = OrderMaster::create([
                'user_id' => auth()->id(),
                'branch_id' => $request->branch_id,
                'member_id' => $request->customer_id,
                'table_id' => $request->table_id,
                'order_type' => $request->order_type,
                'order_status' => 0, // Pending
                'subtotal' => $subtotal,
                'discount' => $request->discount ?? 0,
                'total_amount' => $totalAmount,
                'due_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Create Order Items
            foreach ($request->items as $itemData) {
                $itemTotal = $itemData['price'] * $itemData['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'modifiers' => $itemData['modifiers'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'unit_price' => $itemData['price'],
                    'total' => $itemTotal,
                    'total_price' => $itemTotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Deduct stock
                $this->recipeService->deductStockForProduct(
                    $itemData['item_id'],
                    null, // User said no need add variant
                    $itemData['quantity'],
                    'order',
                    $order->id,
                    $request->branch_id
                );
            }

            // Create Invoice
            SalesInvoice::create([
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'order_id' => $order->id,
                'customer_id' => $request->customer_id,
                'sub_total' => $subtotal,
                'discount' => $request->discount ?? 0,
                'due_amount' => $totalAmount,
                'grand_total' => $totalAmount,
                'status' => 1, // Pending
                'issued_at' => now(),
            ]);

            // Update Table Status
            if ($request->table_id) {
                RestaurantTable::where('id', $request->table_id)->update(['status' => 0]); // Occupied
            }

            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order created successfully.');
        });
    }

    public function show($id)
    {
        $order = OrderMaster::with(['items.product', 'customer', 'table', 'payments', 'invoice'])
            ->findOrFail($id);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'pageTitle' => 'Order Details'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer']);
        $order = OrderMaster::findOrFail($id);
        $order->update(['order_status' => $request->status]);

        // If completed or cancelled, free the table
        if (in_array($request->status, [4, 5]) && $order->table_id) {
            RestaurantTable::where('id', $order->table_id)->update(['status' => 1]); // Free
        }

        return back()->with('success', 'Order status updated.');
    }
}


