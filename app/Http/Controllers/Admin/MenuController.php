<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\MenuItem\UpdateMenuItemRequest;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\ComboItemDetail;
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
                    ->orWhereHas('department', function ($departmentQuery) use ($search) {
                          $departmentQuery->where('name', 'like', "%{$search}%");
                      });

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
        $comboItems = $request->input('combo_items', []);

        if ($request->hasFile('menu_img')) {
            $validated['menu_img'] = $request->file('menu_img')->store('menu-images', 'public');
        }

        unset($validated['combo_items']); // Remove from main table data

        DB::transaction(function () use ($validated, $comboItems) {
            $menuItem = MenuItem::create($validated);

            if ($menuItem->type == 2) {
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

    public function update(UpdateMenuItemRequest $request, MenuItem $item)
    {
        $validated = $request->validated();
        $comboItems = $request->input('combo_items', []);

        if ($request->hasFile('menu_img')) {
            if ($item->menu_img) {
                Storage::disk('public')->delete($item->menu_img);
            }
            $validated['menu_img'] = $request->file('menu_img')->store('menu-images', 'public');
        }

        unset($validated['combo_items']);

        DB::transaction(function () use ($item, $validated, $comboItems) {
            $item->update($validated);

            if ($item->type == 2) {
                $this->syncComboItems($item, $comboItems);
            } else {
                $item->comboItems()->delete();
            }
        });

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item updated successfully.');
    }
    public function destroy(MenuItem $item)
    {
        if ($item->menu_img) {
            Storage::disk('public')->delete($item->menu_img);
        }

        DB::transaction(function () use ($item) {
            $item->comboItems()->delete();
            $item->delete();
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
        $menuItem->comboItems()->delete();

        $comboItemsData = array_map(function ($itemId) use ($menuItem) {
            return [
                'combo_id' => $menuItem->id,
                'item_id' => $itemId,
                'quantity' => 1,
            ];
        }, array_filter($comboItems));

        if (!empty($comboItemsData)) {
            ComboItemDetail::insert($comboItemsData);
        }
    }
}
