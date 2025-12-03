<div>
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
                    <span class="material-symbols-outlined text-indigo-600">payments</span>
                </div>
            </div>
        </div>

        <!-- Top-Selling Item -->
        <div class="bg-white p-6 rounded-xl shadow-sm stat-card border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Top-Selling Item</div>
                    <div class="text-xl font-semibold mt-2">{{ $topItemName }}</div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="text-lg font-semibold mb-4">Low Stock Alerts</div>
            <ul>
                @forelse($lowStock as $stock)
                    <li>{{ $stock->ingredient_name }} - Only {{ $stock->quantity }} left</li>
                @empty
                    <li>No low stock items</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="text-lg font-semibold mb-4">Sales Trend (Last 7 Days)</div>
        <canvas id="salesChart" height="250"></canvas>
    </div>

    @push('scripts')
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
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
                    fill: true
                }]
            },
            options: { responsive: true }
        });
    </script>
    @endpush
</div>
