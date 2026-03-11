<?php

namespace App\Models;

use App\Traits\HasImage;
class ProductCategory extends BaseModel
{
    use HasImage;

    protected $fillable = ['name', 'status', 'image'];
    protected $appends = ['image_url'];
}
