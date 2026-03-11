<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ComboItemDetail extends BaseModel
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
