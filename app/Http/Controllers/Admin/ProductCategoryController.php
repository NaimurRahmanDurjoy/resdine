<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\ResDepartment;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $categories = ProductCategory::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/ProductCategories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction
            ],
            'pageTitle' => 'Product Categories'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ProductCategories/Create', [
            'pageTitle' => 'Create Category'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        $imagePath = null;

        if ($request->hasFile('category_img')) {
            $imagePath = $request->file('category_img')->store('menu_categories', 'public');
        }

        ProductCategory::create([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.product.categories.index')->with('success', 'Menu category created successfully.');
    }

    public function edit(ProductCategory $category)
    {
        return Inertia::render('Admin/ProductCategories/Edit', [
            'category' => $category,
            'pageTitle' => 'Edit Category: ' . $category->name
        ]);
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'status' => $validated['status'],
        ];

        if ($request->hasFile('category_img')) {
            // Delete old image if it exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $data['image'] = $request->file('category_img')->store('menu_categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.product.categories.index')->with('success', 'Menu category updated successfully.');
    }

    public function destroy(ProductCategory $category)
    {
        // Delete associated image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return redirect()->route('admin.product.categories.index')->with('success', 'Menu category deleted successfully.');
    }
}
