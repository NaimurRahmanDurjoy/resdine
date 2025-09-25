@extends('layouts.admin')

@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm stat-card border-l-4 border-indigo-500">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-gray-500 text-sm font-medium">Total Sales Today</div>
                <div class="text-2xl font-bold mt-2">${{ number_format($totalSales, 2) }}</div>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                <span class="material-icons text-indigo-600">payments</span>
            </div>
        </div>
        <div class="mt-4 text-xs text-green-500 flex items-center">
            <span class="material-icons text-sm mr-1">trending_up</span>
            <span>12% increase from yesterday</span>
        </div>
    </div>

    <!-- Orders Today -->
    <div class="bg-white p-6 rounded-xl shadow-sm stat-card border-l-4 border-green-500">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-gray-500 text-sm font-medium">Orders Today</div>
                <div class="text-2xl font-bold mt-2">42</div>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <span class="material-icons text-green-600">receipt</span>
            </div>
        </div>
        <div class="mt-4 text-xs text-green-500 flex items-center">
            <span class="material-icons text-sm mr-1">trending_up</span>
            <span>5% increase from yesterday</span>
        </div>
    </div>

    <!-- Average Order Value -->
    <div class="bg-white p-6 rounded-xl shadow-sm stat-card border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-gray-500 text-sm font-medium">Avg. Order Value</div>
                <div class="text-2xl font-bold mt-2">$24.50</div>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <span class="material-icons text-blue-600">attach_money</span>
            </div>
        </div>
        <div class="mt-4 text-xs text-red-500 flex items-center">
            <span class="material-icons text-sm mr-1">trending_down</span>
            <span>3% decrease from yesterday</span>
        </div>
    </div>

    <!-- Top-Selling Item -->
    <div class="bg-white p-6 rounded-xl shadow-sm stat-card border-l-4 border-purple-500">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-gray-500 text-sm font-medium">Top-Selling Item</div>
                <div class="text-xl font-semibold mt-2">{{ $topItemName }}</div>
                <div class="text-sm text-gray-500 mt-1">28 sold today</div>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <span class="material-icons text-purple-600">local_fire_department</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Additional Info -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sales Chart -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div class="text-lg font-semibold text-gray-800">Sales Trend (Last 7 Days)</div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium">Week</button>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Month</button>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Year</button>
            </div>
        </div>
        <canvas id="salesChart" height="250"></canvas>
    </div>

    <!-- Low Stock & Recent Activity -->
    <div class="space-y-6">
        <!-- Low Stock Alerts -->
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-semibold text-gray-800">Low Stock Alerts</div>
                <span class="material-icons text-amber-500">warning</span>
            </div>
            <ul class="space-y-3">
                @forelse($lowStock as $stock)
                <li class="flex justify-between items-center p-2 hover:bg-amber-50 rounded-lg">
                    <div>
                        <div class="font-medium">{{ $stock->ingredient_name }}</div>
                        <div class="text-xs text-gray-500">Only {{ $stock->quantity }} left</div>
                    </div>
                    <span class="material-icons text-amber-500 text-sm">arrow_forward</span>
                </li>
                @empty
                <li class="text-center text-gray-500 py-4">No low stock items</li>
                @endforelse
            </ul>
            <a href="{{ route('admin.stock.index') }}" class="w-full mt-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition block text-center">
                View All Stock
            </a>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="text-lg font-semibold text-gray-800 mb-4">Recent Orders</div>
            <ul class="space-y-3">
                <li class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium">Order #1245</div>
                        <div class="text-xs text-gray-500">Table 4 • 12:30 PM</div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Completed</span>
                </li>
                <li class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium">Order #1244</div>
                        <div class="text-xs text-gray-500">Takeaway • 12:15 PM</div>
                    </div>
                    <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full">Preparing</span>
                </li>
                <li class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium">Order #1243</div>
                        <div class="text-xs text-gray-500">Table 2 • 11:45 AM</div>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Served</span>
                </li>
            </ul>
            <a href="{{ route('admin.orders.index') }}" class="w-full mt-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition block text-center">
                View All Orders
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Sales ($)',
                data: @json($salesData),
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    },
                    padding: 10,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            }
        }
    });
</script>
@endsection