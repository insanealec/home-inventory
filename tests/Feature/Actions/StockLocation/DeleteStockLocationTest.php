<?php

use App\Actions\StockLocation\DeleteStockLocation;
use App\Models\StockLocation;
use App\Models\User;

test('can delete stock location', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);
    $locationId = $location->id;

    $action = app(DeleteStockLocation::class);
    $result = $action->handle($user, $location);

    expect($result)->toBeTrue();
    expect(StockLocation::find($locationId))->toBeNull();
});

test('delete returns true on success', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);

    $action = app(DeleteStockLocation::class);
    $result = $action->handle($user, $location);

    expect($result)->toBeTrue();
});
