<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\MarketingCampaign;
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

        $defaultBranchId = \App\Models\Branch::first()?->id ?: 1;
        $settings = \App\Models\BranchSetting::where('branch_id', $defaultBranchId)->first();

        // Fetch active campaigns for the web menu
        $activeCampaigns = MarketingCampaign::where('is_active', true)
            ->where(function($query) {
                // If starts_at is in the next 12 hours, show it anyway to handle timezone offsets
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()->addHours(12));
            })
            ->where(function($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()->subHours(12));
            })
            ->orderBy('priority', 'desc')
            ->get();

        return Inertia::render('Web/Menu/Index', [
            'categories' => $categories,
            'items' => $items,
            'activeCampaigns' => $activeCampaigns,
            'branchSetting' => [
                'vat_percentage' => $settings ? (float) $settings->vat_percentage : 0.00,
                'service_charge_percentage' => $settings ? (float) $settings->service_charge_percentage : 0.00,
                'is_vat_inclusive' => $settings ? (bool) $settings->is_vat_inclusive : false,
            ]
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
            'order_type' => 'required|in:1,2,3', // 1=dine-in, 2=takeaway, 3=delivery
            'table_number' => 'nullable|string', 
            'delivery_address' => 'required_if:order_type,3|string|nullable|max:1000',
            'delivery_instructions' => 'nullable|string|max:1000',
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

                // Resolve or associate customer profile in CRM
                $customerId = null;
                if (!empty($validated['customer_phone'])) {
                    $customer = \App\Models\Customer::firstOrCreate(
                        ['phone' => $validated['customer_phone']],
                        ['name' => $validated['customer_name'], 'status' => 1]
                    );
                    $customerId = $customer->id;
                }

                $notes = "Guest: " . $validated['customer_name'] . " (" . $validated['customer_phone'] . ")";
                if ($validated['order_type'] == 1 && $validated['table_number']) {
                    $notes .= " - Table: " . $validated['table_number'];
                } elseif ($validated['order_type'] == 3) {
                    $notes .= " - [Delivery Order]";
                }

                // 1. Create Order
                $order = $this->orderService->createOrder([
                    'user_id' => $systemUserId,
                    'branch_id' => $branchId,
                    'customer_id' => $customerId, // Links order to CRM Customer profile
                    'table_id' => $tableId,
                    'order_type' => $validated['order_type'],
                    'items' => $validated['items'],
                    'discount' => 0,
                    'order_status' => 0, // Pending
                    'collect_amount' => 0, 
                    'notes' => $notes,
                ]);

                // If delivery, create delivery record
                if ($validated['order_type'] == 3) {
                    \App\Models\OrderDelivery::create([
                        'order_id' => $order->id,
                        'delivery_address' => $validated['delivery_address'],
                        'contact_number' => $validated['customer_phone'],
                        'special_instructions' => $validated['delivery_instructions'] ?? null,
                    ]);
                }

                // 2. Handle Instant/Online Payment
                $redirectUrl = null;
                if ($validated['payment_method']) {
                    $methodInt = (int) $validated['payment_method'];
                    
                    // Gateway integer mapping: 2 = Stripe, 3 = bKash, 4 = SSLCommerz
                    $gatewayMapping = [
                        2 => 'stripe',
                        3 => 'bkash',
                        4 => 'sslcommerz',
                    ];
                    
                    if (isset($gatewayMapping[$methodInt])) {
                        $gatewayName = $gatewayMapping[$methodInt];
                        try {
                            $manager = app(\App\Services\Payments\PaymentManager::class);
                            $gateway = $manager->driver($gatewayName);
                            $callbackUrl = route('payment.callback', ['gateway' => $gatewayName]);
                            
                            $paymentResult = $gateway->initiatePayment($order, (float) $validated['total_amount'], $callbackUrl);
                            
                            if ($paymentResult['success'] && isset($paymentResult['redirect_url'])) {
                                $redirectUrl = $paymentResult['redirect_url'];
                            }
                        } catch (\Exception $ex) {
                            \Log::error('Gateway initiation error during checkout: ' . $ex->getMessage());
                        }
                    } else {
                        // Standard Cash/Manual Payment
                        $this->paymentService->processPayment($order, [
                            'amount' => $validated['total_amount'],
                            'payment_method' => $methodInt,
                            'transaction_reference' => 'WEB-' . strtoupper(uniqid())
                        ]);
                    }
                }

                \App\Events\OrderCreated::dispatch($order);

                return response()->json([
                    'success' => true,
                    'message' => 'Your order has been placed successfully!',
                    'order_number' => $order->order_number,
                    'redirect_url' => $redirectUrl,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show tracking page for a specific order.
     */
    public function trackOrder(Request $request, $orderNumber)
    {
        $order = OrderMaster::with(['delivery.driver', 'items.product'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $data = [
            'order' => [
                'order_number' => $order->order_number,
                'order_status' => (int) $order->order_status,
                'order_type' => (int) $order->order_type,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->toIso8601String(),
                'delivery' => $order->delivery ? [
                    'delivery_address' => $order->delivery->delivery_address,
                    'contact_number' => $order->delivery->contact_number,
                    'special_instructions' => $order->delivery->special_instructions,
                    'driver_name' => $order->delivery->driver ? $order->delivery->driver->name : null,
                    'driver_phone' => $order->delivery->driver ? $order->delivery->driver->phone : null,
                ] : null,
            ]
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return Inertia::render('Web/Order/Tracking', $data);
    }
}
