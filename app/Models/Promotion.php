<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends BaseModel
{
    protected $table = 'promotions';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'integer',
        'value' => 'decimal:2'
    ];

    /**
     * The product items that belong to the promotion.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(ProductItem::class, 'promotion_items', 'promotion_id', 'item_id')->withTimestamps();
    }
}
