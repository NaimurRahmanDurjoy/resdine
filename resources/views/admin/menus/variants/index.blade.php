@extends('layouts.admin')

@section('page-title', 'Menu Variants')

@section('content')
<!-- Modern Container -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Menu Variants</h1>
                <p class="text-gray-600 text-sm mt-1">Manage your restaurant menu variants</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="m-4 flex justify-between items-center">
        <form method="GET" action="{{ route('admin.menu.variants.index') }}" class="flex space-x-2">
            <input
                type="text"
                name="search"
                placeholder="Search variants..."
                value="{{ old('search', $search) }}"
                class="border px-2 py-1 rounded w-48 text-sm focus:outline-none focus:ring" />

            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                Search
            </button>
        </form>

        <a href="{{ route('admin.menu.variants.create') }}" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2">
            <span class="material-symbols-outlined text-sm ">add</span>Add Variants
        </a>
    </div>

    <x-list-table
        :headers="['Name', 'Item', 'Price', 'Actions']"
        :items="$variants"
        :pagination="$variants"
        :sortable="[0 => 'name', 1 => 'item_id', 2 => 'price',]">
        <x-slot:rows>
            @foreach ($variants as $variant)
            <tr class="hover:bg-indigo-50 transition">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $variant->name }}</td>
                <td class="px-6 py-4 font-medium text-gray-900">{{ $variant->menuItem->name }}</td>
                <td class="px-6 py-4 font-medium text-gray-900">{{ $variant->price }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-4 text-base text-gray-600">
                        <!-- Edit -->
                        <a href="{{ route('admin.menu.variants.edit', $variant->id) }}" title="Edit" class="hover:text-indigo-600 transition">
                            <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">edit</span>
                        </a>
                        <!-- Delete -->
                        <form action="{{ route('admin.menu.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Are you sure to want delete?')">
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
                <span class="material-symbols-outlined text-gray-300 text-6xl">variants</span>
                <h3 class="text-lg font-semibold text-gray-700">No variants found</h3>
                <p class="text-gray-500 text-sm">Start by adding a new variants to your menu.</p>
                <a href="{{ route('admin.menu.variants.create') }}"
                    class="inline-block mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    + Add variants
                </a>
            </div>
        </x-slot:empty>

        <x-slot:footer>
            <tr>
                <td colspan="4" class="px-6 py-2 text-sm text-gray-500">
                    <div class="flex justify-between items-center w-full">
                        <span>
                            Showing {{ $variants->firstItem() ?? 0 }} to {{ $variants->lastItem() ?? 0 }} variants
                        </span>
                        <span>
                            From {{ $variants->total() }} variants
                        </span>
                    </div>
                </td>
            </tr>
        </x-slot:footer>
    </x-list-table>

</div>
@endsection