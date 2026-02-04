<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShoppingListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopping_list_id',
        'name',
        'quantity',
        'unit',
        'is_completed',
        'category_id',
        'notes',
        'estimated_price',
        'priority',
        'inventory_item_id',
        'sort_order',
    ];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function category()
    {
        return $this->belongsTo(ShoppingCategory::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
