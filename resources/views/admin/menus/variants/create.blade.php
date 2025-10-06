@extends('layouts.admin')

@section('page-title', 'Add Menu variants')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm" x-data="{ selectedType: '{{ old('type', 1) }}' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Menu variants</h1>
        <p class="text-gray-600">Create a new menu variants for your restaurant</p>
    </div>

    <!-- @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif -->

    <form action="{{ route('admin.menu.variants.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Menu Item *</label>
                    <select name="item_id" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            required>
                        <option value="">Select Item</option>
                        @foreach($menuItems as $menuItem)
                            <option value="{{ $menuItem->id }}" {{ old('item_id') == $menuItem->id ? 'selected' : '' }}>
                                {{ $menuItem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                               required>
                    </div>
                </div>

            </div>
        </div>


        <!-- Form Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                <span class="material-icons mr-2 text-sm">save</span> Save Variants
            </button>
            <a href="{{ route('admin.menu.variants.index') }}" 
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
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
