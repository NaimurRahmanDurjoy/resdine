<?php

namespace App\Models;

class PurchaseMaster extends BaseModel
{
    protected $table = 'purchase_master';

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_no',
        'total_amount',
        'notes',
        'status' // e.g. 'pending', 'received', 'cancelled'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_master_id');
    }
}
