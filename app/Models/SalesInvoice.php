<?php

namespace App\Models;

class SalesInvoice extends BaseModel
{
    protected $table = 'invoice_master';

    protected $casts = [
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'collect_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(OrderMaster::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
