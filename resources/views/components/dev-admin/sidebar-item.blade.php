<li x-data="{ open: {{ ($menu->route && request()->routeIs($menu->route . '*')) ? 'true' : 'false' }} }">
    @php
        $hasChildren = $menu->childrenRecursive && $menu->childrenRecursive->isNotEmpty();
        $routeExists = $menu->route && \Illuminate\Support\Facades\Route::has($menu->route);
        $isActive = $menu->route ? request()->routeIs($menu->route . '*') : false;
    @endphp

    @if ($hasChildren)
        <!-- Parent Menu -->
        <button @click="open = !open"
                class="flex items-center justify-between w-full px-4 py-2 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
            <div class="flex items-center">
                <span class="material-symbols-outlined w-5 mr-3">{{ $menu->icon }}</span>
                <span>{{ $menu->name }}</span>
            </div>
            <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
        </button>

        <ul x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
            @foreach ($menu->childrenRecursive as $child)
                @include('components.dev-admin.sidebar-item', ['menu' => $child])
            @endforeach
        </ul>
    @else
        <!-- Single Link -->
        <a href="{{ $routeExists ? route($menu->route) : '#' }}"
           class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-300 rounded hover:bg-blue-50 dark:hover:bg-gray-700
           {{ $isActive ? 'text-blue-600 dark:text-blue-400' : '' }}">
            <span class="material-symbols-outlined w-5 mr-3">{{ $menu->icon }}</span>
            <span>{{ $menu->name }}</span>
        </a>
    @endif
</li>