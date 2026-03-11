<?php

namespace App\Models;

class ProductVariant extends BaseModel
{
    protected $fillable = ['name', 'item_id', 'price'];

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'item_id');
    }
}
