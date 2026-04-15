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
        // Total Sales Today
        $data['totalSales'] = OrderMaster::whereDate('created_at', today())
                            ->where('order_status', 'completed')
                            ->sum('total_amount');

        // Top-selling Menu Item (today)
        $topItem = DB::table('order_details')
                     ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
                     ->whereDate('created_at', today())
                     ->groupBy('item_id')
                     ->orderByDesc('total_qty')
                     ->first();

        $data['topItemName'] = $topItem ? ProductItem::find($topItem->item_id)->name : 'N/A';
        
        // Accurate Low Stock Alerts (based on ingredient.min_stock)
        $data['lowStock'] = StockSummary::with('ingredient')
            ->whereHas('ingredient', function($q) {
                $q->whereColumn('stock_summary.current_stock', '<=', 'ingredients.min_stock');
            })
            ->take(5)
            ->get()
            ->map(function($stock) {
                return [
                    'ingredient_name' => $stock->ingredient->name,
                    'quantity' => $stock->current_stock,
                    'unit' => $stock->ingredient->unit->short_name ?? ''
                ];
            });

        // Expiring Items (within next 30 days)
        $data['expiringItems'] = DB::table('purchase_details')
            ->join('ingredients', 'purchase_details.ingredients_id', '=', 'ingredients.id')
            ->whereNotNull('purchase_details.expiry_date')
            ->where('purchase_details.expiry_date', '<=', now()->addDays(30))
            ->where('purchase_details.expiry_date', '>=', now())
            ->select('ingredients.name as ingredient_name', 'purchase_details.expiry_date', 'purchase_details.quantity')
            ->orderBy('purchase_details.expiry_date', 'asc')
            ->take(5)
            ->get();

        // Sales trend last 7 days
        $salesTrend = OrderMaster::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(total_amount) as total')
                        )
                        ->where('created_at', '>=', now()->subDays(6))
                        ->where('order_status', 'completed')
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

