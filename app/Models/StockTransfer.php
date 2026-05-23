<?php

namespace App\Models;

class StockTransfer extends BaseModel
{
    protected $fillable = [
        'reference_no', 'from_branch_id', 'to_branch_id', 'status', 'total_cost', 'transfer_date', 'notes'
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }
}
