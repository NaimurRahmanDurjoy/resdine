<?php

namespace App\Models;

class PurchaseDetail extends BaseModel
{
    protected $table = 'purchase_details';

    protected $fillable = [
        'purchase_id',
        'inventory_item_id',
        'unit_id',
        'quantity',
        'normalized_quantity',
        'unit_price',
        'total_price',
        'expiry_date'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseMaster::class, 'purchase_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
