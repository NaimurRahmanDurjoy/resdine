<?php

namespace App\Models;

class OrderPayment extends BaseModel
{
    protected $table = 'order_payments';

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'mfs_amount' => 'decimal:2',
        'collect_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(OrderMaster::class, 'order_master_id');
    }
}
