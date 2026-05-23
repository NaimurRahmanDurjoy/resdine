<?php

namespace App\Models;

class Attendance extends BaseModel
{
    protected $fillable = [
        'employee_id', 'date', 'check_in', 'check_out', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
