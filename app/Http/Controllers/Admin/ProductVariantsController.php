<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductItem;
use App\Models\ProductVariant;

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
            ->paginate(10);

        return view('admin.menus.variants.index', compact('variants', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $variants = ProductVariant::with('productItem')->get();
        $menuItems = ProductItem::all();
        return view('admin.menus.variants.create', compact('variants', 'menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'required|exists:menu_items,id',
            'price' => 'required|numeric|min:0',
        ]);

        ProductVariant::create([
            'name' => $validated['name'],
            'item_id' => $validated['item_id'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.menu.variants.index')->with('success', 'Menu variants created successfully.');
    }

    public function edit(ProductVariant $variant)
    {
        $menuItems = ProductItem::all();
        return view('admin.menus.variants.edit', compact('variant', 'menuItems'));
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'required|exists:menu_items,id',
            'price' => 'required|numeric|min:0',
        ]);

        $variant->update([
            'name' => $validated['name'],
            'item_id' => $validated['item_id'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.menu.variants.index')->with('success', 'Menu variants updated successfully.');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return redirect()->route('admin.menu.variants.index')->with('success', 'Menu variants deleted successfully.');
    }
}
