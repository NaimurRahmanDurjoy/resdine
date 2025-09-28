<aside class="w-64 bg-white text-gray-700 min-h-screen flex flex-col shadow-lg border-r border-gray-200">
    <!-- Brand -->
    <div class="p-6 text-center text-2xl font-bold tracking-wide border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
        <span>Resdine</span> POS
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-6 space-y-1 px-3">
        @php
            $menus = Cache::remember('user_menus_' . auth()->id(), 3600, function () {
                return auth()->user()->accessibleMenus();
            });
        @endphp

        @foreach($menus as $menu)
            @include('layouts.partials.menu-item', ['menu' => $menu])
        @endforeach
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-200 text-sm text-gray-500 text-center">
        © {{ date('Y') }} Resdine POS
    </div>
</aside>
