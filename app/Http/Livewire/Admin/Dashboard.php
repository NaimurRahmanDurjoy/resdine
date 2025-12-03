<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\OrderMaster;
use App\Models\StockSummary;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalSales = 0;
    public $ordersToday = 0;
    public $avgOrderValue = 0;
    public $topItemName = 'N/A';
    public $topItemQty = 0;
    public $lowStock;
    public $labels;
    public $salesData;

    public function mount()
    {
        $this->fetchDashboardData();
    }

    public function fetchDashboardData()
    {
        // Total Sales Today
        $this->totalSales = OrderMaster::whereDate('created_at', today())
            ->where('order_status', 'completed')
            ->sum('total_amount');

        // Orders Today
        $this->ordersToday = OrderMaster::whereDate('created_at', today())
            ->count();

        // Average Order Value
        $this->avgOrderValue = $this->ordersToday > 0
            ? round($this->totalSales / $this->ordersToday, 2)
            : 0;

        // Top-selling Menu Item Today
        $topItem = DB::table('order_details')
            ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereDate('created_at', today())
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->first();

        if ($topItem) {
            $menu = MenuItem::find($topItem->item_id);
            $this->topItemName = $menu ? $menu->name : 'N/A';
            $this->topItemQty = $topItem->total_qty;
        }

        // Low Stock Alerts
        $this->lowStock = StockSummary::where('current_stock', '<=', 5)->get();

        // Sales Trend Last 7 Days
        $salesTrend = OrderMaster::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', now()->subDays(6))
            ->where('order_status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->labels = $salesTrend->pluck('date')->map(fn($d) => $d->format('D'))->toArray();
        $this->salesData = $salesTrend->pluck('total')->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
            // ->layout('layouts.admin', ['pageTitle' => 'Dashboard Overview']);
    }
}
