@extends('layouts.admin')

@section('page-title', 'Menu Items')

@section('content')
<!-- Modern Container -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Menu Items</h1>
                <p class="text-gray-600">Manage your restaurant menu items</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="m-4 flex justify-between items-center">
        <form method="GET" action="{{ route('admin.menu.items.index') }}" class="flex space-x-2">
            <input
                type="text"
                name="search"
                placeholder="Search items..."
                value="{{ old('search', $search) }}"
                class="border px-2 py-1 rounded w-48 text-sm focus:outline-none focus:ring" />

            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                Search
            </button>
        </form>

        <a href="{{ route('admin.menu.items.create') }}" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2">
            <span class="material-symbols-outlined text-sm ">add</span>Add Items
        </a>
    </div>

    <x-list-table
        :headers="['Category', 'Name', 'Type', 'Image', 'Price', 'Status', 'Actions']"
        :items="$items"
        :pagination="$items"
        :sortable="[0 => 'category_id', 1 => 'name', 2 => 'type', 4 => 'price', ]">
        <x-slot:rows>
            @foreach ($items as $item)
            <tr class="hover:bg-indigo-50 transition">
                <td class="px-6 py-1 font-medium text-gray-900">{{ $item->category->name }}</td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $item->name }}</td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $item->type == 1 ? 'Regular' : ($item->type == 2 ? 'Combo' : ($item->type == 3 ? 'Comp.' : 'Unknown')) }}</td>

                <td class="px-6 py-1">
                    @if ($item->menu_img)
                    <img src="{{ Storage::url($item->menu_img) }}" alt="{{ $item->name }}" class="w-12 h-12 rounded-md object-cover border border-gray-200" />
                    @else
                    <span class="text-gray-400 italic">No image</span>
                    @endif
                </td>
                <td class="px-6 py-1 font-medium text-gray-900">{{ $item->price }}</td>
                <td class="px-6 py-1">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        {{ $item->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $item->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-1 whitespace-nowrap">
                    <div class="flex items-center space-x-4 text-base text-gray-600">
                        <!-- Edit -->
                        <a href="{{ route('admin.menu.items.edit', $item->id) }}" title="Edit" class="hover:text-indigo-600 transition">
                            <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">edit</span>
                        </a>
                        <!-- Delete -->
                        <form action="{{ route('admin.menu.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure to want delete?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="hover:text-red-600 transition bg-transparent border-0 p-0">
                                <span class="material-symbols-outlined text-red-600" style="font-size: 20px;">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </x-slot:rows>

        <x-slot:empty>
            <div class="flex flex-col items-center space-y-2">
                <span class="material-symbols-outlined text-gray-400 text-6xl mb-4">restaurant_menu</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No menu items found</h3>
                <p class="text-gray-600 mb-4">Get started by creating your first menu item.</p>
                <a href="{{ route('admin.menu.items.create') }}"
                    class="inline-block mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    + Add Menu Items
                </a>
            </div>
        </x-slot:empty>

        <x-slot:footer>
            <tr>
                <td colspan="7" class="px-6 py-2 text-sm text-gray-500">
                    <div class="flex justify-between items-center w-full">
                        <span>
                            Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} Items
                        </span>
                        <span>
                            Page {{ $items->currentPage() }} of {{ $items->lastPage() }}
                        </span>
                    </div>
                </td>

            </tr>
        </x-slot:footer>
    </x-list-table>

</div>
@endsection