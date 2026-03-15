<?php

use App\Actions\ShoppingList\CreateShoppingListAction;
use App\Models\ShoppingList;
use App\Models\User;

test('can create shopping list', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $list = $action->handle($user, [
        'name' => 'Grocery List',
        'notes' => 'Weekly groceries',
    ]);

    expect($list)->toBeInstanceOf(ShoppingList::class);
    expect($list->name)->toBe('Grocery List');
    expect($list->notes)->toBe('Weekly groceries');
    expect($list->user_id)->toBe($user->id);
});

test('can create shopping list with is_completed flag', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $list = $action->handle($user, [
        'name' => 'Grocery List',
        'is_completed' => true,
    ]);

    expect($list->is_completed)->toBeTrue();
});

test('can create shopping list with shopping_date', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $list = $action->handle($user, [
        'name' => 'Grocery List',
        'shopping_date' => '2023-12-25',
    ]);

    expect($list->shopping_date)->toBe('2023-12-25');
});

test('name is required', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
});

test('notes are nullable', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['notes'])->toContain('nullable');
});

test('is_completed is boolean', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['is_completed'])->toContain('boolean');
});

test('shopping_date is nullable date', function () {
    $user = User::factory()->create();

    $action = app(CreateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['shopping_date'])->toContain('nullable');
    expect($rules['shopping_date'])->toContain('date');
});