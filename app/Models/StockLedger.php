<?php

namespace App\Models;

class StockLedger extends BaseModel
{
    protected $table = 'stock_ledger';

    protected $fillable = [
        'ingredient_id',
        'transaction_type', // 'in' or 'out'
        'quantity',
        'reference_type', // e.g. 'purchase', 'sales', 'adjustment'
        'reference_id', // ID of the purchase master or order
        'notes',
        'transaction_date'
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
