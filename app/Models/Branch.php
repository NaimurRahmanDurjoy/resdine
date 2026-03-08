<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'location', 'status'];

    public function resDepartments()
    {
        return $this->hasMany(ResDepartment::class);
    }

    public function staffDepartments()
    {
        return $this->hasMany(StaffDepartment::class);
    }

    public function tables()
    {
        return $this->hasMany(RestaurantTable::class);
    }
}
