<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Models\Ingredient;
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
        $sortable = ['name', 'current_stock', 'created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'name';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);

        // We use Ingredient as the base to catch items with ZERO stock (no summary)
        $query = Ingredient::with(['unit', 'stockSummary']);

        // Main Query with Search
        $query->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });

        // Quick Filters
        if ($filter === 'low_stock') {
            $query->where(function($q) {
                $q->whereHas('stockSummary', function ($sq) {
                    $sq->whereColumn('stock_summary.current_stock', '<=', 'ingredients.min_stock');
                })->orWhereDoesntHave('stockSummary', function($sq) {
                    $sq->where('min_stock', '>', 0);
                });
            });
        } elseif ($filter === 'expiring') {
            $query->where('has_expiry', 1)
                ->whereHas('purchaseDetails', function ($q) {
                    $q->whereNotNull('expiry_date')
                      ->where('expiry_date', '<=', now()->addDays(30))
                      ->where('expiry_date', '>=', now());
                })
                ->with(['purchaseDetails' => function($q) {
                    $q->whereNotNull('expiry_date')
                      ->where('expiry_date', '<=', now()->addDays(30))
                      ->orderBy('expiry_date', 'asc');
                }]);
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

    public function show($id)
    {
        $ingredient = Ingredient::with(['unit', 'stockSummary'])->findOrFail($id);
        
        $ledger = StockLedger::where('ingredient_id', $id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Inventory/Stock/History', [
            'ingredient' => $ingredient,
            'ledger' => $ledger,
            'pageTitle' => 'Stock Audit: ' . $ingredient->name
        ]);
    }


    public function adjust()
    {
        $branchId = auth()->user()->branch_id ?? Branch::first()?->id;
        
        $ingredients = Ingredient::with(['unit', 'stockSummary' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->where('status', 1)->get();
        
        return Inertia::render('Admin/Inventory/Stock/Adjust', [
            'ingredients' => $ingredients,
            'pageTitle' => 'Stock Adjustment'
        ]);
    }

    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
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

                $ingredient = Ingredient::findOrFail($validated['ingredient_id']);
                
                // 1. Update/Create Stock Summary
                $stockSummary = StockSummary::where([
                    'ingredient_id' => $validated['ingredient_id'],
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
                            'ingredient_id' => $validated['ingredient_id'],
                            'unit_id' => $ingredient->unit_id,
                            'branch_id' => $branchId,
                            'current_stock' => $validated['quantity'],
                            'average_cost' => 0, // Adjustment might not have cost info
                            'batch_no' => null,
                            'last_transaction_date' => now()
                        ]);
                    }
                    
                    $qtyIn = $validated['quantity'];
                    $qtyOut = 0;
                    $transType = 5; // 5=adjustment_in
                } else {
                    if (!$stockSummary || $stockSummary->current_stock < $validated['quantity']) {
                        throw new Exception('Insufficient stock for outward adjustment.');
                    }
                    
                    $stockSummary->current_stock -= $validated['quantity'];
                    $stockSummary->last_transaction_date = now();
                    $stockSummary->save();
                    
                    $qtyIn = 0;
                    $qtyOut = $validated['quantity'];
                    $transType = 6; // 6=adjustment_out
                }

                // 2. Create Stock Ledger Entry
                StockLedger::create([
                    'ingredient_id' => $validated['ingredient_id'],
                    'unit_id' => $ingredient->unit_id,
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
