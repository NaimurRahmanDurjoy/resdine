<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PurchaseMaster;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\StockSummary;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseController extends Controller
{
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
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date'
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Determine branch_id safely
                $branchId = auth()->user()->branch_id;
                if (!$branchId) {
                    // Fallback to first branch if admin has no branch assigned
                    $branch = Branch::first();
                    $branchId = $branch ? $branch->id : null;
                }

                if (!$branchId) {
                    throw new Exception('A branch must be assigned to store a purchase.');
                }

                $totalAmount = 0;
                
                // Preliminary calculation
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
                    'status' => 2 // 2 = Received
                ]);

                // 2. Create Details and Update Stock
                foreach ($validated['items'] as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    $ingredient = Ingredient::findOrFail($item['ingredient_id']);
                    
                    // Normalize empty date string to null
                    $expiryDate = !empty($item['expiry_date']) ? $item['expiry_date'] : null;

                    // Record detail
                    PurchaseDetail::create([
                        'purchase_id' => $purchaseMaster->id,
                        'ingredients_id' => $item['ingredient_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice,
                        'expiry_date' => $expiryDate
                    ]);

                    // Update Stock Summary
                    $stockSummary = StockSummary::where([
                        'ingredient_id' => $item['ingredient_id'],
                        'branch_id' => $branchId,
                        'batch_no' => null 
                    ])->lockForUpdate()->first();
                    
                    if ($stockSummary) {
                        $stockSummary->current_stock += $item['quantity'];
                        // Calculate new average cost (simplified)
                        $totalStock = $stockSummary->current_stock;
                        if ($totalStock > 0) {
                            $stockSummary->average_cost = (($stockSummary->average_cost * ($totalStock - $item['quantity'])) + $totalPrice) / $totalStock;
                        }
                        $stockSummary->last_transaction_date = now();
                        $stockSummary->save();
                    } else {
                        StockSummary::create([
                            'ingredient_id' => $item['ingredient_id'],
                            'unit_id' => $ingredient->unit_id,
                            'branch_id' => $branchId,
                            'current_stock' => $item['quantity'],
                            'average_cost' => $item['unit_price'],
                            'batch_no' => null,
                            'expiry_date' => $expiryDate,
                            'last_transaction_date' => now()
                        ]);
                    }

                    // Insert to Stock Ledger
                    StockLedger::create([
                        'ingredient_id' => $item['ingredient_id'],
                        'unit_id' => $ingredient->unit_id,
                        'branch_id' => $branchId,
                        'transaction_type' => 1, // 1=purchase
                        'reference_id' => $purchaseMaster->id,
                        'reference_type' => 'purchase',
                        'qty_in' => $item['quantity'],
                        'qty_out' => 0,
                        'unit_cost' => $item['unit_price'],
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
        $purchase = PurchaseMaster::with(['supplier', 'details.ingredient.unit'])->findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json($purchase);
        }

        return Inertia::render('Admin/Inventory/Purchase/Show', [
            'purchase' => $purchase,
            'pageTitle' => 'Purchase Order Details'
        ]);
    }
}
