@extends('layouts.admin')

@section('page-title', 'Add Menu Category')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm" x-data="{ selectedType: '{{ old('type', 1) }}' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Menu Category</h1>
        <p class="text-gray-600">Create a new menu category for your restaurant</p>
    </div>

    <form action="{{ route('admin.menu.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->


            <div class="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <x-image-upload-preview name="category_img" :existingImage="$category->image ?? null" />
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="status" value="1" id="status"
                        {{ old('status', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="status" class="ml-2 block text-sm text-gray-700">Active Category</label>
                </div>
            </div>
        </div>


        <!-- Form Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                <span class="material-symbols-outlined mr-2 text-sm">save</span> Save Category
            </button>
            <a href="{{ route('admin.menu.categories.index') }}"
                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition" data-ajax-link>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection