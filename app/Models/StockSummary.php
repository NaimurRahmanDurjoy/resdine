<?php

namespace App\Models;

class StockSummary extends BaseModel
{
    protected $table = 'stock_summary';

    protected $fillable = [
        'ingredient_id',
        'unit_id',
        'branch_id',
        'current_stock',
        'average_cost',
        'batch_no',
        'expiry_date',
        'last_transaction_date'
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
