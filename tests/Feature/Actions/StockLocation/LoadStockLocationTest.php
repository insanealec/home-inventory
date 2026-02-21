<?php

use App\Actions\StockLocation\LoadStockLocation;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use App\Models\User;

test('can load stock location with inventory items', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);
    InventoryItem::factory()->count(3)->create([
        'user_id' => $user->id,
        'stock_location_id' => $location->id,
    ]);

    $action = app(LoadStockLocation::class);
    $loadedLocation = $action->handle($user, $location);

    expect($loadedLocation->id)->toBe($location->id);
    expect($loadedLocation->inventoryItems)->toHaveCount(3);
});

test('returns stock location with empty inventory items', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);

    $action = app(LoadStockLocation::class);
    $loadedLocation = $action->handle($user, $location);

    expect($loadedLocation->inventoryItems)->toHaveCount(0);
});

test('loads inventory items relationship', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);
    $item = InventoryItem::factory()->create([
        'user_id' => $user->id,
        'stock_location_id' => $location->id,
        'name' => 'Test Item',
    ]);

    $action = app(LoadStockLocation::class);
    $loadedLocation = $action->handle($user, $location);

    expect($loadedLocation->inventoryItems->first()->name)->toBe('Test Item');
});
