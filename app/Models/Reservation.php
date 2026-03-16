<?php

namespace App\Models;

class Reservation extends BaseModel
{
    protected $fillable = [
        'customer_id',
        'table_id',
        'branch_id',
        'reservation_time',
        'guests_count',
        'customer_name',
        'customer_phone',
        'special_requests',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
