<?php

namespace App\Services;

use App\Models\StockSummary;
use App\Models\OrderMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Get all active alerts categorized by module.
     */
    public function getAlerts()
    {
        if (!Auth::check()) {
            return [
                'total' => 0,
                'groups' => [],
                'items' => []
            ];
        }

        $inventory = $this->getInventoryAlerts();
        $orders = $this->getOrderAlerts(); 

        $groups = [
            'inventory' => [
                'count' => $inventory['count'],
                'label' => $inventory['label'],
                'url' => $inventory['url']
            ],
            'orders' => [
                'count' => $orders['count'],
                'label' => $orders['label'],
                'url' => $orders['url']
            ]
        ];

        // Merge all items for the dropdown
        $items = array_merge($inventory['items'], $orders['items']);

        $total = array_sum(array_column($groups, 'count'));

        return [
            'total' => $total,
            'groups' => $groups,
            'items' => $items
        ];
    }

    /**
     * Calculate Inventory specific alerts (Low Stock + Expiring)
     */
    protected function getInventoryAlerts()
    {
        $items = [];

        // 1. Low Stock Logic
        $lowStockQuery = StockSummary::with('inventoryItem')
            ->whereHas('inventoryItem', function($q) {
                $q->whereColumn('stock_summary.current_stock', '<=', 'inventory_items.min_stock');
            });
            
        $lowStockCount = $lowStockQuery->count();
        
        $lowStocks = $lowStockQuery->limit(10)->get();
        foreach ($lowStocks as $stock) {
            if ($stock->inventoryItem) {
                $items[] = [
                    'id' => 'ls_' . $stock->id,
                    'type' => 'low_stock',
                    'message' => $stock->inventoryItem->name . ' is low in stock (' . $stock->current_stock . ' left)',
                    'url' => route('admin.stock.show', $stock->inventoryItem->id)
                ];
            }
        }

        // 2. Expiring Items Logic (Next 30 days)
        $expiringQuery = \App\Models\PurchaseDetail::with(['inventoryItem', 'purchase'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now());

        $expiringCount = $expiringQuery->count();

        $expiringItems = $expiringQuery->orderBy('expiry_date', 'asc')->limit(10)->get();
        foreach ($expiringItems as $detail) {
            if ($detail->inventoryItem && $detail->purchase) {
                $days = now()->diffInDays($detail->expiry_date, false);
                $dayStr = $days <= 0 ? 'today' : "in {$days} days";
                $items[] = [
                    'id' => 'exp_' . $detail->id,
                    'type' => 'expiring',
                    'message' => $detail->inventoryItem->name . " expires {$dayStr}",
                    'url' => route('admin.purchase.show', $detail->purchase_id)
                ];
            }
        }

        $count = $lowStockCount + $expiringCount;

        return [
            'count' => $count,
            'label' => 'Inventory Alerts',
            'url' => route('admin.stock.index'),
            'items' => $items
        ];
    }

    /**
     * Calculate Order specific alerts (New/Pending)
     */
    protected function getOrderAlerts()
    {
        $items = [];
        $pendingQuery = OrderMaster::where('order_status', 'pending');
        $pendingCount = $pendingQuery->count();

        $pendingOrders = $pendingQuery->orderBy('created_at', 'asc')->limit(10)->get();
        foreach ($pendingOrders as $order) {
            $items[] = [
                'id' => 'ord_' . $order->id,
                'type' => 'pending_order',
                'message' => 'New Order #' . $order->order_number . ' pending',
                'url' => route('admin.orders.show', $order->id)
            ];
        }

        return [
            'count' => $pendingCount,
            'label' => 'Pending Orders',
            'url' => route('admin.orders.index'),
            'items' => $items
        ];
    }
}
