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

class MenuCategoryController extends Controller
{
    public function index()
    {
        $items = MenuItem::with(['category','unit','department'])->get();
        return view('admin.menu.index', compact('items'));
    }

    public function create()
    {
        $categories = MenuCategory::all();
        $units = Unit::all();
        $departments = ResDepartment::all();
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get(); // Get regular menu items for combo
        return view('admin.menu.create', compact('categories', 'menuItems','units','departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2,3', // 1=regular, 2=combo, 3=complementary
            'category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
            'menu_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'combo_items' => 'required_if:type,2|array', // Required only for combo type
            'combo_items.*.item_id' => 'required_if:type,2|exists:menu_items,id',
            'combo_items.*.quantity' => 'required_if:type,2|integer|min:1'
        ]);

        // Create the main menu item
        $menuItem = new MenuItem();
        $menuItem->name = $request->name;
        $menuItem->type = $request->type;
        $menuItem->category_id = $request->category_id;
        $menuItem->price = $request->price;
        $menuItem->status = $request->status;

        // Handle image upload
        if ($request->hasFile('menu_img')) {
            $imagePath = $request->file('menu_img')->store('menu-images', 'public');
            $menuItem->menu_img = $imagePath;
        }

        $menuItem->save();

        // Handle combo items if type is combo (2)
        if ($request->type == 2 && $request->has('combo_items')) {
            foreach ($request->combo_items as $comboItem) {
                ComboItemDetail::create([
                    'combo_id' => $menuItem->id,
                    'item_id' => $comboItem['item_id'],
                    'quantity' => $comboItem['quantity']
                ]);
            }
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu)
    {
        $categories = MenuCategory::all();
        $menuItems = MenuItem::where('type', 1)->where('status', 1)->get();
        
        // Get existing combo items if editing a combo
        $comboItems = [];
        if ($menu->type == 2) {
            $comboItems = ComboItemDetail::with('menuItem')->where('combo_id', $menu->id)->get();
        }
        
        return view('admin.menu.edit', compact('menu', 'categories', 'menuItems', 'comboItems'));
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