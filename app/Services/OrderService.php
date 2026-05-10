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

            // 1. Strict Stock Validation
            $allErrors = [];
            foreach ($data['items'] as $itemData) {
                $itemErrors = $this->recipeService->validateStockForRecipe(
                    $itemData['item_id'],
                    $itemData['variant_id'] ?? null,
                    $itemData['quantity'],
                    $data['branch_id']
                );
                if (!empty($itemErrors)) {
                    $allErrors = array_merge($allErrors, $itemErrors);
                }
            }

            if (!empty($allErrors)) {
                // Group by ingredient to avoid duplicates and join with commas
                $uniqueErrors = array_unique($allErrors);
                throw new \Exception("Insufficient stock: " . implode(", ", $uniqueErrors));
            }

            // 2. Create Order Master
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

            // 3. Create Order Items & Deduct Stock
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

            // 4. Create Invoice
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

            // 5. Update Table Status
            if ($order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 0]); // Occupied
            }

            // 6. Notify (e.g., KDS)
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
                $item->quantity - $item->refunded_qty, // Only restore what wasn't already refunded
                'order_cancellation',
                $order->id,
                $order->branch_id
            );
        }
    }

    /**
     * Refund a specific quantity of an order item.
     */
    public function refundOrderItem(int $orderDetailId, int $quantityToRefund): OrderItem
    {
        return DB::transaction(function () use ($orderDetailId, $quantityToRefund) {
            $item = OrderItem::with('order')->findOrFail($orderDetailId);
            $order = $item->order;

            if ($quantityToRefund > ($item->quantity - $item->refunded_qty)) {
                throw new \Exception("Refund quantity exceeds available quantity.");
            }

            // 1. Update refunded quantity
            $item->increment('refunded_qty', $quantityToRefund);

            // 2. Recalculate Order Master Totals
            $refundAmount = $item->unit_price * $quantityToRefund;
            
            $order->subtotal -= $refundAmount;
            $order->total_amount -= $refundAmount;
            
            // Adjust due_amount but don't go below 0 (excess would be a credit/cash refund)
            $order->due_amount = max(0, $order->due_amount - $refundAmount);
            $order->save();

            // 3. Restore Stock
            $this->recipeService->restoreStockForProduct(
                $item->item_id,
                $item->variant_id,
                $quantityToRefund,
                'order_refund',
                $order->id,
                $order->branch_id
            );

            // 4. Update Invoice
            $invoice = SalesInvoice::where('order_id', $order->id)->first();
            if ($invoice) {
                $invoice->sub_total = $order->subtotal;
                $invoice->grand_total = $order->total_amount;
                $invoice->due_amount = $order->due_amount;
                $invoice->save();
            }

            return $item;
        });
    }
}
