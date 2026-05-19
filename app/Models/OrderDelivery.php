<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    protected $table = 'order_deliveries';

    protected $fillable = [
        'order_id',
        'driver_id',
        'delivery_address',
        'contact_number',
        'latitude',
        'longitude',
        'delivery_fee',
        'estimated_delivery_time',
        'delivered_at',
        'special_instructions'
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(OrderMaster::class, 'order_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
