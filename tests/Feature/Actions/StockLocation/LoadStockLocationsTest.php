<?php

use App\Actions\StockLocation\LoadStockLocations;
use App\Models\StockLocation;
use App\Models\User;

test('can load all stock locations for user', function () {
    $user = User::factory()->create();
    StockLocation::factory()->count(5)->create(['user_id' => $user->id]);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, []);

    expect($result->total())->toBe(5);
    expect($result->count())->toBe(5);
});

test('can search stock locations by name', function () {
    $user = User::factory()->create();
    StockLocation::factory()->create(['user_id' => $user->id, 'name' => 'Warehouse A']);
    StockLocation::factory()->create(['user_id' => $user->id, 'name' => 'Warehouse B']);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, ['search' => 'Warehouse A']);

    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('Warehouse A');
});

test('can search stock locations by short name', function () {
    $user = User::factory()->create();
    StockLocation::factory()->create(['user_id' => $user->id, 'short_name' => 'WH-A']);
    StockLocation::factory()->create(['user_id' => $user->id, 'short_name' => 'WH-B']);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, ['search' => 'WH-A']);

    expect($result->total())->toBe(1);
    expect($result->first()->short_name)->toBe('WH-A');
});

test('can search stock locations by description', function () {
    $user = User::factory()->create();
    StockLocation::factory()->create(['user_id' => $user->id, 'description' => 'Main storage']);
    StockLocation::factory()->create(['user_id' => $user->id, 'description' => 'Backup storage']);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, ['search' => 'Main']);

    expect($result->total())->toBe(1);
    expect($result->first()->description)->toBe('Main storage');
});

test('can sort by name ascending', function () {
    $user = User::factory()->create();
    StockLocation::factory()->create(['user_id' => $user->id, 'name' => 'Zebra']);
    StockLocation::factory()->create(['user_id' => $user->id, 'name' => 'Apple']);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, [
        'sortBy' => 'name',
        'sortDirection' => 'asc',
    ]);

    expect($result->first()->name)->toBe('Apple');
    expect($result->last()->name)->toBe('Zebra');
});

test('can sort by name descending', function () {
    $user = User::factory()->create();
    StockLocation::factory()->create(['user_id' => $user->id, 'name' => 'Apple']);
    StockLocation::factory()->create(['user_id' => $user->id, 'name' => 'Zebra']);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, [
        'sortBy' => 'name',
        'sortDirection' => 'desc',
    ]);

    expect($result->first()->name)->toBe('Zebra');
    expect($result->last()->name)->toBe('Apple');
});

test('uses pagination with custom per page', function () {
    $user = User::factory()->create();
    StockLocation::factory()->count(20)->create(['user_id' => $user->id]);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user, ['perPage' => 10]);

    expect($result->count())->toBe(10);
    expect($result->total())->toBe(20);
});

test('does not return locations from other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    StockLocation::factory()->create(['user_id' => $user1->id, 'name' => 'User1 Location']);
    StockLocation::factory()->create(['user_id' => $user2->id, 'name' => 'User2 Location']);

    $action = app(LoadStockLocations::class);
    $result = $action->handle($user1, []);

    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('User1 Location');
});
