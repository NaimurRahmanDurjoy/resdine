@extends('layouts.devAdmin')

@section('title', 'Software Menus')
@section('page-title', 'Software Menu List')
@section('page-description', 'Check current database table sizes and usage.')
@section('content')

@php
    $baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
@endphp

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Menus List</h1>
        <a href="{{ route($baseRoute . '.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
            <span class="material-symbols-outlined text-sm mr-1">add</span>
            Add Menu
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Name</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Route</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Icon</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Parent Menu</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Order</th>
                    <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Active</th>
                    <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
<tbody class="divide-y divide-gray-100 dark:divide-gray-700">
    @forelse ($menus as $menu)
        {{-- Parent Row --}}
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                {{ $menu->name }}
            </td>
            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $menu->route ?? '-' }}</td>
            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                @if($menu->icon)
                    <span class="material-symbols-outlined text-blue-500">{{ $menu->icon }}</span>
                @endif
            </td>
            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $menu->parent_id ?? '-' }}</td>
            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $menu->order }}</td>
            <td class="px-4 py-2 text-center">
                <span class="px-2 py-1 text-xs rounded {{ $menu->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $menu->is_active ? 'Yes' : 'No' }}
                </span>
            </td>
            <td class="px-4 py-2 text-center">
                <a href="{{ route($baseRoute . '.edit', $menu->id) }}" class="text-blue-600 hover:text-blue-800 mx-1">Edit</a>
                <form action="{{ route($baseRoute . '.destroy', $menu->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800 mx-1">Delete</button>
                </form>
            </td>
        </tr>

        {{-- Child Rows --}}
        @foreach ($menu->children as $child)
            <tr class=" hover:bg-gray-100 dark:hover:bg-gray-800">
                <td class="px-8 py-2 text-sm text-gray-700 dark:text-gray-300">
                     {{ $child->name }}
                </td>
                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $child->route ?? '-' }}</td>
                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                    @if($child->icon)
                        <span class="material-symbols-outlined text-blue-500">{{ $child->icon }}</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $menu->name }}</td>
                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $child->order }}</td>
                <td class="px-4 py-2 text-center">
                    <span class="px-2 py-1 text-xs rounded {{ $child->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $child->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-2 text-center">
                    <a href="{{ route($baseRoute . '.edit', $child->id) }}" class="text-blue-600 hover:text-blue-800 mx-1">Edit</a>
                    <form action="{{ route($baseRoute . '.destroy', $child->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800 mx-1">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    @empty
        <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                No menus found.
            </td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>
</div>
@endsection