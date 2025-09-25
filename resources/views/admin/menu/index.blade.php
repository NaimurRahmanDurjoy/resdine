@extends('layouts.admin')

@section('page-title', 'Menu Management')

@section('content')
<div class="flex justify-between mb-4">
    <h2 class="text-xl font-bold">Menu Items</h2>
    <a href="{{ route('admin.menu.create') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Add Item</a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-gray-500">Name</th>
                <th class="px-6 py-3 text-left text-gray-500">Type</th>
                <th class="px-6 py-3 text-left text-gray-500">Category</th>
                <th class="px-6 py-3 text-left text-gray-500">Price</th>
                <th class="px-6 py-3 text-left text-gray-500">Status</th>
                <th class="px-6 py-3 text-right text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($items as $item)
            <tr>
                <td class="px-6 py-4">{{ $item->name }}</td>
                <td class="px-6 py-4 capitalize">{{ $item->type }}</td>
                <td class="px-6 py-4">{{ $item->category->name }}</td>
                <td class="px-6 py-4">${{ $item->price }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded {{ $item->status ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                        {{ $item->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.menu.edit', $item->id) }}" class="text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
