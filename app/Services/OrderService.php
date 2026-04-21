<?php

namespace App\Services;

use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\SalesInvoice;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $recipeService;
    protected $notificationService;

    public function __construct(RecipeService $recipeService, NotificationService $notificationService)
    {
        $this->recipeService = $recipeService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new order with items and initial invoice.
     */
    public function createOrder(array $data): OrderMaster
    {
        return DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $discount = $data['discount'] ?? 0;
            $totalAmount = $subtotal - $discount;

            // 1. Create Order Master
            $order = OrderMaster::create([
                'user_id' => $data['user_id'] ?? auth()->id(),
                'branch_id' => $data['branch_id'],
                'member_id' => $data['customer_id'] ?? null,
                'table_id' => $data['table_id'] ?? null,
                'order_type' => $data['order_type'],
                'order_status' => $data['order_status'] ?? 0, // 0 = Pending
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'due_amount' => $totalAmount,
                'collect_amount' => $data['collect_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Create Order Items & Deduct Stock
            foreach ($data['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'modifiers' => $itemData['modifiers'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['price'],
                    'total_price' => $itemData['price'] * $itemData['quantity'],
                ]);

                // Centralized Stock Deduction
                $this->recipeService->deductStockForProduct(
                    $itemData['item_id'],
                    $itemData['variant_id'] ?? null,
                    $itemData['quantity'],
                    'order',
                    $order->id,
                    $data['branch_id']
                );
            }

            // 3. Create Invoice
            SalesInvoice::create([
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'order_id' => $order->id,
                'customer_id' => $data['customer_id'] ?? null,
                'sub_total' => $subtotal,
                'discount' => $discount,
                'due_amount' => $order->due_amount,
                'grand_total' => $totalAmount,
                'status' => 1, // Pending
                'issued_at' => now(),
            ]);

            // 4. Update Table Status
            if ($order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 0]); // Occupied
            }

            // 5. Notify (e.g., KDS)
            // $this->notificationService->notifyNewOrder($order);

            return $order;
        });
    }

    /**
     * Update order status and handle side effects.
     */
    public function updateStatus(int $orderId, int $status): OrderMaster
    {
        return DB::transaction(function () use ($orderId, $status) {
            $order = OrderMaster::findOrFail($orderId);
            $oldStatus = $order->order_status;
            
            $order->update(['order_status' => $status]);

            // If cancelled (e.g., status 5), restore stock
            if ($status == 5 && $oldStatus != 5) {
                $this->restoreStock($order);
            }

            // If completed or cancelled, free the table
            if (in_array($status, [4, 5]) && $order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 1]); // Free
            }

            return $order;
        });
    }

    /**
     * Restore stock for a cancelled/returned order.
     */
    protected function restoreStock(OrderMaster $order)
    {
        foreach ($order->items as $item) {
            $this->recipeService->restoreStockForProduct(
                $item->item_id,
                $item->variant_id,
                $item->quantity,
                'order_cancellation',
                $order->id,
                $order->branch_id
            );
        }
    }
}
