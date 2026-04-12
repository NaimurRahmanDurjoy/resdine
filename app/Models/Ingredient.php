<?php

namespace App\Models;

class Ingredient extends BaseModel
{
    protected $fillable = [
        'name',
        'unit_id',
        'min_stock',
        'has_expiry',
        'status'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
