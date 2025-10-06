<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\MenuVariant;

class MenuVariantsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $variants = MenuVariant::with('MenuItem')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->when($search, fn($q) => $q->where('item', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10);

        return view('admin.menus.variants.index', compact('variants', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $variants = MenuVariant::with('MenuItem')->get();
        $menuItems = MenuItem::all();
        return view('admin.menus.variants.create', compact('variants','menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'required|exists:menu_items,id',
            'price' => 'required|numeric|min:0',
        ]);

        MenuVariant::create([
            'name' => $validated['name'],
            'item_id' => $validated['item_id'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.menu.variants.index')->with('success', 'Menu variants created successfully.');
    }

    public function edit(MenuVariant $variants)
    {
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get();
        
        return view('admin.menus.variants.edit', compact('menu', 'categories', 'menuItems', 'comboItems'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2,3',
            'category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
            'menu_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'combo_items' => 'required_if:type,2|array',
            'combo_items.*.item_id' => 'required_if:type,2|exists:menu_items,id',
            'combo_items.*.quantity' => 'required_if:type,2|integer|min:1'
        ]);

        // Update the main menu item
        $menu->name = $request->name;
        $menu->type = $request->type;
        $menu->category_id = $request->category_id;
        $menu->price = $request->price;
        $menu->status = $request->status;

        // Handle image upload
        if ($request->hasFile('menu_img')) {
            // Delete old image
            if ($menu->menu_img) {
                Storage::disk('public')->delete($menu->menu_img);
            }
            
            $imagePath = $request->file('menu_img')->store('menu-images', 'public');
            $menu->menu_img = $imagePath;
        }

        $menu->save();

        // Handle combo items - delete existing and create new ones
        if ($menu->type == 2) {
            // Delete existing combo items
            ComboItemDetail::where('combo_id', $menu->id)->delete();
            
            // Add new combo items
            if ($request->has('combo_items')) {
                foreach ($request->combo_items as $comboItem) {
                    ComboItemDetail::create([
                        'combo_id' => $menu->id,
                        'item_id' => $comboItem['item_id'],
                        'quantity' => $comboItem['quantity']
                    ]);
                }
            }
        } else {
            // If changing from combo to another type, remove combo items
            ComboItemDetail::where('combo_id', $menu->id)->delete();
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        // Delete associated image
        if ($menu->menu_img) {
            Storage::disk('public')->delete($menu->menu_img);
        }

        // Delete combo items if exists
        if ($menu->type == 2) {
            ComboItemDetail::where('combo_id', $menu->id)->delete();
        }

        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu item deleted successfully.');
    }
}