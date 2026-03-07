<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductItem;
use App\Models\ProductVariant;

use Inertia\Inertia;

class ProductVariantsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $variants = ProductVariant::with('productItem')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhereHas('productItem', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        });
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/ProductVariants/Index', [
            'variants' => $variants,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction
            ],
            'pageTitle' => 'Menu Variants'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ProductVariants/Create', [
            'menuItems' => ProductItem::all(),
            'pageTitle' => 'Create Variant'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'required|exists:menu_items,id',
            'price' => 'required|numeric|min:0',
        ]);

        ProductVariant::create($validated);

        return redirect()->route('admin.product.variants.index')->with('success', 'Menu variant created successfully.');
    }

    public function edit(ProductVariant $variant)
    {
        return Inertia::render('Admin/ProductVariants/Edit', [
            'variant' => $variant,
            'menuItems' => ProductItem::all(),
            'pageTitle' => 'Edit Variant: ' . $variant->name
        ]);
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'required|exists:menu_items,id',
            'price' => 'required|numeric|min:0',
        ]);

        $variant->update($validated);

        return redirect()->route('admin.product.variants.index')->with('success', 'Menu variant updated successfully.');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return redirect()->route('admin.product.variants.index')->with('success', 'Menu variant deleted successfully.');
    }
}
