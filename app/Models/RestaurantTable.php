<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = ['name', 'branch_id', 'capacity', 'status', 'section'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
