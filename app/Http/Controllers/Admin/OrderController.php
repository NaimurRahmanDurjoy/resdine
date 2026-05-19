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
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller {
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $orders = OrderMaster::with(['customer', 'table'])
            ->when($search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'sort', 'direction']),
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
        try {
            $order = $this->orderService->createOrder($request->validated());
            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $order = OrderMaster::with(['items.product', 'customer', 'table', 'payments', 'invoice', 'delivery.driver'])
            ->findOrFail($id);

        $drivers = \App\Models\User::whereHas('role', function ($query) {
            $query->where('name', 'driver')->orWhere('name', 'Delivery Driver');
        })->get();

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'drivers' => $drivers,
            'pageTitle' => 'Order Details'
        ]);
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $order = OrderMaster::findOrFail($id);

        if ($order->order_type != 3) {
            return back()->withErrors(['error' => 'This is not a delivery order.']);
        }

        $delivery = $order->delivery;
        if (!$delivery) {
            $delivery = new \App\Models\OrderDelivery();
            $delivery->order_id = $order->id;
        }

        $delivery->driver_id = $request->driver_id;
        $delivery->save();

        return back()->with('success', 'Driver assigned successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer']);
        
        try {
            $this->orderService->updateStatus($id, $request->status);
            return back()->with('success', 'Order status updated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    public function refundItem(Request $request, $orderDetailId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->orderService->refundOrderItem($orderDetailId, $request->quantity);
            return back()->with('success', 'Item refunded successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to refund item: ' . $e->getMessage()]);
        }
    }
}


