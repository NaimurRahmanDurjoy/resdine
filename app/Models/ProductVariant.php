<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['name', 'item_id', 'price'];

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'item_id');
    }
}
