<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PurchaseMaster;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\ProductItem;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Models\Unit;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseController extends Controller
{
    protected $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['purchase_date', 'invoice_number', 'supplier_name', 'total_amount', 'status', 'created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);

        $purchases = PurchaseMaster::with('supplier')
            ->when($search, function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });

        // Handle sorting by related supplier table using a subquery
        if ($sort === 'supplier_name') {
            $purchases->orderBy(
                Supplier::select('name')
                    ->whereColumn('suppliers.id', 'purchase_master.supplier_id')
                    ->limit(1), 
                $direction
            );
        } else {
            $purchases->orderBy($sort, $direction);
        }

        $purchases = $purchases->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Inventory/Purchase/Index', [
            'purchases' => $purchases,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Purchases'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Inventory/Purchase/Create', [
            'suppliers' => Supplier::all(),
            'ingredients' => Ingredient::with('unit')->where('status', 1)->get(),
            'productItems' => ProductItem::with('unit')->where('is_retail', 1)->get(),
            'units' => Unit::all(),
            'pageTitle' => 'New Purchase Order'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:1,2', // 1=Ingredient, 2=ProductItem
            'items.*.item_id' => 'required',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $branchId = auth()->user()->branch_id ?? Branch::first()?->id;

                if (!$branchId) {
                    throw new Exception('A branch must be assigned to store a purchase.');
                }

                $totalAmount = 0;
                foreach ($validated['items'] as $item) {
                    $totalAmount += ($item['quantity'] * $item['unit_price']);
                }

                // 1. Create Purchase Master
                $purchaseMaster = PurchaseMaster::create([
                    'branch_id' => $branchId,
                    'user_id' => auth()->id(),
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_date' => $validated['purchase_date'],
                    'invoice_number' => $validated['invoice_number'] ?? 'PO-' . time(),
                    'total_amount' => $totalAmount,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 2 // Assuming 2 means 'Received'
                ]);

                // 2. Create Details and Update Stock
                foreach ($validated['items'] as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    $itemType = $item['item_type'];
                    $itemId = $item['item_id'];
                    
                    $itemClass = $itemType == 1 ? Ingredient::class : ProductItem::class;
                    $itemModel = $itemClass::with('inventoryItem')->findOrFail($itemId);
                    $inventoryItem = $itemModel->inventoryItem;
                    
                    if (!$inventoryItem) {
                        throw new Exception("Inventory item mapping missing for " . $itemModel->name);
                    }

                    // Normalization
                    $normalizedQuantity = $this->recipeService->convertQuantity(
                        (float)$item['quantity'], 
                        $item['unit_id'] ?? $itemModel->unit_id, 
                        $itemModel->unit_id
                    );

                    $expiryDate = !empty($item['expiry_date']) ? $item['expiry_date'] : null;

                    // Record detail
                    PurchaseDetail::create([
                        'purchase_id' => $purchaseMaster->id,
                        'inventory_item_id' => $inventoryItem->id,
                        'unit_id' => $item['unit_id'] ?? $itemModel->unit_id,
                        'quantity' => $item['quantity'],
                        'normalized_quantity' => $normalizedQuantity,
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice,
                        'expiry_date' => $expiryDate
                    ]);

                    // Update Stock Summary
                    $stockSummary = StockSummary::where([
                        'inventory_item_id' => $inventoryItem->id,
                        'branch_id' => $branchId,
                        'batch_no' => null 
                    ])->lockForUpdate()->first();
                    
                    if ($stockSummary) {
                        $oldStock = (float)$stockSummary->current_stock;
                        $stockSummary->current_stock += $normalizedQuantity;
                        $newStock = (float)$stockSummary->current_stock;
                        
                        if ($newStock > 0) {
                            $stockSummary->average_cost = (($oldStock * $stockSummary->average_cost) + $totalPrice) / $newStock;
                        } else {
                            $stockSummary->average_cost = $totalPrice / $normalizedQuantity;
                        }
                        
                        $stockSummary->last_transaction_date = now();
                        $stockSummary->save();
                    } else {
                        $stockSummary = StockSummary::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'unit_id' => $itemModel->unit_id,
                            'branch_id' => $branchId,
                            'current_stock' => $normalizedQuantity,
                            'average_cost' => $totalPrice / $normalizedQuantity,
                            'batch_no' => null,
                            'expiry_date' => $expiryDate,
                            'last_transaction_date' => now()
                        ]);
                    }

                    // Update cost on item model and inventory item
                    if ($itemType == 1) {
                        $itemModel->update(['cost' => $totalPrice / $normalizedQuantity]);
                    }
                    $inventoryItem->update(['cost' => $totalPrice / $normalizedQuantity]);

                    // Insert to Stock Ledger
                    StockLedger::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'unit_id' => $itemModel->unit_id,
                        'branch_id' => $branchId,
                        'transaction_type' => 1,
                        'reference_id' => $purchaseMaster->id,
                        'reference_type' => 'purchase',
                        'qty_in' => $normalizedQuantity,
                        'qty_out' => 0,
                        'unit_cost' => $totalPrice / $normalizedQuantity,
                        'batch_no' => null,
                        'expiry_date' => $expiryDate,
                        'transaction_date' => now()
                    ]);
                }
            });

            return redirect()->route('admin.purchase.index')->with('success', 'Purchase order created and stock updated successfully.');

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Purchase Store Error: ' . $e->getMessage());
            return back()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = PurchaseMaster::with(['supplier', 'details.inventoryItem.unit'])->findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json($purchase);
        }

        return Inertia::render('Admin/Inventory/Purchase/Show', [
            'purchase' => $purchase,
            'pageTitle' => 'Purchase Order Details'
        ]);
    }
}
