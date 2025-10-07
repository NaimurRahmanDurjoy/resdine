<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Unit;
use App\Models\ResDepartment;
use App\Models\ComboItemDetail; // Add this model
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $items = MenuItem::with(['category','unit','department'])
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->when($search, fn($q) => $q->where('type', 'like', "%$search%"))
            ->when($search, fn($q) => $q->where('res_department', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10);
        return view('admin.menus.items.index', compact('items','search', 'sort', 'direction'));
    }

    public function create()
    {
        $categories = MenuCategory::all();
        $units = Unit::all();
        $departments = ResDepartment::all();
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get(); // Get regular menu items for combo
        return view('admin.menus.items.create', compact('categories', 'menuItems','units','departments'));
    }

  public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2,3',
            'category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'department_id' => 'required|exists:res_departments,id',
            'description' => 'nullable|string',
            'menu_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'combo_items' => 'required_if:type,2|array',
            'combo_discount' => 'nullable|numeric|min:0|max:100',
            'combo_final_price' => 'nullable|numeric|min:0'
        ]);

        // Handle image upload with Storage
        if ($request->hasFile('menu_img')) {
            $validated['menu_img'] = $request->file('menu_img')->store('menu-images', 'public');
        }

        $menuItem = MenuItem::create($validated);

        // Handle combo items
        if ($request->type == 2 && $request->has('combo_items')) {
            $comboItemsData = collect($request->combo_items)->map(function($itemId) {
                return ['item_id' => $itemId, 'quantity' => 1];
            })->toArray();

            $menuItem->comboItems()->createMany($comboItemsData);
        }

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu)
    {
        $categories = MenuCategory::all();
        $units = Unit::all();
        $departments = ResDepartment::all();
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get();
        
        // Eager load combo items with their menu items
        $menu->load('comboItems.menuItem');
        
        return view('admin.menus.items.edit', compact('menu', 'categories', 'menuItems', 'departments', 'units'));
    }

     public function update(Request $request, MenuItem $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2,3',
            'category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'department_id' => 'required|exists:res_departments,id',
            'description' => 'nullable|string',
            'menu_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'combo_items' => 'required_if:type,2|array',
            'combo_discount' => 'nullable|numeric|min:0|max:100',
            'combo_final_price' => 'nullable|numeric|min:0'
        ]);

        // Handle menu image upload to menu-images folder
        if ($request->hasFile('menu_img')) {
            if ($menu->menu_img) {
                Storage::disk('public')->delete($menu->menu_img);
            }
            // Store new image in menu-images folder
            $validated['menu_img'] = $request->file('menu_img')
                ->store('menu-images', 'public');
        }

        $menu->update($validated);

        // Handle combo items
        if ($menu->type == 2) {
            $menu->comboItems()->delete();
            
            if ($request->has('combo_items')) {
                $comboItemsData = collect($request->combo_items)->map(function($itemId) {
                    return ['item_id' => $itemId, 'quantity' => 1];
                })->toArray();

                $menu->comboItems()->createMany($comboItemsData);
            }
        } else {
            $menu->comboItems()->delete();
        }

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        // Delete menu image from menu-images folder
        if ($menu->menu_img) {
            Storage::disk('public')->delete($menu->menu_img);
        }
        $menu->comboItems()->delete();
        $menu->delete();

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item deleted successfully.');
    }

}