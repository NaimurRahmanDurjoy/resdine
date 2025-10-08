<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\MenuItem\UpdateMenuItemRequest;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Unit;
use App\Models\ResDepartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $items = MenuItem::with(['category', 'unit', 'department'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('res_department', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(10);

        return view('admin.menus.items.index', compact('items', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $categories = MenuCategory::all();
        $units = Unit::all();
        $departments = ResDepartment::all();
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get();

        return view('admin.menus.items.create', compact('categories', 'menuItems', 'units', 'departments'));
    }

    public function store(StoreMenuItemRequest $request)
    {
        $validated = $request->validated();

        // Remove combo_items from the validated data before creating MenuItem
        $comboItems = $request->input('combo_items', []);

        if ($request->hasFile('menu_img')) {
            $validated['menu_img'] = $request->file('menu_img')->store('menu-images', 'public');
        }

        // Remove combo_items to prevent mass assignment error
        unset($validated['combo_items']);

        DB::transaction(function () use ($validated, $comboItems) {
            $menuItem = MenuItem::create($validated);

            if ($menuItem->type == 2 && !empty($comboItems)) {
                $this->syncComboItems($menuItem, $comboItems);
            }
        });

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $item)
    {
        $categories = MenuCategory::all();
        $units = Unit::all();
        $departments = ResDepartment::all();
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get();

        $item->load('comboItems.menuItem');

        return view('admin.menus.items.edit', compact('item', 'categories', 'menuItems', 'departments', 'units'));
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menu)
    {
        // dd($request->input('combo_items'));
        $validated = $request->validated();

        $comboItems = $request->input('combo_items', []);

        if ($request->hasFile('menu_img')) {
            if ($menu->menu_img) {
                Storage::disk('public')->delete($menu->menu_img);
            }
            $validated['menu_img'] = $request->file('menu_img')->store('menu-images', 'public');
        }

        unset($validated['combo_items']);

        DB::transaction(function () use ($menu, $validated, $comboItems) {
            $menu->update($validated);

        $type = $validated['type'] ?? $menu->type;

        if ($type == 2) {
            $this->syncComboItems($menu, $comboItems);
        } else {
            $menu->comboItems()->delete();
        }

        });


        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        if ($menu->menu_img) {
            Storage::disk('public')->delete($menu->menu_img);
        }

        DB::transaction(function () use ($menu) {
            $menu->comboItems()->delete();
            $menu->delete();
        });

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item deleted successfully.');
    }

    /**
     * Sync combo items for a menu item.
     *
     * @param MenuItem $menuItem
     * @param array $comboItems
     * @return void
     */
private function syncComboItems(MenuItem $menuItem, array $comboItems): void
{
    \Log::info('Syncing combo items', ['menu_item_id' => $menuItem->id, 'combo_items' => $comboItems]);

    $menuItem->comboItems()->delete();

    $comboItemsData = collect($comboItems)->map(function ($itemId) {
        return ['item_id' => $itemId, 'quantity' => 1];
    })->toArray();

    \Log::info('Combo items data prepared', $comboItemsData);

    $menuItem->comboItems()->createMany($comboItemsData);
}


}
