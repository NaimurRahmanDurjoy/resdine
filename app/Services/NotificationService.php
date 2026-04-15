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
                'groups' => []
            ];
        }

        $groups = [
            'inventory' => $this->getInventoryAlerts(),
            'orders' => $this->getOrderAlerts(), 
        ];

        $total = array_sum(array_column($groups, 'count'));

        return [
            'total' => $total,
            'groups' => $groups
        ];
    }

    /**
     * Calculate Inventory specific alerts (Low Stock + Expiring)
     */
    protected function getInventoryAlerts()
    {
        // 1. Low Stock Count
        $lowStockCount = StockSummary::whereHas('ingredient', function($q) {
            $q->whereColumn('stock_summary.current_stock', '<=', 'ingredients.min_stock');
        })->count();

        // 2. Expiring Items Count (Next 30 days)
        $expiringCount = DB::table('purchase_details')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->count();

        $count = $lowStockCount + $expiringCount;

        return [
            'count' => $count,
            'label' => 'Inventory Alerts',
            'details' => [
                'low_stock' => $lowStockCount,
                'expiring' => $expiringCount
            ],
            'url' => route('admin.stock.index')
        ];
    }

    /**
     * Future: Calculate Order specific alerts (New/Pending)
     */
    protected function getOrderAlerts()
    {
        $pendingCount = OrderMaster::where('order_status', 'pending')->count();

        return [
            'count' => $pendingCount,
            'label' => 'Pending Orders',
            'url' => route('admin.orders.index')
        ];
    }
}
