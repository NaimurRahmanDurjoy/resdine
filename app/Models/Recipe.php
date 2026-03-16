<?php

namespace App\Models;

class Recipe extends BaseModel
{
    protected $fillable = [
        'menu_item_id',
        'variant_id',
        'branch_id',
    ];

    public function menuItem()
    {
        return $this->belongsTo(ProductItem::class, 'menu_item_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class);
    }
}
