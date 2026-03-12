<?php

namespace App\Models;

class Ingredient extends BaseModel
{
    protected $fillable = [
        'name',
        'unit_id',
        'min_stock',
        'expiry_tracking',
        'status'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
