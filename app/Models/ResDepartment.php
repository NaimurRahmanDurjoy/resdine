<?php

namespace App\Models;

class ResDepartment extends BaseModel
{
    protected $fillable = ['name', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
