<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasImage;

    protected $fillable = ['name', 'status', 'image'];
    protected $appends = ['image_url'];
}
