<?php

namespace App\Models;

class PurchaseDetail extends BaseModel
{
    protected $table = 'purchase_details';

    protected $fillable = [
        'purchase_id',
        'ingredients_id',
        'quantity',
        'unit_price',
        'total_price',
        'expiry_date'
    ];

    public function purchase()
    {
        return $this->belongsTo(PurchaseMaster::class, 'purchase_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredients_id');
    }
}
