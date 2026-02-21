<?php

use App\Actions\InventoryItem\LoadItems;
use App\Data\InventoryItem\LoadItemsFilters;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use App\Models\User;

test('can load all inventory items for user', function () {
    $user = User::factory()->create();
    $items = InventoryItem::factory()->count(5)->create(['user_id' => $user->id]);

    $action = app(LoadItems::class);
    $filters = new LoadItemsFilters(
        search: null,
        stockLocationId: null,
        sortBy: 'name',
        sortDirection: 'asc',
        page: 1,
        perPage: 15,
    );

    $result = $action->handle($user, $filters);

    expect($result->total())->toBe(5);
    expect($result->count())->toBe(5);
});

test('can search inventory items by name', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Apple']);
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Banana']);

    $action = app(LoadItems::class);
    $filters = new LoadItemsFilters(
        search: 'Apple',
        stockLocationId: null,
        sortBy: 'name',
        sortDirection: 'asc',
        page: 1,
        perPage: 15,
    );

    $result = $action->handle($user, $filters);

    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('Apple');
});

test('can search inventory items by sku', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'sku' => 'ABC123']);
    InventoryItem::factory()->create(['user_id' => $user->id, 'sku' => 'XYZ789']);

    $action = app(LoadItems::class);
    $filters = new LoadItemsFilters(
        search: 'ABC',
        stockLocationId: null,
        sortBy: 'name',
        sortDirection: 'asc',
        page: 1,
        perPage: 15,
    );

    $result = $action->handle($user, $filters);

    expect($result->total())->toBe(1);
    expect($result->first()->sku)->toBe('ABC123');
});

test('can filter by stock location', function () {
    $user = User::factory()->create();
    $location1 = StockLocation::factory()->create(['user_id' => $user->id]);
    $location2 = StockLocation::factory()->create(['user_id' => $user->id]);

    InventoryItem::factory()->create(['user_id' => $user->id, 'stock_location_id' => $location1->id]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'stock_location_id' => $location2->id]);

    $action = app(LoadItems::class);
    $filters = new LoadItemsFilters(
        search: null,
        stockLocationId: $location1->id,
        sortBy: 'name',
        sortDirection: 'asc',
        page: 1,
        perPage: 15,
    );

    $result = $action->handle($user, $filters);

    expect($result->total())->toBe(1);
    expect($result->first()->stock_location_id)->toBe($location1->id);
});

test('can sort by different columns', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Banana']);
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Apple']);

    $action = app(LoadItems::class);
    $filters = new LoadItemsFilters(
        search: null,
        stockLocationId: null,
        sortBy: 'name',
        sortDirection: 'desc',
        page: 1,
        perPage: 15,
    );

    $result = $action->handle($user, $filters);

    expect($result->first()->name)->toBe('Banana');
    expect($result->last()->name)->toBe('Apple');
});

test('does not return items from other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    InventoryItem::factory()->create(['user_id' => $user1->id, 'name' => 'User1 Item']);
    InventoryItem::factory()->create(['user_id' => $user2->id, 'name' => 'User2 Item']);

    $action = app(LoadItems::class);
    $filters = new LoadItemsFilters(
        search: null,
        stockLocationId: null,
        sortBy: 'name',
        sortDirection: 'asc',
        page: 1,
        perPage: 15,
    );

    $result = $action->handle($user1, $filters);

    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('User1 Item');
});
