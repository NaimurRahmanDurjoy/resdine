@props(['menu'])

@php
    $hasChildren = $menu->children->count() > 0;
    $isActive = $menu->isActive();
@endphp

@if($hasChildren)
    <!-- Menu with children (dropdown) -->
    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
        <button @click="open = !open"
            class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ $isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
            <div class="flex items-center">
                @if($menu->icon)
                    <span class="material-symbols-outlined w-5 mr-3">{{ $menu->icon }}</span>
                @endif
                <span>{{ $menu->name }}</span>
            </div>
            <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
        </button>

        <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
            @foreach($menu->children as $child)
                <x-dev-admin.sidebar-item :menu="$child" />
            @endforeach
        </div>
    </div>
@else
    <!-- Single menu item -->
    <a href="{{ $menu->getUrl() }}"
        class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ $isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
        @if($menu->icon)
            <span class="material-symbols-outlined w-5 mr-3">{{ $menu->icon }}</span>
        @endif
        <span>{{ $menu->name }}</span>
    </a>
@endif