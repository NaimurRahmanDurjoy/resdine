<?php

namespace App\Models;

class Branch extends BaseModel
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
