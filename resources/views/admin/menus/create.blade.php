@extends('layouts.admin')

@section('page-title', 'Add Menu Item')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm" x-data="{ selectedType: '{{ old('type', 1) }}' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Menu Item</h1>
        <p class="text-gray-600">Create a new menu item for your restaurant</p>
    </div>

    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
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
                           required>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="status" value="1" id="status" 
                           {{ old('status', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="status" class="ml-2 block text-sm text-gray-700">Active Item</label>
                </div>
            </div>

            <!-- Price & Image -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                               required>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2"> Res Department *</label>
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
                    <input type="file" name="menu_img" 
                           accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
        </div>

        <!-- Combo Items Section - Show only when type == 2 -->
        <div x-show="selectedType == 2" x-transition class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-800">Combo Items</h3>
                <button type="button" onclick="addComboItem()" 
                        class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition flex items-center">
                    <span class="material-icons mr-1 text-sm">add</span> Add Item
                </button>
            </div>
            
            <div id="combo-items-container" class="space-y-3">
                <!-- Default empty combo item -->
                <div class="combo-item grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Menu Item</label>
                        <select name="combo_items[0][item_id]" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Select Menu Item</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} - ${{ number_format($item->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="combo_items[0][quantity]" value="1" min="1"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="md:col-span-2">
                        <button type="button" onclick="removeComboItem(this)" 
                                class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex space-x-3">
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                <span class="material-icons mr-2 text-sm">save</span> Save Item
            </button>
            <a href="{{ route('admin.menu.index') }}" 
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let comboItemCount = 1;

function addComboItem() {
    const container = document.getElementById('combo-items-container');
    const newItem = document.createElement('div');
    newItem.className = 'combo-item grid grid-cols-1 md:grid-cols-12 gap-3 items-end';
    newItem.innerHTML = `
        <div class="md:col-span-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Menu Item</label>
            <select name="combo_items[${comboItemCount}][item_id]" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="">Select Menu Item</option>
                @foreach($menuItems as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} - ${{ number_format($item->price, 2) }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
            <input type="number" name="combo_items[${comboItemCount}][quantity]" value="1" min="1"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
        </div>
        <div class="md:col-span-2">
            <button type="button" onclick="removeComboItem(this)" 
                    class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Remove
            </button>
        </div>
    `;
    container.appendChild(newItem);
    comboItemCount++;
}

function removeComboItem(button) {
    const comboItem = button.closest('.combo-item');
    if (document.querySelectorAll('.combo-item').length > 1) {
        comboItem.remove();
    }
}
</script>
@endsection
