<?php

use App\Actions\InventoryItem\DeleteItem;
use App\Models\InventoryItem;
use App\Models\ShoppingListItem;
use App\Models\ShoppingList;
use App\Models\User;

test('delete soft-deletes the inventory item', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);
    $itemId = $item->id;

    $action = app(DeleteItem::class);
    $action->handle($user, $item);

    expect(InventoryItem::find($itemId))->toBeNull();
    expect(InventoryItem::withTrashed()->find($itemId))->not->toBeNull();
    expect(InventoryItem::withTrashed()->find($itemId)->deleted_at)->not->toBeNull();
});

test('delete returns true on success', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);

    $action = app(DeleteItem::class);
    $result = $action->handle($user, $item);

    expect($result)->toBeTrue();
});

test('soft-deleted item preserves shopping list item reference', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $shoppingListItem = ShoppingListItem::factory()->create([
        'shopping_list_id' => $list->id,
        'inventory_item_id' => $item->id,
    ]);

    app(DeleteItem::class)->handle($user, $item);

    $shoppingListItem->refresh();
    expect($shoppingListItem->inventory_item_id)->toBe($item->id);
});
