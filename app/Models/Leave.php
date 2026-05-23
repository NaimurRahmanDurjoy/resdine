<?php

namespace App\Models;

class Leave extends BaseModel
{
    protected $fillable = [
        'employee_id', 'start_date', 'end_date', 'type', 'reason', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
