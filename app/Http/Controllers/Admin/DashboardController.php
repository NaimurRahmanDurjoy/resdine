<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\Ingredient;
use App\Models\StockSummary;
use App\Models\ProductItem;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $yesterday = today()->subDay();

        // 1. Total Sales Today & Trend
        $todaySales = OrderMaster::whereDate('created_at', $today)
            ->where('order_status', 4) // Completed
            ->sum('total_amount');
            
        $yesterdaySales = OrderMaster::whereDate('created_at', $yesterday)
            ->where('order_status', 4)
            ->sum('total_amount');

        $salesDiff = $todaySales - $yesterdaySales;
        $salesTrendVal = $yesterdaySales > 0 ? round(($salesDiff / $yesterdaySales) * 100, 1) : 0;
        $salesTrendUp = $salesDiff >= 0;

        $data['totalSales'] = (float)$todaySales;
        $data['salesTrend'] = [
            'up' => $salesTrendUp,
            'value' => abs($salesTrendVal)
        ];

        // 2. Orders Today & Trend
        $todayOrders = OrderMaster::whereDate('created_at', $today)->count();
        $yesterdayOrders = OrderMaster::whereDate('created_at', $yesterday)->count();

        $ordersDiff = $todayOrders - $yesterdayOrders;
        $ordersTrendVal = $yesterdayOrders > 0 ? round(($ordersDiff / $yesterdayOrders) * 100, 1) : 0;
        $ordersTrendUp = $ordersDiff >= 0;

        $data['ordersToday'] = $todayOrders;
        $data['ordersTrend'] = [
            'up' => $ordersTrendUp,
            'value' => abs($ordersTrendVal)
        ];

        // 3. Avg Order Value & Trend
        $todayAov = $todayOrders > 0 ? ($todaySales / $todayOrders) : 0;
        $yesterdayAov = $yesterdayOrders > 0 ? ($yesterdaySales / $yesterdayOrders) : 0;

        $aovDiff = $todayAov - $yesterdayAov;
        $aovTrendVal = $yesterdayAov > 0 ? round(($aovDiff / $yesterdayAov) * 100, 1) : 0;
        $aovTrendUp = $aovDiff >= 0;

        $data['avgOrderValue'] = round($todayAov, 2);
        $data['aovTrend'] = [
            'up' => $aovTrendUp,
            'value' => abs($aovTrendVal)
        ];

        // 4. Top-selling Menu Item (today)
        $topItem = DB::table('order_details')
            ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereDate('created_at', $today)
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->first();

        if ($topItem) {
            $product = ProductItem::find($topItem->item_id);
            $data['topItemName'] = $product ? $product->name : 'N/A';
            $data['topItemSold'] = (int)$topItem->total_qty;
        } else {
            $data['topItemName'] = 'No sales today';
            $data['topItemSold'] = 0;
        }
        
        // Accurate Low Stock Alerts (based on ingredient.min_stock)
        $data['lowStock'] = StockSummary::with('inventoryItem')
            ->whereHas('inventoryItem', function($q) {
                $q->whereColumn('stock_summary.current_stock', '<=', 'inventory_items.min_stock');
            })
            ->take(5)
            ->get()
            ->map(function($stock) {
                return [
                    'ingredient_name' => $stock->inventoryItem->name,
                    'quantity' => $stock->current_stock,
                    'unit' => $stock->inventoryItem->unit->short_name ?? ''
                ];
            });

        // Expiring Items (within next 30 days)
        $data['expiringItems'] = DB::table('purchase_details')
            ->join('inventory_items', 'purchase_details.inventory_item_id', '=', 'inventory_items.id')
            ->whereNotNull('purchase_details.expiry_date')
            ->where('purchase_details.expiry_date', '<=', now()->addDays(30))
            ->where('purchase_details.expiry_date', '>=', now())
            ->select('inventory_items.name as ingredient_name', 'purchase_details.expiry_date', 'purchase_details.quantity')
            ->orderBy('purchase_details.expiry_date', 'asc')
            ->take(5)
            ->get();

        // Sales trend last 7 days
        $salesTrend = OrderMaster::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(total_amount) as total')
                        )
                        ->where('created_at', '>=', now()->subDays(6))
                        ->where('order_status', 4) // Completed
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        $data['labels'] = $salesTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('D'))->toArray();
        $data['salesData'] = $salesTrend->pluck('total')->toArray();

        // Recent Orders
        $data['recentOrders'] = OrderMaster::with(['customer', 'table'])
                                ->latest()
                                ->take(5)
                                ->get();

        $data['pageTitle'] = 'Dashboard';

        return Inertia::render('Admin/Dashboard', $data);
    }
}

