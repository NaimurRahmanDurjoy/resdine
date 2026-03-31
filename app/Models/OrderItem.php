<?php

namespace App\Models;

class OrderItem extends BaseModel
{
    protected $table = 'order_details';

    protected $casts = [
        'modifiers' => 'json',
        'price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(OrderMaster::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductItem::class, 'item_id');
    }
}
