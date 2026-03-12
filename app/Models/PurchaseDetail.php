<?php

namespace App\Models;

class PurchaseDetail extends BaseModel
{
    protected $table = 'purchase_details';

    protected $fillable = [
        'purchase_master_id',
        'ingredient_id',
        'quantity',
        'unit_price',
        'total_price',
        'expiry_date'
    ];

    public function purchaseMaster()
    {
        return $this->belongsTo(PurchaseMaster::class, 'purchase_master_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
