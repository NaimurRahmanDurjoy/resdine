<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Models\InventoryItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromBranch', 'toBranch'])->orderBy('transfer_date', 'desc')->paginate(10);
        return Inertia::render('Admin/Inventory/Transfers/Index', [
            'transfers' => $transfers,
            'pageTitle' => 'Stock Transfers'
        ]);
    }

    public function create()
    {
        $items = InventoryItem::with('unit')->get();
        $branches = Branch::all();
        return Inertia::render('Admin/Inventory/Transfers/Create', [
            'items' => $items,
            'branches' => $branches,
            'pageTitle' => 'Initiate Stock Transfer'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $transfer = StockTransfer::create([
                    'reference_no' => 'TRF-' . strtoupper(Str::random(6)),
                    'from_branch_id' => $validated['from_branch_id'],
                    'to_branch_id' => $validated['to_branch_id'],
                    'status' => 'Pending',
                    'transfer_date' => $validated['transfer_date'],
                    'notes' => $validated['notes'],
                    'total_cost' => 0
                ]);

                $totalCost = 0;

                foreach ($validated['items'] as $itemData) {
                    $item = InventoryItem::findOrFail($itemData['inventory_item_id']);
                    
                    // Validate stock exists in from_branch
                    $stockSummary = StockSummary::where('inventory_item_id', $item->id)
                        ->where('branch_id', $validated['from_branch_id'])
                        ->first();
                        
                    if (!$stockSummary || $stockSummary->current_stock < $itemData['quantity']) {
                        throw new Exception("Insufficient stock in source branch for item: " . $item->name);
                    }

                    $unitCost = $stockSummary->average_cost;
                    $lineTotal = $unitCost * $itemData['quantity'];
                    $totalCost += $lineTotal;

                    StockTransferDetail::create([
                        'stock_transfer_id' => $transfer->id,
                        'inventory_item_id' => $item->id,
                        'unit_id' => $item->unit_id,
                        'quantity' => $itemData['quantity'],
                        'unit_cost' => $unitCost
                    ]);
                }

                $transfer->update(['total_cost' => $totalCost]);
            });

            return redirect()->route('admin.inventory.transfers.index')->with('success', 'Stock transfer initiated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dispatchAction(Request $request, StockTransfer $transfer)
    {
        if ($transfer->status !== 'Pending') {
            return back()->with('error', 'Only pending transfers can be dispatched.');
        }

        try {
            DB::transaction(function () use ($transfer) {
                foreach ($transfer->details as $detail) {
                    $stockSummary = StockSummary::where('inventory_item_id', $detail->inventory_item_id)
                        ->where('branch_id', $transfer->from_branch_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stockSummary || $stockSummary->current_stock < $detail->quantity) {
                        throw new Exception("Insufficient stock during dispatch for item ID: " . $detail->inventory_item_id);
                    }

                    $stockSummary->current_stock -= $detail->quantity;
                    $stockSummary->save();

                    StockLedger::create([
                        'inventory_item_id' => $detail->inventory_item_id,
                        'unit_id' => $detail->unit_id,
                        'branch_id' => $transfer->from_branch_id,
                        'transaction_type' => 8, // Transfer Out
                        'reference_type' => 'stock_transfer_out',
                        'reference_id' => $transfer->id,
                        'qty_in' => 0,
                        'qty_out' => $detail->quantity,
                        'unit_cost' => $detail->unit_cost,
                        'transaction_date' => now()
                    ]);
                }
                $transfer->update(['status' => 'Dispatched']);
            });
            return back()->with('success', 'Transfer dispatched successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receiveAction(Request $request, StockTransfer $transfer)
    {
        if ($transfer->status !== 'Dispatched') {
            return back()->with('error', 'Only dispatched transfers can be received.');
        }

        try {
            DB::transaction(function () use ($transfer) {
                foreach ($transfer->details as $detail) {
                    $stockSummary = StockSummary::firstOrCreate(
                        ['inventory_item_id' => $detail->inventory_item_id, 'branch_id' => $transfer->to_branch_id],
                        ['unit_id' => $detail->unit_id, 'current_stock' => 0, 'average_cost' => $detail->unit_cost]
                    );

                    $stockSummary->current_stock += $detail->quantity;
                    $stockSummary->save();

                    StockLedger::create([
                        'inventory_item_id' => $detail->inventory_item_id,
                        'unit_id' => $detail->unit_id,
                        'branch_id' => $transfer->to_branch_id,
                        'transaction_type' => 9, // Transfer In
                        'reference_type' => 'stock_transfer_in',
                        'reference_id' => $transfer->id,
                        'qty_in' => $detail->quantity,
                        'qty_out' => 0,
                        'unit_cost' => $detail->unit_cost,
                        'transaction_date' => now()
                    ]);
                }
                $transfer->update(['status' => 'Received']);
            });
            return back()->with('success', 'Transfer received successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
