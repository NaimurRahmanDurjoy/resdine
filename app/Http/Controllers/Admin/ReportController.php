<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // High Level Stats
        $salesToday = OrderMaster::whereDate('created_at', $today)->sum('total_amount');
        $salesMonth = OrderMaster::where('created_at', '>=', $thisMonth)->sum('total_amount');
        $ordersTodayCount = OrderMaster::whereDate('created_at', $today)->count();
        $pendingOrdersCount = OrderMaster::where('order_status', 0)->count();

        // Recent Activity
        $recentOrders = OrderMaster::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Top Selling Items (all time simple query)
        $topItems = OrderItem::select('item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as revenue'))
            ->with('product:id,name,image_url')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->take(4)
            ->get();

        // 7-day Sales Trend Matrix
        $trendData = [];
        $maxSales = 0;
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyTotal = OrderMaster::whereDate('created_at', $date)->sum('total_amount');
            
            if ($dailyTotal > $maxSales) {
                $maxSales = $dailyTotal;
            }

            $trendData[] = [
                'day' => $date->format('D'),
                'date' => $date->format('M d'),
                'total' => (float) $dailyTotal
            ];
        }

        return Inertia::render('Admin/Reports/Index', [
            'stats' => [
                'salesToday' => $salesToday,
                'salesMonth' => $salesMonth,
                'ordersToday' => $ordersTodayCount,
                'pendingOrders' => $pendingOrdersCount
            ],
            'recentOrders' => $recentOrders,
            'topItems' => $topItems,
            'trendData' => $trendData,
            'maxTrendSales' => $maxSales > 0 ? $maxSales : 1, // Avoid division by zero
            'pageTitle' => 'Reports & Analytics'
        ]);
    }
}
