@extends('layouts.devAdmin')

@section('page-title', 'Software Menu')

@section('content')

@php
$baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
@endphp
<!-- Modern Container -->
<!-- Modern Container -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Software Menu List</h1>
                <p class="text-gray-600 text-sm mt-1">Manage your software menu list</p>
            </div>
        </div>
    </div>

    <!-- Search and Add Button -->
    <div class="m-4 flex justify-between items-center">
        <form method="GET" action="{{ route($baseRoute . '.index') }}" class="flex space-x-2">
            <input
                type="text"
                name="search"
                placeholder="Search menus..."
                value="{{ old('search', $search) }}"
                class="border px-2 py-1 rounded w-48 text-sm focus:outline-none focus:ring" />

            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                Search
            </button>
        </form>

        <a href="{{ route($baseRoute . '.create') }}" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Add Menu</span>
        </a>
    </div>

    <!-- List Table Component -->
    <x-list-table
        :headers="['Menu Name', 'Route', 'Icon', 'Parent Menu', 'Order', 'Status', 'Actions']"
        :items="$menus"
        :pagination="$menus"
        :sortable="[
            0 => 'name', 
            1 => 'route', 
            2 => 'parent_id', 
            4 => 'order'
        ]">
        
        <!-- Rows Slot - This is required by the component -->
        <x-slot:rows>
            @forelse ($menus as $menu)
                <!-- Parent Row -->
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-1 font-semibold text-gray-800">
                        {{ $menu->name }}
                    </td>
                    <td class="px-6 py-1 text-gray-600">{{ $menu->route ?? '-' }}</td>
                    <td class="px-6 py-1 text-gray-600">
                        @if($menu->icon)
                        <span class="material-symbols-outlined text-blue-500">{{ $menu->icon }}</span>
                        @else
                        -
                        @endif
                    </td>
                    <td class="px-6 py-1 text-gray-600">-</td>
                    <td class="px-6 py-1 text-gray-600">{{ $menu->order }}</td>
                    <td class="px-6 py-1 text-center">
                        <span class="px-2 py-1 text-xs rounded {{ $menu->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $menu->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-6 py-1 text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route($baseRoute . '.edit', $menu->id) }}" 
                               class="text-blue-600 hover:text-blue-800 transition">
                                <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">edit</span>
                            </a>
                            <form action="{{ route($baseRoute . '.destroy', $menu->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure?')" 
                                        class="text-red-600 hover:text-red-800 transition">
                                    <span class="material-symbols-outlined text-red-600" style="font-size: 20px;">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Child Rows -->
                @foreach ($menu->children as $child)
                <tr class="hover:bg-gray-50 bg-gray-50">
                    <td class="px-10 py-1 text-gray-700">
                        {{ $child->name }}
                    </td>
                    <td class="px-6 py-1 text-gray-600">{{ $child->route ?? '-' }}</td>
                    <td class="px-6 py-1 text-gray-600">
                        @if($child->icon)
                        <span class="material-symbols-outlined text-blue-500">{{ $child->icon }}</span>
                        @else
                        -
                        @endif
                    </td>
                    <td class="px-6 py-1 text-gray-600">{{ $menu->name }}</td>
                    <td class="px-6 py-1 text-gray-600">{{ $child->order }}</td>
                    <td class="px-6 py-1 text-center">
                        <span class="px-2 py-1 text-xs rounded {{ $child->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $child->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-6 py-1 text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route($baseRoute . '.edit', $child->id) }}" 
                               class="text-blue-600 hover:text-blue-800 transition">
                                <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">edit</span>
                            </a>
                            <form action="{{ route($baseRoute . '.destroy', $child->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure?')" 
                                        class="text-red-600 hover:text-red-800 transition">
                                    <span class="material-symbols-outlined text-red-600" style="font-size: 20px;">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            @empty
                <!-- Empty state is handled by the component's empty slot -->
            @endforelse
        </x-slot:rows>

        <!-- Empty Slot -->
        <x-slot:empty>
            <div class="flex flex-col items-center space-y-2 py-8">
                <span class="material-symbols-outlined text-gray-400 text-6xl mb-4">menu</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No menus found</h3>
                <p class="text-gray-600 mb-4">Get started by creating your first menu.</p>
                <a href="{{ route($baseRoute .'.create') }}"
                    class="inline-block mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    + Add Menu
                </a>
            </div>
        </x-slot:empty>

        <!-- Footer Slot -->
        <x-slot:footer>
            <tr>
                <td colspan="7" class="px-6 py-2 text-sm text-gray-500 bg-gray-50">
                    <div class="flex justify-between items-center w-full">
                        <span>
                            Showing {{ $menus->firstItem() ?? 0 }} to {{ $menus->lastItem() ?? 0 }} of {{ $menus->total() }} menus
                        </span>
                        <span>
                            Page {{ $menus->currentPage() }} of {{ $menus->lastPage() }}
                        </span>
                    </div>
                </td>
            </tr>
        </x-slot:footer>
    </x-list-table>
</div>
@endsection