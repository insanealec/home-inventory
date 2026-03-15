<?php

use App\Actions\ShoppingList\AddInventoryItemToShoppingListAction;
use App\Models\InventoryItem;
use App\Models\ShoppingList;
use App\Models\User;

test('can add inventory item to shopping list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $inventoryItem = $user->inventoryItems()->create([
        'name' => 'Test Inventory Item',
        'quantity' => 10,
        'unit' => 'kg',
        'description' => 'Test description',
    ]);

    $action = app(AddInventoryItemToShoppingListAction::class);
    $item = $action->handle($user, $list->id, $inventoryItem->id, 2);

    expect($item)->toBeInstanceOf(App\Models\ShoppingListItem::class);
    expect($item->name)->toBe('Test Inventory Item');
    expect($item->quantity)->toBe(2);
    expect($item->unit)->toBe('kg');
    expect($item->notes)->toBe('Test description');
    expect($item->inventory_item_id)->toBe($inventoryItem->id);
    expect($item->shopping_list_id)->toBe($list->id);
});

test('can add inventory item with different quantity', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $inventoryItem = $user->inventoryItems()->create([
        'name' => 'Test Inventory Item',
        'quantity' => 10,
        'unit' => 'kg',
        'description' => 'Test description',
    ]);

    $action = app(AddInventoryItemToShoppingListAction::class);
    $item = $action->handle($user, $list->id, $inventoryItem->id, 5);

    expect($item->quantity)->toBe(5);
});

test('validates shopping list exists', function () {
    $user = User::factory()->create();
    $inventoryItem = $user->inventoryItems()->create([
        'name' => 'Test Inventory Item',
        'quantity' => 10,
    ]);

    $action = app(AddInventoryItemToShoppingListAction::class);
    
    // This should throw ModelNotFoundException, which is proper handling
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    $action->handle($user, 999, $inventoryItem->id, 1);
});

test('validates inventory item exists', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(AddInventoryItemToShoppingListAction::class);
    
    // This should throw ModelNotFoundException, which is proper handling
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    $action->handle($user, $list->id, 999, 1);
});

test('quantity must be positive integer', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $inventoryItem = $user->inventoryItems()->create([
        'name' => 'Test Inventory Item',
        'quantity' => 10,
    ]);

    $action = app(AddInventoryItemToShoppingListAction::class);
    $rules = $action->rules();

    expect($rules['quantity'])->toContain('required');
    expect($rules['quantity'])->toContain('integer');
    expect($rules['quantity'])->toContain('min:1');
});