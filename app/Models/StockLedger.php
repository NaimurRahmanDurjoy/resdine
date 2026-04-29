<?php

namespace App\Models;

class StockLedger extends BaseModel
{
    protected $table = 'stock_ledger';

    protected $fillable = [
        'inventory_item_id',
        'unit_id',
        'branch_id',
        'transaction_type',
        'reference_id',
        'reference_type',
        'qty_in',
        'qty_out',
        'unit_cost',
        'batch_no',
        'expiry_date',
        'transaction_date'
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
