<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductItem;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductionController extends Controller
{
    protected $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index()
    {
        $history = StockLedger::with(['ingredient', 'productItem'])
            ->where('reference_type', 'production')
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Inventory/Production/Index', [
            'history' => $history,
            'pageTitle' => 'Production History'
        ]);
    }

    public function create()
    {
        $prepItems = ProductItem::prepItems()->with(['unit', 'recipe'])->get();
        
        return Inertia::render('Admin/Inventory/Production/Create', [
            'prepItems' => $prepItems,
            'pageTitle' => 'New Production Batch'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_item_id' => 'required|exists:product_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'production_date' => 'required|date',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $branchId = auth()->user()->branch_id ?? \App\Models\Branch::first()?->id;
                $product = ProductItem::with('inventoryItem')->findOrFail($validated['product_item_id']);

                if (!$product->inventoryItem) {
                    throw new Exception("Inventory mapping missing for this product.");
                }

                // 1. Calculate cost and deduct raw ingredients
                $recipe = $product->recipe;
                if (!$recipe) {
                    throw new Exception("No recipe defined for this product.");
                }

                $recipeCostData = $this->recipeService->calculateRecipeCost($recipe);
                $unitCost = $recipeCostData['total_cost'];

                // 2. Deduct ingredients
                $refId = time(); 
                $this->recipeService->deductStockForProduct(
                    $product->id, 
                    null, 
                    $validated['quantity'], 
                    'production_deduction', 
                    $refId, 
                    $branchId
                );

                // 3. Add the prepped product to stock
                $this->addProducedStock($product, $validated['quantity'], $unitCost, $branchId, $refId);
            });

            return redirect()->route('admin.production.index')->with('success', 'Production batch recorded successfully.');
            
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function addProducedStock($product, $qty, $unitCost, $branchId, $refId)
    {
        $inventoryItem = $product->inventoryItem;

        $stockSummary = StockSummary::where([
            'inventory_item_id' => $inventoryItem->id,
            'branch_id' => $branchId,
            'batch_no' => null
        ])->first();

        if ($stockSummary) {
            $oldStock = (float)$stockSummary->current_stock;
            $newStock = $oldStock + $qty;
            
            if ($newStock > 0) {
                $stockSummary->average_cost = (($oldStock * $stockSummary->average_cost) + ($qty * $unitCost)) / $newStock;
            }
            
            $stockSummary->current_stock = $newStock;
            $stockSummary->last_transaction_date = now();
            $stockSummary->save();
        } else {
            StockSummary::create([
                'inventory_item_id' => $inventoryItem->id,
                'unit_id' => $product->unit_id,
                'branch_id' => $branchId,
                'current_stock' => $qty,
                'average_cost' => $unitCost,
                'batch_no' => null,
                'last_transaction_date' => now()
            ]);
        }

        StockLedger::create([
            'inventory_item_id' => $inventoryItem->id,
            'unit_id' => $product->unit_id,
            'branch_id' => $branchId,
            'transaction_type' => 9, // production_in
            'reference_id' => $refId,
            'reference_type' => 'production',
            'qty_in' => $qty,
            'qty_out' => 0,
            'unit_cost' => $unitCost,
            'transaction_date' => now()
        ]);
    }
}

