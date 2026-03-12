<?php

namespace App\Models;

class Supplier extends BaseModel
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'company_name'
    ];
}
