<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductItem\StoreMenuItemRequest;
use App\Http\Requests\ProductItem\UpdateMenuItemRequest;
use App\Models\ProductItem;
use App\Models\ProductCategory;
use App\Models\ComboItemDetail;
use App\Models\Unit;
use App\Models\ResDepartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Services\ImageUploadService;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $items = ProductItem::with(['category', 'unit', 'department'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($departmentQuery) use ($search) {
                        $departmentQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $items,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction
            ],
            'pageTitle' => 'Menu Items'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => ProductCategory::all(),
            'units' => Unit::all(),
            'departments' => ResDepartment::all(),
            'menuItems' => ProductItem::where('type', 1)->where('status', 1)->get(),
            'pageTitle' => 'Create Product'
        ]);
    }

    public function store(StoreMenuItemRequest $request)
    {
        $validated = $request->validated();
        $comboItems = $request->input('combo_items', []);

        if ($request->hasFile('menu_img')) {
            $validated['menu_img'] = $this->imageService->upload($request->file('menu_img'), 'menu-images');
        }

        unset($validated['combo_items']);

        DB::transaction(function () use ($validated, $comboItems) {
            $menuItem = ProductItem::create($validated);

            if ($menuItem->type == 2) {
                $this->syncComboItems($menuItem, $comboItems);
            }
        });

        return redirect()->route('admin.product.items.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(ProductItem $item)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $item,
            'categories' => ProductCategory::all(),
            'units' => Unit::all(),
            'departments' => ResDepartment::all(),
            'menuItems' => ProductItem::where('type', 1)->where('status', 1)->get(),
            'comboItemIds' => $item->comboItems()->pluck('item_id')->toArray(),
            'pageTitle' => 'Edit Product: ' . $item->name
        ]);
    }

    public function update(UpdateMenuItemRequest $request, ProductItem $item)
    {
        $validated = $request->validated();
        $comboItems = $request->input('combo_items', []);

        if ($request->hasFile('menu_img')) {
            $this->imageService->delete($item->menu_img);
            $validated['menu_img'] = $this->imageService->upload($request->file('menu_img'), 'menu-images');
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

        return redirect()->route('admin.product.items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(ProductItem $item)
    {
        $this->imageService->delete($item->menu_img);

        DB::transaction(function () use ($item) {
            $item->comboItems()->delete();
            $item->delete();
        });

        return redirect()->route('admin.product.items.index')->with('success', 'Menu item deleted successfully.');
    }

    private function syncComboItems(ProductItem $menuItem, array $comboItems): void
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
