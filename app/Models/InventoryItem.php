<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends BaseModel
{
    use SoftDeletes;

    protected $table = 'inventory_items';

    protected $fillable = [
        'name',
        'sku',
        'unit_id',
        'item_type',
        'reference_id',
        'min_stock',
        'cost',
        'status'
    ];

    /**
     * Get the unit associated with this item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the stock summary for this item.
     */
    public function stockSummary()
    {
        return $this->hasOne(StockSummary::class, 'inventory_item_id');
    }

    /**
     * Get the stock ledger entries for this item.
     */
    public function stockLedger()
    {
        return $this->hasMany(StockLedger::class, 'inventory_item_id');
    }

    /**
     * Get the source ingredient if this is an ingredient.
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'reference_id')->where('item_type', 1);
    }

    /**
     * Get the source product if this is a product or prep item.
     */
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'reference_id')->whereIn('item_type', [2, 3]);
    }
}
