<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboItemDetail extends Model
{
    use HasFactory;

    protected $fillable = ['combo_id', 'item_id', 'quantity'];

    public function combo()
    {
        return $this->belongsTo(MenuItem::class, 'combo_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }
}
