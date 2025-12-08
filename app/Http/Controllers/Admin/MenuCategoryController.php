<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Unit;
use App\Models\ResDepartment;
use App\Models\ComboItemDetail; // Add this model


class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $categories = MenuCategory::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10);

        return view('admin.menus.category.index', compact('categories', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $categories = MenuCategory::all();
        return view('admin.menus.category.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $imagePath = null;

        if ($request->hasFile('category_img')) {
            $file = $request->file('category_img');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/menu_category_img');

            // Create folder if not exists
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $imagePath = 'uploads/menu_category_img/' . $filename;
        }

        MenuCategory::create([
            'name' => $validated['name'],
            'status' => $request->has('status') ? 1 : 0,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.menu.categories.index')->with('success', 'Menu category created successfully.');
    }


    public function edit(MenuCategory $category)
    {
        $categories = MenuCategory::where('status', 1)->get();
        
        return view('admin.menus.category.edit', compact('category'));
    }

    public function update(Request $request, MenuCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($request->hasFile('category_img')) {
            // Delete old image if it exists
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $file = $request->file('category_img');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/menu_category_img');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $data['image'] = 'uploads/menu_category_img/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.menu.categories.index')->with('success', 'Menu category updated successfully.');
    }


    public function destroy(MenuCategory $category)
    {
        // Delete associated image
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }
        $category->delete();
        return redirect()->route('admin.menu.categories.index')->with('success', 'Menu category deleted successfully.');
    }
}