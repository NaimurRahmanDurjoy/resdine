<?php

namespace App\Models;

class Customer extends BaseModel
{
    protected $fillable = ['name', 'email', 'phone', 'status'];

    public function memberships()
    {
        return $this->belongsToMany(Membership::class, 'customer_memberships')
            ->withPivot(['card_no', 'start_date', 'end_date'])
            ->withTimestamps();
    }

    public function currentMembership()
    {
        return $this->memberships()
            ->wherePivot('start_date', '<=', now())
            ->where(function ($query) {
                $query->wherePivot('end_date', '>=', now())
                    ->orWherePivot('end_date', null);
            })
            ->first();
    }

    public function loyaltyPoints()
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->loyaltyPoints ? $this->loyaltyPoints->points_earned - $this->loyaltyPoints->points_redeemed : 0;
    }
}
