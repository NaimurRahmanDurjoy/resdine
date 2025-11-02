<aside
    class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-100 dark:bg-gray-800 shadow-lg transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="{ '-translate-x-full': !sidebarOpen }"
    x-cloak>
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-500 to-purple-600">
        <div class="text-2xl font-bold  text-white px-4 py-2 rounded">
            Resdine POS
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-4">
        <div class="space-y-2">
            @foreach($menus as $menu)
            <x-admin.sidebar-item :menu="$menu" />
            @endforeach
        </div>
    </nav>

    <!-- Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-500 text-center ">
            © {{ date('Y') }} Resdine POS
        </div>
    </div>
</aside>