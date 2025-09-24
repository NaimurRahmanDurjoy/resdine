<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\StockSummary;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        // Total Sales Today
        $totalSales = OrderMaster::whereDate('created_at', today())
                            ->where('order_status', 'completed')
                            ->sum('total_amount');

        // Top-selling Menu Item (today)
        $topItem = DB::table('order_details')
                     ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
                     ->whereDate('created_at', today())
                     ->groupBy('item_id')
                     ->orderByDesc('total_qty')
                     ->first();

        $topItemName = $topItem ? MenuItem::find($topItem->menu_id)->name : 'N/A';

        // Low Stock Alerts
        $lowStock = StockSummary::where('current_stock', '<=', 5)->get(); // threshold = 5

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

        $labels = $salesTrend->pluck('date')->map(fn($d) => $d->format('D'))->toArray();
        $salesData = $salesTrend->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'totalSales', 'topItemName', 'lowStock', 'labels', 'salesData'
        ));
    }
}

