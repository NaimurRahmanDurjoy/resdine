<?php

namespace App\Models;

class PromotionItem extends BaseModel
{
    protected $table = 'promotion_items';

    protected $casts = [
        'promotion_id' => 'integer',
        'item_id' => 'integer'
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'item_id');
    }
}
