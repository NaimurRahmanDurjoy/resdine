<?php

namespace App\Models;

class RecipeItem extends BaseModel
{
    protected $table = 'recipe_items';

    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'sub_product_id',
        'quantity',
        'unit_id',
        'wastage_percentage',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function subProduct()
    {
        return $this->belongsTo(ProductItem::class, 'sub_product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
