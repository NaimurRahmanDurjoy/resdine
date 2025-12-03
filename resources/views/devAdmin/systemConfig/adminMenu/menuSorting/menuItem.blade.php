<div class="menu-item cursor-move" data-id="{{ $menu->id }}">
    <div class="bg-white p-3 rounded-lg border shadow-sm flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <span class="material-symbols-outlined text-blue-500">{{ $menu->icon }}</span>
            <span class="font-medium">{{ $menu->name }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Order: {{ $menu->order }}</span>
            <span class="px-2 py-1 text-xs {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded">
                {{ $menu->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    @if($menu->children->isNotEmpty())
        <div class="menu-children menu-group space-y-2 mt-2">
            @foreach($menu->children as $child)
                @include('devAdmin.systemConfig.adminMenu.menuSorting.menuItem', ['menu' => $child])
            @endforeach
        </div>
    @else
        <div class="menu-children menu-group space-y-2 mt-2"></div>
    @endif
</div>
