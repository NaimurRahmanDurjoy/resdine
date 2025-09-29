@extends('layouts.admin')

@section('page-title', 'Menu Category')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Menu Category</h1>
            <p class="text-gray-600">Manage your restaurant menu Category</p>
        </div>
        <a href="{{ route('admin.menu.categories.create') }}" 
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
            <span class="material-icons mr-2 text-sm">add</span> Add Categories
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="datatable min-w-full divide-y divide-gray-200" data-nonorderable-columns='@json($nonSortableColumns)'>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($categories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover rounded">
                        @else
                            <span class="text-gray-400">No image</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.menu.categories.edit', $category->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 px-3 py-1 rounded hover:bg-indigo-50 transition flex items-center">
                                <span class="material-icons mr-1 text-sm">edit</span> Edit
                            </a>
                            <form action="{{ route('admin.menu.categories.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                        class="text-red-600 hover:text-red-900 px-3 py-1 rounded hover:bg-red-50 transition flex items-center">
                                    <span class="material-icons mr-1 text-sm">delete</span> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    @if($categories->isEmpty())
        <div class="text-center py-12">
            <span class="material-icons text-gray-400 text-6xl mb-4">category</span>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No menu categories found</h3>
            <p class="text-gray-600 mb-4">Get started by creating your first menu category.</p>
            <a href="{{ route('admin.menu.categories.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Add Your First Category
            </a>
        </div>
    @endif
</div>
@endsection