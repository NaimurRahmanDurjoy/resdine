<?php

namespace App\Models;

class RestaurantTable extends BaseModel
{
    protected $fillable = ['name', 'branch_id', 'capacity', 'status', 'section'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(OrderMaster::class, 'table_id');
    }
}
