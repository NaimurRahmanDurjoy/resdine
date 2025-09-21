@extends('layouts.admin')

@section('content')
<h2 class="text-xl font-bold mb-4">Add Menu Item</h2>

<form action="{{ route('admin.menu.store') }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700">Name</label>
        <input type="text" name="name" class="w-full border px-3 py-2 rounded" value="{{ old('name') }}" required>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700">Type</label>
        <select name="type" class="w-full border px-3 py-2 rounded" x-data x-on:change="$refs.comboSection.classList.toggle('hidden', $event.target.value!='combo')">
            <option value="menu">Menu</option>
            <option value="combo">Combo</option>
            <option value="complementary">Complementary</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700">Category</label>
        <select name="category_id" class="w-full border px-3 py-2 rounded" required>
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700">Price</label>
        <input type="number" name="price" class="w-full border px-3 py-2 rounded" value="{{ old('price') }}" step="0.01" min="0" required>
    </div>

    <div class="mb-4">
        <label class="inline-flex items-center">
            <input type="checkbox" name="status" value="1" checked class="form-checkbox">
            <span class="ml-2">Active</span>
        </label>
    </div>

    <!-- Dynamic Combo Section -->
    <div x-ref="comboSection" class="mb-4 hidden">
        <label class="block text-gray-700">Combo Items</label>
        <div id="combo-items-container">
            <div class="flex space-x-2 mb-2">
                <input type="text" name="combo_items[]" placeholder="Item name" class="border px-3 py-2 rounded w-full">
                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500 text-white rounded">Remove</button>
            </div>
        </div>
        <button type="button" onclick="addComboItem()" class="px-4 py-2 bg-blue-500 text-white rounded">Add Combo Item</button>
    </div>

    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded mt-4">Save</button>
</form>

<script>
function addComboItem() {
    let container = document.getElementById('combo-items-container');
    let div = document.createElement('div');
    div.classList.add('flex','space-x-2','mb-2');
    div.innerHTML = `
        <input type="text" name="combo_items[]" placeholder="Item name" class="border px-3 py-2 rounded w-full">
        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500 text-white rounded">Remove</button>
    `;
    container.appendChild(div);
}
</script>
@endsection
