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
        'preparation_status' => 'string',
        'refunded_qty' => 'integer'
    ];

    protected $fillable = [
        'order_id',
        'item_id',
        'variant_id',
        'modifiers',
        'quantity',
        'refunded_qty',
        'preparation_status',
        'unit_price',
        'total_price'
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
