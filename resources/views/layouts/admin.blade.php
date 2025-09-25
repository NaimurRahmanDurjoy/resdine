<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resdine POS Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-link.active {
            background-color: rgba(99, 102, 241, 0.1);
            border-right: 3px solid #6366f1;
            color: #6366f1;
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-50 flex font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-white text-gray-700 min-h-screen flex flex-col shadow-lg border-r border-gray-200">
        <!-- Brand -->
        <div class="p-6 text-center text-2xl font-bold tracking-wide border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
            <span>Resdine</span> POS
        </div>

        <!-- Navigation -->
        <nav class="flex-1 mt-6 space-y-1 px-3">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-indigo-500">dashboard</span> 
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.menu.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-green-500">restaurant_menu</span> 
                <span class="font-medium">Menu</span>
            </a>
            <a href="{{ route('admin.stock.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-blue-500">inventory</span> 
                <span class="font-medium">Stock</span>
            </a>
            <a href="{{ route('admin.purchase.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.purchase.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-amber-500">shopping_cart</span> 
                <span class="font-medium">Purchases</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-purple-500">receipt_long</span> 
                <span class="font-medium">Orders</span>
            </a>
            <a href="{{ route('admin.customers.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-pink-500">people</span> 
                <span class="font-medium">Customers</span>
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-red-500">admin_panel_settings</span> 
                <span class="font-medium">Users & Roles</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="material-icons mr-3 text-gray-500">settings</span> 
                <span class="font-medium">Settings</span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 text-sm text-gray-500 text-center">
            © {{ date('Y') }} Resdine POS
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <div class="text-xl font-semibold text-gray-800">
                @yield('page-title', 'Admin Panel')
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <span class="material-icons text-gray-500">notifications</span>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">3</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="material-icons text-indigo-600 text-sm">person</span>
                    </div>
                    <div class="text-gray-700 font-medium">Hi, {{ auth()->user()->name }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center">
                        <span class="material-icons mr-1 text-sm">logout</span> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 bg-gray-50">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>