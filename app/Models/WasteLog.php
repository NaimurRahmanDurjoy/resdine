<?php

namespace App\Models;

class WasteLog extends BaseModel
{
    protected $fillable = [
        'inventory_item_id', 'branch_id', 'unit_id', 'quantity', 'cost', 'reason', 'date', 'notes'
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
