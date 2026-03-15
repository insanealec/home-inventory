<?php

namespace Database\Factories;

use App\Models\ShoppingCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingCategoryFactory extends Factory
{
    protected $model = ShoppingCategory::class;

    public function definition(): array
    {
        return [
            'name'          => $this->faker->unique()->words(2, true),
            'store_section' => $this->faker->optional()->word(),
            'color'         => $this->faker->optional()->safeHexColor(),
            'sort_order'    => $this->faker->numberBetween(0, 10),
            'user_id'       => User::factory(),
        ];
    }
}
