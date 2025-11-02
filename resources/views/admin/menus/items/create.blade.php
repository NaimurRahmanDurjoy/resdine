@extends('layouts.admin')

@section('page-title', 'Add Menu Item')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm" x-data="{ selectedType: '{{ old('type', 1) }}' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Menu Item</h1>
        <p class="text-gray-600">Create a new menu item for your restaurant</p>
    </div>

    <form action="{{ route('admin.menu.items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <select name="type"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        x-model="selectedType"
                        required>
                        <option value="1">Regular Menu Item</option>
                        <option value="2">Combo Meal</option>
                        <option value="3">Complementary Item</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="Enter menu item name"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="Describe your menu item...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="status" value="1" id="status"
                            {{ old('status', true) ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="status" class="ml-2 block text-sm text-gray-700">Active</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700">Featured</label>
                    </div>
                </div>
            </div>

            <!-- Price & Details -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                            class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="0.00"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0"
                            class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit *</label>
                    <select name="unit_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Res Department *</label>
                    <select name="department_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required>
                        <option value="">Select Res. Dept</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <x-image-upload-preview name="menu_img" :existingImage="$item->image ?? null" />
                </div>
            </div>
        </div>

        <!-- Combo Items Section with Select2 Multi-select -->
        <div x-show="selectedType == 2" x-transition class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-800">Combo Meal Items</h3>
                <p class="text-sm text-gray-600">Select multiple menu items to include in this combo</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column: Combo Items -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Combo Items *</label>
                    <select name="combo_items[]" multiple="multiple"
                        class="combo-items-select w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        @foreach($menuItems as $item)
                        <option value="{{ $item->id }}" {{ in_array($item->id, old('combo_items', [])) ? 'selected' : '' }}>
                            {{ $item->name }} - ${{ number_format($item->price, 2) }}
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Select multiple items to include in this combo meal</p>
                </div>

                <!-- Right Column: Combo Discount & Final Price -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Combo Discount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Combo Discount (%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                            <input type="number" name="combo_discount" value="{{ old('combo_discount', 0) }}" min="0" max="100"
                                class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                placeholder="0">
                        </div>
                    </div>

                    <!-- Final Combo Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Final Combo Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="combo_final_price" value="{{ old('combo_final_price') }}" step="0.01" min="0"
                                class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                <span class="material-symbols-outlined mr-2 text-sm">save</span> Save Item
            </button>
            <a href="{{ route('admin.menu.items.index') }}"
                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for combo items multi-select
        $('.combo-items-select').select2({
            placeholder: "Select items for combo meal",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<style>
    /* .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem;
        min-height: 42px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #6366f1;
        ring: 2px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #6366f1;
        border-color: #6366f1;
        color: white;
        padding: 2px 8px;
        border-radius: 0.375rem;
    } */
</style>
@endsection