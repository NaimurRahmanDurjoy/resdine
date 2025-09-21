<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Category;

class MenuController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->get();
        return view('admin.menu.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:menu,combo,complementary',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        MenuItem::create($request->all());

        return redirect()->route('admin.menu.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::all();
        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:menu,combo,complementary',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu item deleted successfully.');
    }
}
