<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\WasteLog;
use App\Models\InventoryItem;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class WasteManagementController extends Controller
{
    public function index()
    {
        $wasteLogs = WasteLog::with(['inventoryItem', 'branch', 'unit'])->orderBy('date', 'desc')->paginate(10);
        return Inertia::render('Admin/Inventory/Waste/Index', [
            'wasteLogs' => $wasteLogs,
            'pageTitle' => 'Waste Management'
        ]);
    }

    public function create()
    {
        $items = InventoryItem::with('unit')->get();
        $branches = Branch::all();
        return Inertia::render('Admin/Inventory/Waste/Create', [
            'items' => $items,
            'branches' => $branches,
            'pageTitle' => 'Log Waste'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $item = InventoryItem::findOrFail($validated['inventory_item_id']);
                
                $stockSummary = StockSummary::where('inventory_item_id', $item->id)
                    ->where('branch_id', $validated['branch_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$stockSummary || $stockSummary->current_stock < $validated['quantity']) {
                    throw new Exception("Insufficient stock to log waste for this item.");
                }

                $cost = $stockSummary->average_cost * $validated['quantity'];

                WasteLog::create([
                    'inventory_item_id' => $item->id,
                    'branch_id' => $validated['branch_id'],
                    'unit_id' => $item->unit_id,
                    'quantity' => $validated['quantity'],
                    'cost' => $cost,
                    'reason' => $validated['reason'],
                    'date' => $validated['date'],
                    'notes' => $validated['notes']
                ]);

                $stockSummary->current_stock -= $validated['quantity'];
                $stockSummary->save();

                StockLedger::create([
                    'inventory_item_id' => $item->id,
                    'unit_id' => $item->unit_id,
                    'branch_id' => $validated['branch_id'],
                    'transaction_type' => 7, // Assuming 7 is waste
                    'reference_type' => 'waste_log',
                    'qty_in' => 0,
                    'qty_out' => $validated['quantity'],
                    'unit_cost' => $stockSummary->average_cost,
                    'transaction_date' => now(),
                    'reason' => $validated['reason'],
                    'notes' => $validated['notes']
                ]);
            });

            return redirect()->route('admin.inventory.waste.index')->with('success', 'Waste logged successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
