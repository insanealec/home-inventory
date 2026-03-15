<?php

use App\Actions\ShoppingCategory\CreateShoppingCategoryAction;
use App\Models\ShoppingCategory;
use App\Models\User;

test('can create a shopping category with required field', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingCategoryAction::class);
    $category = $action->handle($user, ['name' => 'Produce']);

    expect($category)->toBeInstanceOf(ShoppingCategory::class);
    expect($category->name)->toBe('Produce');
    expect($category->user_id)->toBe($user->id);
    $this->assertDatabaseHas('shopping_categories', [
        'name' => 'Produce',
        'user_id' => $user->id,
    ]);
});

test('can create a shopping category with all optional fields', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingCategoryAction::class);
    $category = $action->handle($user, [
        'name' => 'Fruits',
        'store_section' => 'Aisle 3',
        'color' => '#FF5733',
        'sort_order' => 5,
    ]);

    expect($category->name)->toBe('Fruits');
    expect($category->store_section)->toBe('Aisle 3');
    expect($category->color)->toBe('#FF5733');
    expect($category->sort_order)->toBe(5);
});

test('name is required', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingCategoryAction::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
});

test('name has max length constraint', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingCategoryAction::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('max:255');
});

test('optional fields have correct validation rules', function () {
    $action = app(CreateShoppingCategoryAction::class);
    $rules = $action->rules();

    expect($rules['store_section'])->toContain('nullable');
    expect($rules['store_section'])->toContain('max:255');
    expect($rules['color'])->toContain('nullable');
    expect($rules['color'])->toContain('max:50');
    expect($rules['sort_order'])->toContain('nullable');
    expect($rules['sort_order'])->toContain('integer');
    expect($rules['sort_order'])->toContain('min:0');
});
