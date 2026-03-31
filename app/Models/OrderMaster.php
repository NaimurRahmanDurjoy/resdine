<?php

namespace App\Models;

class OrderMaster extends BaseModel
{
    protected $table = 'order_master';

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'collect_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_master_id');
    }

    public function invoice()
    {
        return $this->hasOne(SalesInvoice::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'member_id');
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
