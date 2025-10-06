<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Resdine POS Admin')</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex font-sans">

    <!-- Global Toast Notifications -->
    <x-toast />

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <div class="text-xl font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </div>
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <span class="material-icons text-gray-500">notifications</span>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">3</span>
                </div>
                <!-- User info -->
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="material-icons text-indigo-600 text-sm">person</span>
                    </div>
                    <div class="text-gray-700 font-medium">Hi, {{ auth()->user()->name }}</div>
                </div>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center transition-colors">
                        <span class="material-icons mr-1 text-sm">logout</span> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 bg-gray-50">
            <x-validation-toast />
            @yield('content')
        </main>
    </div>

    <x-modal />

    @yield('scripts')

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    </script>

</body>

</html>