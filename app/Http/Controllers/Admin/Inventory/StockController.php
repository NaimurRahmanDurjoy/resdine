<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Models\Ingredient;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $sortable = ['current_stock', 'last_transaction_date'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'last_transaction_date';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);

        $query = StockSummary::with(['inventoryItem.unit', 'unit']);

        // Search in unified inventory item name
        if ($search) {
            $query->whereHas('inventoryItem', function ($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%");
            });
        }

        // Quick Filters
        if ($filter === 'low_stock') {
            $query->whereHas('inventoryItem', function ($q) {
                $q->whereColumn('stock_summary.current_stock', '<=', 'inventory_items.min_stock');
            });
        } elseif ($filter === 'expiring') {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>=', now());
        }

        $stocks = $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Inventory/Stock/Index', [
            'stocks' => $stocks,
            'filters' => [
                'search' => $search,
                'filter' => $filter,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Current Stock'
        ]);
    }

    public function show($type, $id)
    {
        $itemClass = $type == 1 ? Ingredient::class : ProductItem::class;
        $sourceItem = $itemClass::with(['unit', 'inventoryItem.stockSummary', 'inventoryItem.unit'])->findOrFail($id);
        $inventoryItem = $sourceItem->inventoryItem;

        $ledger = StockLedger::where('inventory_item_id', $inventoryItem->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Inventory/Stock/History', [
            'item' => $sourceItem,
            'ledger' => $ledger,
            'pageTitle' => 'Stock Audit: ' . $sourceItem->name
        ]);
    }


    public function adjust()
    {
        $branchId = auth()->user()->branch_id ?? Branch::first()?->id;

        $ingredients = Ingredient::with(['unit', 'inventoryItem.stockSummary' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->where('status', 1)->get();

        $retailItems = ProductItem::with(['unit', 'inventoryItem.stockSummary' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->where('is_retail', true)->get();

        return Inertia::render('Admin/Inventory/Stock/Adjust', [
            'ingredients' => $ingredients,
            'retailItems' => $retailItems,
            'pageTitle' => 'Stock Adjustment'
        ]);
    }

    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:1,2', // 1=Ingredient, 2=ProductItem
            'item_id' => 'required|numeric',
            'transaction_type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $branchId = auth()->user()->branch_id ?? Branch::first()?->id;

                if (!$branchId) {
                    throw new Exception('A branch must be assigned to perform stock adjustment.');
                }

                $itemClass = $validated['item_type'] == 1 ? Ingredient::class : ProductItem::class;
                $sourceItem = $itemClass::with('inventoryItem')->findOrFail($validated['item_id']);
                $inventoryItem = $sourceItem->inventoryItem;

                if (!$inventoryItem) {
                    return redirect()->back()->withErrors(['error' => 'Inventory item not found for the given source item.']);
                }

                // 1. Update/Create Stock Summary
                $stockSummary = StockSummary::where([
                    'inventory_item_id' => $inventoryItem->id,
                    'branch_id' => $branchId,
                    'batch_no' => null
                ])->lockForUpdate()->first();

                if ($validated['transaction_type'] === 'in') {
                    if ($stockSummary) {
                        $stockSummary->current_stock += $validated['quantity'];
                        $stockSummary->last_transaction_date = now();
                        $stockSummary->save();
                    } else {
                        StockSummary::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'unit_id' => $sourceItem->unit_id,
                            'branch_id' => $branchId,
                            'current_stock' => $validated['quantity'],
                            'average_cost' => 0,
                            'batch_no' => null,
                            'last_transaction_date' => now()
                        ]);
                    }

                    $qtyIn = $validated['quantity'];
                    $qtyOut = 0;
                    $transType = 5; // adjustment_in
                } else {
                    if (!$stockSummary || $stockSummary->current_stock < $validated['quantity']) {
                        throw new Exception('Insufficient stock for outward adjustment.');
                    }

                    $stockSummary->current_stock -= $validated['quantity'];
                    $stockSummary->last_transaction_date = now();
                    $stockSummary->save();

                    $qtyIn = 0;
                    $qtyOut = $validated['quantity'];
                    $transType = 6; // adjustment_out
                }

                // 2. Create Stock Ledger Entry
                StockLedger::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'unit_id' => $sourceItem->unit_id,
                    'branch_id' => $branchId,
                    'transaction_type' => $transType,
                    'reference_type' => 'adjustment',
                    'qty_in' => $qtyIn,
                    'qty_out' => $qtyOut,
                    'notes' => $validated['notes'],
                    'transaction_date' => now()
                ]);
            });

            return redirect()->route('admin.stock.index')->with('success', 'Stock adjusted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
