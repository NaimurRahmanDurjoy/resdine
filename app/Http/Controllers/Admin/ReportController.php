<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\StockLedger;
use App\Models\InventoryItem;
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
        $topItems = OrderItem::select('item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as revenue'))
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

    /**
     * ABC Inventory Analysis
     * Classifies inventory items by annual consumption value:
     *   A = Top 80% of value (critical items)
     *   B = Next 15% of value (moderate items)
     *   C = Remaining 5% (low-value items)
     */
    public function abcAnalysis(Request $request)
    {
        $months = $request->input('months', 12);
        $since = Carbon::now()->subMonths($months);

        // Get consumption value per item from stock ledger (qty_out * unit_cost)
        $items = DB::table('stock_ledger')
            ->join('inventory_items', 'stock_ledger.inventory_item_id', '=', 'inventory_items.id')
            ->select(
                'inventory_items.id',
                'inventory_items.name',
                DB::raw('SUM(stock_ledger.qty_out * stock_ledger.unit_cost) as consumption_value'),
                DB::raw('SUM(stock_ledger.qty_out) as total_consumed')
            )
            ->where('stock_ledger.transaction_date', '>=', $since)
            ->where('stock_ledger.qty_out', '>', 0)
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->orderByDesc('consumption_value')
            ->get();

        $totalValue = $items->sum('consumption_value');
        $runningTotal = 0;
        $classified = [];

        foreach ($items as $item) {
            $runningTotal += $item->consumption_value;
            $cumulativePercent = $totalValue > 0 ? ($runningTotal / $totalValue) * 100 : 0;

            if ($cumulativePercent <= 80) {
                $category = 'A';
            } elseif ($cumulativePercent <= 95) {
                $category = 'B';
            } else {
                $category = 'C';
            }

            $classified[] = [
                'id' => $item->id,
                'name' => $item->name,
                'consumption_value' => round($item->consumption_value, 2),
                'total_consumed' => round($item->total_consumed, 2),
                'percent_of_total' => $totalValue > 0 ? round(($item->consumption_value / $totalValue) * 100, 2) : 0,
                'category' => $category
            ];
        }

        return Inertia::render('Admin/Reports/AbcAnalysis', [
            'items' => $classified,
            'totalValue' => round($totalValue, 2),
            'months' => $months,
            'pageTitle' => 'ABC Inventory Analysis'
        ]);
    }

    /**
     * Hourly Sales Heatmap
     * Returns a 7 (days) x 24 (hours) matrix of order counts/revenue.
     */
    public function hourlyHeatmap(Request $request)
    {
        $weeks = $request->input('weeks', 4);
        $since = Carbon::now()->subWeeks($weeks);

        $data = OrderMaster::select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', $since)
            ->groupBy('day_of_week', 'hour')
            ->get();

        // Build a 7x24 matrix
        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $matrix = [];
        foreach ($dayNames as $idx => $dayName) {
            $matrix[$dayName] = array_fill(0, 24, ['orders' => 0, 'revenue' => 0]);
        }

        foreach ($data as $row) {
            $dayIndex = $row->day_of_week - 1; // MySQL DAYOFWEEK: 1=Sun, 7=Sat
            $dayName = $dayNames[$dayIndex] ?? 'Sun';
            $matrix[$dayName][$row->hour] = [
                'orders' => $row->order_count,
                'revenue' => (float)$row->revenue
            ];
        }

        return Inertia::render('Admin/Reports/HourlyHeatmap', [
            'matrix' => $matrix,
            'dayNames' => $dayNames,
            'weeks' => $weeks,
            'pageTitle' => 'Hourly Sales Heatmap'
        ]);
    }

    /**
     * Staff Performance Report
     * Aggregates revenue, order counts, and average ticket size per staff member.
     */
    public function staffPerformance(Request $request)
    {
        $months = $request->input('months', 1);
        $since = Carbon::now()->subMonths($months);

        $staff = OrderMaster::select(
                'created_by',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as avg_ticket')
            )
            ->where('created_at', '>=', $since)
            ->whereNotNull('created_by')
            ->groupBy('created_by')
            ->orderByDesc('total_revenue')
            ->get();

        // Attach user info
        $userIds = $staff->pluck('created_by');
        $users = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');

        $result = $staff->map(function ($s) use ($users) {
            return [
                'user_id' => $s->created_by,
                'name' => $users[$s->created_by] ?? 'Unknown',
                'total_orders' => $s->total_orders,
                'total_revenue' => round($s->total_revenue, 2),
                'avg_ticket' => round($s->avg_ticket, 2),
            ];
        });

        return Inertia::render('Admin/Reports/StaffPerformance', [
            'staff' => $result,
            'months' => $months,
            'pageTitle' => 'Staff Performance'
        ]);
    }
}
