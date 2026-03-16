<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\Customer;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller {
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    public function index()
    {
        return Inertia::render('Admin/Orders/Index', [
            'orders' => OrderMaster::with('customer')->latest()->get(),
            'pageTitle' => 'Orders'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Orders/Create', [
            'customers' => Customer::where('status', 1)->get(),
            'pageTitle' => 'Create Order'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'base_amount' => 'required|numeric|min:0',
            // ... other order validations
        ]);

        $amount = $validated['base_amount'];
        $customer = null;

        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            $amount = $this->loyaltyService->applyDiscount($customer, $amount);
        }

        $order = OrderMaster::create([
            'customer_id' => $request->customer_id,
            'total_amount' => $amount,
            'status' => 'completed', // Assuming immediate completion for demo
            // ... other fields
        ]);

        if ($customer) {
            $this->loyaltyService->accruePoints($order);
        }

        // Deduct stock based on recipes
        // Normally this would happen in a service, but for this demo logic:
        $recipeService = app(\App\Services\RecipeService::class);
        foreach ($request->items as $item) {
            $recipeService->deductStockForProduct(
                $item['product_id'], 
                $item['variant_id'] ?? null,
                $item['quantity'], 
                'invoice', 
                $order->id,
                1 // Default branch_id for now
            );
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order created and points accrued.');
    }

    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
}


