<?php

use App\Actions\InventoryItem\DeleteItem;
use App\Models\InventoryItem;
use App\Models\User;

test('can delete inventory item', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);
    $itemId = $item->id;

    $action = app(DeleteItem::class);
    $result = $action->handle($user, $item);

    expect($result)->toBeTrue();
    expect(InventoryItem::find($itemId))->toBeNull();
});

test('delete returns true on success', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);

    $action = app(DeleteItem::class);
    $result = $action->handle($user, $item);

    expect($result)->toBeTrue();
});
