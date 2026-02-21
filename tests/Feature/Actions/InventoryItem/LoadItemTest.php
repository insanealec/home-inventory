<?php

use App\Actions\InventoryItem\LoadItem;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use App\Models\User;

test('can load inventory item with stock location', function () {
    $user = User::factory()->create();
    $stockLocation = StockLocation::factory()->create(['user_id' => $user->id]);
    $item = InventoryItem::factory()->create([
        'user_id' => $user->id,
        'stock_location_id' => $stockLocation->id,
    ]);

    $action = app(LoadItem::class);
    $loadedItem = $action->handle($user, $item);

    expect($loadedItem->id)->toBe($item->id);
    expect($loadedItem->stockLocation)->toBeInstanceOf(StockLocation::class);
    expect($loadedItem->stockLocation->id)->toBe($stockLocation->id);
});

test('returns item without stock location when it is null', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create([
        'user_id' => $user->id,
        'stock_location_id' => null,
    ]);

    $action = app(LoadItem::class);
    $loadedItem = $action->handle($user, $item);

    expect($loadedItem->id)->toBe($item->id);
    expect($loadedItem->stockLocation)->toBeNull();
});
