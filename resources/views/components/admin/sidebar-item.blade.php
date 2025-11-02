@props(['menu'])

@php
    $hasChildren = $menu['hasChildren'];
    $isActive = $menu['isActive'];
@endphp

@if($hasChildren)
    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ $isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
            <div class="flex items-center">
                @if($menu['model']->icon)
                    <span class="material-symbols-outlined w-5 mr-3">{{ $menu['model']->icon }}</span>
                @endif
                <span>{{ $menu['model']->name }}</span>
            </div>
            <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
        </button>

        <div x-show="open" x-collapse class="ml-4 mt-2 space-y-1">
            @foreach($menu['children'] ?? [] as $child)
                <x-admin.sidebar-item :menu="$child" />
            @endforeach
        </div>
    </div>
@else
    <a href="{{ $menu['url'] }}"
       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ $isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
        @if($menu['model']->icon)
            <span class="material-symbols-outlined w-5 mr-3">{{ $menu['model']->icon }}</span>
        @endif
        <span>{{ $menu['model']->name }}</span>
    </a>
@endif
