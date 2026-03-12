<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockMaster;
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
        $query = StockMaster::with(['ingredient.unit']);

        // Handle simple filtering or search if needed
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('ingredient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $stocks = $query->get();

        return Inertia::render('Admin/Inventory/Stock/Index', [
            'stocks' => $stocks,
            'pageTitle' => 'Current Stock'
        ]);
    }

    public function lowStock()
    {
        // Find ingredients where current_stock <= min_stock in StockMaster
        // or items that don't even have a StockMaster entry but have min_stock > 0
        
        $stocks = StockMaster::with(['ingredient.unit'])
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
        $ingredients = Ingredient::with(['unit', 'stockMaster'])->where('status', 1)->get();
        
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
                $stockMaster = StockMaster::firstOrCreate(
                    ['ingredient_id' => $validated['ingredient_id']],
                    ['current_stock' => 0, 'total_in' => 0, 'total_out' => 0]
                );

                if ($validated['transaction_type'] === 'in') {
                    $stockMaster->current_stock += $validated['quantity'];
                    $stockMaster->total_in += $validated['quantity'];
                } else {
                    if ($stockMaster->current_stock < $validated['quantity']) {
                        throw new Exception('Insufficient stock for outward adjustment.');
                    }
                    $stockMaster->current_stock -= $validated['quantity'];
                    $stockMaster->total_out += $validated['quantity'];
                }

                $stockMaster->save();

                StockLedger::create([
                    'ingredient_id' => $validated['ingredient_id'],
                    'transaction_type' => $validated['transaction_type'],
                    'quantity' => $validated['quantity'],
                    'reference_type' => 'adjustment',
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
