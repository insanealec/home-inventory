<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShoppingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'store_section',
        'color',
        'sort_order',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
