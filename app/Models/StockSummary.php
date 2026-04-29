<?php

namespace App\Models;

class StockSummary extends BaseModel
{
    protected $table = 'stock_summary';

    protected $fillable = [
        'inventory_item_id',
        'unit_id',
        'branch_id',
        'current_stock',
        'average_cost',
        'batch_no',
        'expiry_date',
        'last_transaction_date'
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
