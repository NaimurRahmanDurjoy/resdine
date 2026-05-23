<?php

namespace App\Models;

class Payroll extends BaseModel
{
    protected $fillable = [
        'employee_id', 'month', 'year', 'basic_salary', 
        'allowances', 'deductions', 'net_salary', 'payment_status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
