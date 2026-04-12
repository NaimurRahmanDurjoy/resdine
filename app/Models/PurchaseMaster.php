<?php

namespace App\Models;

class PurchaseMaster extends BaseModel
{
    protected $table = 'purchase_master';

    protected $fillable = [
        'branch_id',
        'user_id',
        'supplier_id',
        'purchase_date',
        'invoice_number',
        'total_amount',
        'notes',
        'status'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = ['status_label'];

    public function getStatusLabelAttribute()
    {
        return match ((int)$this->status) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Received',
            3 => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }
}
