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
        $sortable = ['name','short_name','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $stocks = StockLedger::with(['ingredient.unit'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('ingredient', function ($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();


        return Inertia::render('Admin/Inventory/Stock/Index', [
            'stocks' => $stocks,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Current Stock'
        ]);
    }

    public function lowStock()
    {
        // Find ingredients where current_stock <= min_stock in StockMaster
        // or items that don't even have a StockMaster entry but have min_stock > 0
        
        $stocks = StockLedger::with(['ingredient.unit'])
            ->whereHas('ingredient', function ($q) {
                $q->whereColumn('current_stock', '<=', 'min_stock');
            })->get();

        // Also find ingredients with no stock record but configured with min_stock > 0
        $noStockIngredients = Ingredient::with('unit')
            ->whereDoesntHave('stockMaster')
            ->where('min_stock', '>', 0)
            ->get()
            ->map(function ($ingredient) {
                return (object) [
                    'ingredient' => $ingredient,
                    'current_stock' => 0,
                    'status' => 'critical'
                ];
            });

        return Inertia::render('Admin/Inventory/Stock/LowStock', [
            'stocks' => $stocks,
            'missingStocks' => $noStockIngredients,
            'pageTitle' => 'Low Stock Alerts'
        ]);
    }

    public function expiryAlerts()
    {
        // In a real scenario, we'd query PurchaseDetails or batches for expiry_date.
        // We'll simulate checking purchase details for upcoming expiries.
        
        $expiringItems = DB::table('purchase_details')
            ->join('ingredients', 'purchase_details.ingredient_id', '=', 'ingredients.id')
            ->join('units', 'ingredients.unit_id', '=', 'units.id')
            ->whereNotNull('purchase_details.expiry_date')
            ->where('purchase_details.expiry_date', '<=', now()->addDays(30))
            ->where('ingredients.expiry_tracking', 1)
            ->select(
                'purchase_details.id',
                'ingredients.name as ingredient_name',
                'units.short_name as unit_name',
                'purchase_details.quantity',
                'purchase_details.expiry_date',
                'purchase_details.purchase_master_id'
            )
            ->orderBy('purchase_details.expiry_date', 'asc')
            ->get();

        return Inertia::render('Admin/Inventory/Stock/ExpiryAlert', [
            'expiringItems' => $expiringItems,
            'pageTitle' => 'Expiry Alerts'
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
