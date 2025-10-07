<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $guarded = [];
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function department()
    {
        return $this->belongsTo(ResDepartment::class, 'department_id');
    }
    public function comboItems()
    {
        return $this->hasMany(ComboItemDetail::class, 'combo_id');
    }
}
