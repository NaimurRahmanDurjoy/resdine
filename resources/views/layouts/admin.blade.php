<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopNest POS Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.x.x/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white min-h-screen">
        <div class="p-6 text-center text-xl font-bold border-b border-gray-700">
            ShopNest Admin
        </div>
        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 hover:bg-gray-700">Dashboard</a>
            <a href="{{ route('admin.menu.index') }}" class="block px-6 py-3 hover:bg-gray-700">Menu</a>
            <a href="{{ route('admin.stock.index') }}" class="block px-6 py-3 hover:bg-gray-700">Stock</a>
            <a href="{{ route('admin.purchase.index') }}" class="block px-6 py-3 hover:bg-gray-700">Purchases</a>
            <a href="{{ route('admin.orders.index') }}" class="block px-6 py-3 hover:bg-gray-700">Orders</a>
            <a href="{{ route('admin.customers.index') }}" class="block px-6 py-3 hover:bg-gray-700">Customers</a>
            <a href="{{ route('admin.users.index') }}" class="block px-6 py-3 hover:bg-gray-700">Users & Roles</a>
            <a href="{{ route('admin.settings.index') }}" class="block px-6 py-3 hover:bg-gray-700">Settings</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <div class="text-lg font-semibold">Admin Panel</div>
            <div class="flex items-center space-x-4">
                <div class="text-gray-600">Hi, {{ auth()->user()->name }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Logout</button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
