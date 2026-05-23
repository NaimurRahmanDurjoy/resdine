<?php

namespace App\Models;

class StockTransferDetail extends BaseModel
{
    protected $fillable = [
        'stock_transfer_id', 'inventory_item_id', 'unit_id', 'quantity', 'unit_cost'
    ];

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
