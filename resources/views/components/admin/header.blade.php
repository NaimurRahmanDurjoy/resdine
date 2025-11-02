<header class="bg-white shadow-sm px-6 h-16 flex justify-between items-center border-b border-gray-200">
    <button class="lg:hidden pr-4 rounded-md" @click="sidebarOpen = true">
        <span class="material-symbols-outlined">menu</span>
    </button>

    <!-- Page title -->
    <h1 class="text-xl font-semibold text-gray-800">
        @yield('page-title', 'Dashboard')
    </h1>

    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <div class="relative">
            <span class="material-symbols-outlined text-gray-500">notifications</span>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">3</span>
        </div>

        <!-- User info -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-sm">person</span>
            </div>
            <span class="text-gray-700 font-medium truncate max-w-xs">Hi, {{ auth()->user()->name }}</span>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center transition-colors">
                <span class="material-symbols-outlined mr-1 text-sm">logout</span> Logout
            </button>
        </form>
    </div>
</header>
