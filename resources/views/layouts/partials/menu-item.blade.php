@php
    $hasChildren = $menu->childrenRecursive->count() > 0;

    if (!function_exists('isMenuActive')) {
        function isMenuActive($menu) {
            if ($menu->route && (request()->routeIs($menu->route) || request()->is($menu->route.'*'))) {
                return true;
            }
            foreach ($menu->childrenRecursive as $child) {
                if (isMenuActive($child)) return true;
            }
            return false;
        }
    }

    $isActive = isMenuActive($menu);
@endphp

<div x-data="{ open: @json($isActive) }">
    <a href="{{ $menu->route ? route($menu->route) : '#' }}"
       @if($hasChildren)
           @click.prevent="open = !open"
       @endif
       class="flex items-center px-4 py-3 hover:bg-indigo-50 rounded-lg transition sidebar-link {{ $isActive ? 'active' : '' }}">
        <span class="material-icons mr-3 text-indigo-500">{{ $menu->icon }}</span>
        <span class="font-medium">{{ $menu->name }}</span>
        @if($hasChildren)
            <span class="material-icons ml-auto">expand_more</span>
        @endif
    </a>

    @if($hasChildren)
        <div x-show="open" class="ml-6 mt-1 space-y-1">
            @foreach($menu->childrenRecursive as $child)
                @include('layouts.partials.menu-item', ['menu' => $child])
            @endforeach
        </div>
    @endif
</div>
