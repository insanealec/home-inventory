<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'stock_location_id',
        'position',
        'description',
        'quantity',
        'reorder_point',
        'reorder_quantity',
        'min_stock_level',
        'max_stock_level',
        'unit_price',
        'unit',
        'expiration_date',
        'user_id',
    ];

    public function stockLocation()
    {
        return $this->belongsTo(StockLocation::class);
    }

    public function shoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->user_id) {
                throw new \InvalidArgumentException('User ID is required');
            }
        });
    }
}
