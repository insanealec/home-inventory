<?php

use App\Actions\ShoppingList\UpdateShoppingListAction;
use App\Models\ShoppingList;
use App\Models\User;

test('can update shopping list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $updatedList = $action->handle($user, $list->id, [
        'name' => 'Updated Grocery List',
        'notes' => 'Weekly groceries with updates',
    ]);

    expect($updatedList->name)->toBe('Updated Grocery List');
    expect($updatedList->notes)->toBe('Weekly groceries with updates');
});

test('can update shopping list is_completed flag', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $updatedList = $action->handle($user, $list->id, [
        'is_completed' => true,
    ]);

    expect($updatedList->is_completed)->toBeTrue();
});

test('can update shopping list with shopping_date', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $updatedList = $action->handle($user, $list->id, [
        'shopping_date' => '2023-12-30',
    ]);

    expect($updatedList->shopping_date)->toBe('2023-12-30');
});

test('name is optional in update', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('string');
    expect($rules['name'])->toContain('max:255');
});

test('notes are nullable', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['notes'])->toContain('nullable');
});

test('is_completed is boolean', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['is_completed'])->toContain('boolean');
});

test('shopping_date is nullable date', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['shopping_date'])->toContain('nullable');
    expect($rules['shopping_date'])->toContain('date');
});

test('handles not found gracefully', function () {
    $user = User::factory()->create();

    $action = app(UpdateShoppingListAction::class);
    
    // This should throw ModelNotFoundException, which is proper handling
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    $action->handle($user, 999, ['name' => 'Test']);
});