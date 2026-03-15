<?php

use App\Actions\ShoppingList\GetShoppingListItemAction;
use App\Models\ShoppingListItem;
use App\Models\ShoppingList;
use App\Models\User;

test('can get shopping list item', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $item = $list->items()->create([
        'name' => 'Test Item',
        'quantity' => 1,
    ]);

    $action = app(GetShoppingListItemAction::class);
    $retrievedItem = $action->handle($user, $item->id);

    expect($retrievedItem)->toBeInstanceOf(ShoppingListItem::class);
    expect($retrievedItem->name)->toBe('Test Item');
    expect($retrievedItem->id)->toBe($item->id);
});

test('handles not found gracefully', function () {
    $user = User::factory()->create();

    $action = app(GetShoppingListItemAction::class);
    
    // This should throw ModelNotFoundException, which is proper handling
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    $action->handle($user, 999);
});

test('returns item with inventory_item_relationship', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $inventoryItem = $user->inventoryItems()->create([
        'name' => 'Inventory Item',
        'quantity' => 5,
    ]);
    
    $item = $list->items()->create([
        'name' => 'Test Item with Inventory',
        'quantity' => 1,
        'inventory_item_id' => $inventoryItem->id,
    ]);

    $action = app(GetShoppingListItemAction::class);
    $retrievedItem = $action->handle($user, $item->id);

    expect($retrievedItem->inventoryItem)->toBeInstanceOf(App\Models\InventoryItem::class);
    expect($retrievedItem->inventoryItem->name)->toBe('Inventory Item');
});