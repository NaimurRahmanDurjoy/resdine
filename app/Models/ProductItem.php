<?php

namespace App\Models;

use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\ResDepartment;
use App\Models\ComboItemDetail;
use App\Traits\HasImage;
class ProductItem extends BaseModel
{
    use HasImage;

    protected $guarded = [];
    protected $appends = ['image_url'];
    protected string $imageField = 'menu_img';

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
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
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'item_id');
    }
    public function recipe()
    {
        return $this->hasOne(Recipe::class, 'menu_item_id');
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'menu_item_id');
    }

    /**
     * Recipes where this product item is used as a sub-component.
     */
    public function usedInRecipes()
    {
        return $this->hasMany(RecipeItem::class, 'sub_product_id');
    }

    public function scopePrepItems($query)
    {
        return $query->where('is_prep_item', true);
    }
}
