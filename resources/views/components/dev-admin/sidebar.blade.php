<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="{ '-translate-x-full': !sidebarOpen }"
    x-cloak>

    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-lg">code</span>
            </div>
            <span class="text-xl font-bold text-gray-800 dark:text-white">DevAdmin</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-4">
        <div class="space-y-2">
            @foreach($menus as $menu)
                <x-dev-admin.sidebar-item :menu="$menu" />
            @endforeach
        </div>
    </nav>

    <!-- Bottom Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            <div>v{{ config('app.version', '1.0.0') }}</div>
            <div>Env: {{ app()->environment() }}</div>
        </div>
    </div>
</aside>