<?php

namespace App\Models;

class Ingredient extends BaseModel
{
    protected $fillable = [
        'name',
        'unit_id',
        'cost',
        'min_stock',
        'has_expiry',
        'status'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function inventoryItem()
    {
        return $this->hasOne(InventoryItem::class, 'reference_id')->where('item_type', 1);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'ingredients_id');
    }
}
