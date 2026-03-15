<?php

use App\Actions\ShoppingCategory\GetShoppingCategoriesAction;
use App\Models\ShoppingCategory;
use App\Models\User;

test('can get all shopping categories for a user', function () {
    $user = User::factory()->create();
    ShoppingCategory::factory()->count(3)->create(['user_id' => $user->id]);
    ShoppingCategory::factory()->count(2)->create(['user_id' => User::factory()->create()->id]);

    $action = app(GetShoppingCategoriesAction::class);
    $categories = $action->handle($user);

    expect($categories)->toHaveCount(3);
    expect($categories->pluck('user_id')->unique()->all())->toBe([$user->id]);
});

test('categories are ordered by sort_order then name', function () {
    $user = User::factory()->create();
    ShoppingCategory::factory()->create(['user_id' => $user->id, 'name' => 'Fruits', 'sort_order' => 2]);
    ShoppingCategory::factory()->create(['user_id' => $user->id, 'name' => 'Vegetables', 'sort_order' => 1]);
    ShoppingCategory::factory()->create(['user_id' => $user->id, 'name' => 'Aisle', 'sort_order' => 1]);

    $action = app(GetShoppingCategoriesAction::class);
    $categories = $action->handle($user);

    $names = $categories->pluck('name')->all();
    expect($names[0])->toBe('Aisle');
    expect($names[1])->toBe('Vegetables');
    expect($names[2])->toBe('Fruits');
});

test('returns empty collection when user has no categories', function () {
    $user = User::factory()->create();

    $action = app(GetShoppingCategoriesAction::class);
    $categories = $action->handle($user);

    expect($categories)->toHaveCount(0);
});
