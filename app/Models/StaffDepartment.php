<?php

namespace App\Models;

class StaffDepartment extends BaseModel
{
    protected $fillable = ['name', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
