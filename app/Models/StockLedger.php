<?php

namespace App\Models;

class StockLedger extends BaseModel
{
    protected $table = 'stock_ledger';

    protected $fillable = [
        'ingredient_id',
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

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
