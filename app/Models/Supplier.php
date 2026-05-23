<?php

namespace App\Models;

class Supplier extends BaseModel
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'company_name'
    ];

    public function ledgers()
    {
        return $this->hasMany(SupplierLedger::class);
    }
}
