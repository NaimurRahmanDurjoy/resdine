@php
    $hasChildren = $menu->childrenRecursive && $menu->childrenRecursive->count() > 0;
    $isActive = $menu->route ? request()->routeIs($menu->route) : false;
@endphp

<div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between w-full rounded-lg transition-colors
                {{ $isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
        
        <!-- Clickable label -->
        <a href="{{ $menu->route ? route($menu->route) : '#' }}" class="flex items-center px-4 py-2 w-full">
            @if($menu->icon)
                <span class="material-symbols-outlined inline-block w-5 mr-2">{{ $menu->icon }}</span>
            @endif
            <span>{{ $menu->name }}</span>
        </a>

        <!-- Toggle button for submenu -->
        @if($hasChildren)
            <button @click="open = !open"
                    class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">
                    expand_more
                </span>
            </button>
        @endif
    </div>

    <!-- Submenu -->
    @if($hasChildren)
        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
            @foreach($menu->childrenRecursive as $child)
                <x-dev-admin.sidebar-item :menu="$child" />
            @endforeach
        </div>
    @endif
</div>
