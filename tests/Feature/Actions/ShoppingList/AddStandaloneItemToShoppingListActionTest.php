<?php

use App\Actions\ShoppingList\AddStandaloneItemToShoppingListAction;
use App\Models\ShoppingList;
use App\Models\User;

test('can add standalone item to shopping list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(AddStandaloneItemToShoppingListAction::class);
    $item = $action->handle($user, $list->id, [
        'name' => 'Standalone Test Item',
        'quantity' => 3,
        'unit' => 'pieces',
        'notes' => 'Test standalone item',
    ]);

    expect($item)->toBeInstanceOf(App\Models\ShoppingListItem::class);
    expect($item->name)->toBe('Standalone Test Item');
    expect($item->quantity)->toBe(3);
    expect($item->unit)->toBe('pieces');
    expect($item->notes)->toBe('Test standalone item');
    expect($item->inventory_item_id)->toBeNull();
    expect($item->shopping_list_id)->toBe($list->id);
});

test('can add standalone item with category', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $category = $user->shoppingCategories()->create([
        'name' => 'Produce',
    ]);

    $action = app(AddStandaloneItemToShoppingListAction::class);
    $item = $action->handle($user, $list->id, [
        'name' => 'Standalone Item with Category',
        'quantity' => 2,
        'category_id' => $category->id,
    ]);

    expect($item->category_id)->toBe($category->id);
});

test('validates name is required', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(AddStandaloneItemToShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
    expect($rules['name'])->toContain('string');
    expect($rules['name'])->toContain('max:255');
});

test('validates quantity is required positive integer', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(AddStandaloneItemToShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['quantity'])->toContain('required');
    expect($rules['quantity'])->toContain('integer');
    expect($rules['quantity'])->toContain('min:1');
});

test('ensures inventory_item_id is null for standalone items', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(AddStandaloneItemToShoppingListAction::class);
    $item = $action->handle($user, $list->id, [
        'name' => 'Test Item',
        'quantity' => 1,
        // Explicitly setting inventory_item_id to null in the handler
    ]);

    expect($item->inventory_item_id)->toBeNull();
});