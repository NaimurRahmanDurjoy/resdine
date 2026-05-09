<?php

namespace App\Models;

class RecipeItem extends BaseModel
{
    protected $table = 'recipe_items';

    protected $fillable = [
        'recipe_id',
        'inventory_item_id',
        'quantity',
        'unit_id',
        'wastage_percentage',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
