<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>

    <!-- Material Symbols (Google Fonts) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body class="bg-gray-50 flex font-sans" x-data="{ sidebarOpen: false, darkMode: false }" :class="{ 'dark': darkMode }">
    <!-- Global Toast Notifications -->
    <x-toast />
    <x-validation-toast />

    <!-- Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>
    <!-- <div x-show="sidebarOpen"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
        @click="sidebarOpen = false">
    </div> -->

    <!-- Sidebar -->
    @yield('sidebar')

    <div class="flex-1 flex flex-col min-h-screen lg:ml-64 transition-all">
        <!-- Header -->
        @yield('header')
        <!--Page Content -->
        <main class="flex-1 p-6 bg-gray-50 dark:bg-gray-900">
            <div id="content-wrapper" data-base-route="{{ base_url() }}">
                @yield('alerts')
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Modal -->
    <x-modal />


    <!-- Livewire Scripts -->
    @livewireScripts
    @stack('scripts')
    @yield('scripts')
</body>

</html>