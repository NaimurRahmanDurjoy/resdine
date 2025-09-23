@extends('layouts.admin')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Total Sales Card -->
    <div class="bg-white p-6 rounded shadow">
        <div class="text-gray-500">Total Sales Today</div>
        <div class="text-2xl font-bold">${{ number_format($totalSales, 2) }}</div>
    </div>

    <!-- Top-Selling Item -->
    <div class="bg-white p-6 rounded shadow">
        <div class="text-gray-500">Top-Selling Item</div>
        <div class="text-xl font-semibold">{{ $topItemName }}</div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="bg-white p-6 rounded shadow">
        <div class="text-gray-500">Low Stock Ingredients</div>
        <ul class="mt-2 list-disc pl-5 text-red-600">
            @forelse($lowStock as $stock)
                <li>{{ $stock->ingredient_name }} ({{ $stock->quantity }} left)</li>
            @empty
                <li>No low stock items</li>
            @endforelse
        </ul>
    </div>

</div>

<!-- Charts Section -->
<div class="mt-8 bg-white p-6 rounded shadow">
    <div class="text-gray-500 mb-4 font-semibold">Sales Trend (Last 7 Days)</div>
    <canvas id="salesChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Sales ($)',
                data: @json($salesData),
                backgroundColor: 'rgba(34,197,94,0.2)',
                borderColor: 'rgba(34,197,94,1)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection
