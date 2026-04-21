<?php

namespace App\Models;

class Unit extends BaseModel
{
    protected $fillable = [
        'name', 
        'base_unit_id', 
        'conversion_factor', 
        'status'
    ];

    /**
     * Get the base unit this unit belongs to.
     */
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Get the sub-units that belong to this base unit.
     */
    public function subUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }
}
