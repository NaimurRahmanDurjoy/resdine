<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Resdine POS Admin')</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Replace Material Icons with Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex font-sans" x-data="{ sidebarOpen: false }">

    <!-- Global Toast Notifications -->
    <x-toast />
    <div x-show="sidebarOpen"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
        @click="sidebarOpen = false">
    </div>

    <!-- Sidebar -->
    <x-admin.sidebar class="flex-shrink-0" />

    <div class="flex-1 flex flex-col min-h-screen lg:ml-64 transition-all duration-300">
        <!-- Topbar -->
        <x-admin.header />

        <!-- Page content -->
        <main class="flex-1 p-6 bg-gray-50">
            <x-validation-toast />
            @yield('content')
        </main>
    </div>

    <x-modal />

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @yield('scripts')

</body>

</html>