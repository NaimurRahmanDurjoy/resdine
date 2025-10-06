<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuVariant extends Model
{
    protected $fillable = ['name', 'item_id', 'price'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }
}
