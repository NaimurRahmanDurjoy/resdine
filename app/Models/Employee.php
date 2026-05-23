<?php

namespace App\Models;

class Employee extends BaseModel
{
    protected $fillable = [
        'user_id', 'staff_department_id', 'employee_code', 
        'designation', 'basic_salary', 'joining_date', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(StaffDepartment::class, 'staff_department_id');
    }
}
