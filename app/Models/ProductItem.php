<?php

namespace App\Models;

use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\ResDepartment;
use App\Models\ComboItemDetail;
use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
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
}
