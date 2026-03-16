<?php

namespace App\Models;

class Membership extends BaseModel
{
    protected $fillable = [
        'name',
        'discount_percentage',
        'loyalty_multiplier',
        'min_points',
        'max_points',
        'status'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_memberships')
            ->withPivot(['card_no', 'start_date', 'end_date'])
            ->withTimestamps();
    }

    public static function getTierByPoints($points)
    {
        return self::where('status', 1)
            ->where('min_points', '<=', $points)
            ->where(function ($query) use ($points) {
                $query->where('max_points', '>=', $points)
                    ->orWhereNull('max_points');
            })
            ->first();
    }
}
