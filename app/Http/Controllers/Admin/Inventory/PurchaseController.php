<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PurchaseMaster;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\StockMaster;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = PurchaseMaster::with('supplier')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Inventory/Purchase/Index', [
            'purchases' => $purchases,
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
            'invoice_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $totalAmount = 0;
                
                // Preliminary calculation
                foreach ($validated['items'] as $item) {
                    $totalAmount += ($item['quantity'] * $item['unit_price']);
                }

                // 1. Create Purchase Master
                $purchaseMaster = PurchaseMaster::create([
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_date' => $validated['purchase_date'],
                    'invoice_no' => $validated['invoice_no'] ?? 'PO-' . time(),
                    'total_amount' => $totalAmount,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'received'
                ]);

                // 2. Create Details and Update Stock
                foreach ($validated['items'] as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];

                    // Record detail
                    PurchaseDetail::create([
                        'purchase_master_id' => $purchaseMaster->id,
                        'ingredient_id' => $item['ingredient_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice,
                        'expiry_date' => $item['expiry_date'] ?? null
                    ]);

                    // Update Stock Master
                    $stockMaster = StockMaster::where('ingredient_id', $item['ingredient_id'])->first();
                    
                    if ($stockMaster) {
                        $stockMaster->current_stock += $item['quantity'];
                        $stockMaster->total_in += $item['quantity'];
                        $stockMaster->save();
                    } else {
                        StockMaster::create([
                            'ingredient_id' => $item['ingredient_id'],
                            'current_stock' => $item['quantity'],
                            'total_in' => $item['quantity'],
                            'total_out' => 0
                        ]);
                    }

                    // Insert to Stock Ledger
                    StockLedger::create([
                        'ingredient_id' => $item['ingredient_id'],
                        'transaction_type' => 'in',
                        'quantity' => $item['quantity'],
                        'reference_type' => 'purchase',
                        'reference_id' => $purchaseMaster->id,
                        'notes' => 'Purchase Order #' . $purchaseMaster->id,
                        'transaction_date' => now()
                    ]);
                }
            });

            return redirect()->route('admin.purchase.index')->with('success', 'Purchase order created and stock updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = PurchaseMaster::with(['supplier', 'details.ingredient.unit'])->findOrFail($id);
        
        return Inertia::render('Admin/Inventory/Purchase/Show', [
            'purchase' => $purchase,
            'pageTitle' => 'Purchase Order Details'
        ]);
    }
}
