<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'sku' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'quantity' => fake()->numberBetween(1, 100),
            'user_id' => \App\Models\User::factory(),
            'stock_location_id' => null,
            'position' => null,
        ];
    }
}
