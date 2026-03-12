<?php

namespace App\Models;

class StockMaster extends BaseModel
{
    protected $table = 'stock_master';

    protected $fillable = [
        'ingredient_id',
        'current_stock',
        'total_in',
        'total_out',
        'last_updated'
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
