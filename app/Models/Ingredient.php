<?php

namespace App\Models;

class Ingredient extends BaseModel
{
    protected $fillable = [
        'name',
        'unit_id',
        'min_stock',
        'has_expiry',
        'status'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function stockSummary()
    {
        return $this->hasOne(StockSummary::class);
    }

    public function stockLedger()
    {
        return $this->hasMany(StockLedger::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'ingredients_id');
    }
}
