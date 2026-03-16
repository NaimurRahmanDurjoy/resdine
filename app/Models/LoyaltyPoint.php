<?php

namespace App\Models;

class LoyaltyPoint extends BaseModel
{
    protected $fillable = [
        'customer_id',
        'points_earned',
        'points_redeemed'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
