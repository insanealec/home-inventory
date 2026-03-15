<?php

namespace Database\Factories;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingListFactory extends Factory
{
    protected $model = ShoppingList::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'notes' => $this->faker->paragraph(),
            'is_completed' => false,
            'shopping_date' => null,
            'user_id' => User::factory(),
        ];
    }
}