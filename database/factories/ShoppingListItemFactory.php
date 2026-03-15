<?php

namespace Database\Factories;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingListItemFactory extends Factory
{
    protected $model = ShoppingListItem::class;

    public function definition(): array
    {
        return [
            'shopping_list_id' => ShoppingList::factory(),
            'name'             => $this->faker->words(3, true),
            'quantity'         => $this->faker->numberBetween(1, 10),
            'unit'             => null,
            'is_completed'     => false,
            'category_id'      => null,
            'notes'            => null,
            'estimated_price'  => null,
            'priority'         => 1,
            'inventory_item_id' => null,
            'sort_order'       => 0,
        ];
    }
}
