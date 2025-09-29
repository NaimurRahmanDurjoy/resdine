@extends('layouts.admin')

@section('page-title', 'Edit Menu Category')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Menu Category</h1>
        <p class="text-gray-600">Update the menu category details</p>
    </div>

    <form action="{{ route('admin.menu.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                       required>
            </div>

            <!-- Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                @if($category->image)
                    <x-image-upload-preview name="category_img" :existingImage="$category->image ?? null" />
                @endif
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="status" value="1" id="status"
                       {{ old('status', $category->status) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="status" class="ml-2 block text-sm text-gray-700">Active Category</label>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Update Category
            </button>
            <a href="{{ route('admin.menu.categories.index') }}"
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
