@extends('layouts.devAdmin')

@section('page-title', 'Admin Menu')

@section('content')

@php
$baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-800">Admin Menu Sorting</h1>
        <p class="text-gray-600 text-sm mt-1">Drag items to reorder or nest them under others.</p>
    </div>

    <div id="menuList" class="menu-group space-y-2 p-4">
        @foreach($menus as $menu)
            @include('devAdmin.systemConfig.adminMenu.menuSorting.menuItem', ['menu' => $menu])
        @endforeach
    </div>

    <div class="mt-6 p-4 border-t">
        <button id="saveOrder" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none">
            Save Order
        </button>
        <span id="message" class="ml-4 text-sm text-green-600"></span>
    </div>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const message = document.getElementById('message');

    // Initialize Sortable on all menu groups
    function initSortable(el) {
        new Sortable(el, {
            group: 'nested',
            animation: 200,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            ghostClass: 'bg-blue-50',
            onAdd: () => message.textContent = 'Changes made - click Save to update',
            onUpdate: () => message.textContent = 'Changes made - click Save to update'
        });
    }

    document.querySelectorAll('.menu-group').forEach(group => initSortable(group));

    // Save order
    document.getElementById('saveOrder').addEventListener('click', function() {
        const structure = getMenuStructure(document.getElementById('menuList'));
        fetch('{{ route("devAdmin.systemConfig.adminPanel.menuSorting.updateOrder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ structure })
        })
        .then(res => res.json())
        .then(() => {
            message.textContent = 'Order saved successfully!';
            setTimeout(() => message.textContent = '', 3000);
        })
        .catch(() => {
            message.textContent = 'Error saving order';
            message.className = 'ml-4 text-sm text-red-600';
        });
    });

    // Recursively extract hierarchy
    function getMenuStructure(container) {
        const items = [];
        container.querySelectorAll(':scope > .menu-item').forEach((item, index) => {
            const id = item.dataset.id;
            const childrenContainer = item.querySelector(':scope > .menu-children');
            items.push({
                id: id,
                order: index + 1,
                children: childrenContainer ? getMenuStructure(childrenContainer) : []
            });
        });
        return items;
    }
});
</script>

<style>
.menu-item { transition: all 0.2s ease; }
.menu-item:hover { transform: translateX(4px); }
.menu-children { margin-left: 2rem; margin-top: 0.5rem; }
</style>

@endsection
