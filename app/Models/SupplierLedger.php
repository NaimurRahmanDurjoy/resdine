<?php

namespace App\Models;

class SupplierLedger extends BaseModel
{
    protected $fillable = [
        'supplier_id', 'reference_type', 'reference_id', 
        'debit_amount', 'credit_amount', 'balance', 'transaction_date', 'notes'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
