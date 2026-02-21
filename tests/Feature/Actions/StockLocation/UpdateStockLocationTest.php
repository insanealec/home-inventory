<?php

use App\Actions\StockLocation\UpdateStockLocation;
use App\Models\StockLocation;
use App\Models\User;

test('can update stock location', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old Name',
        'short_name' => 'OLD',
    ]);

    $action = app(UpdateStockLocation::class);
    $updated = $action->handle($location, [
        'id' => $location->id,
        'name' => 'New Name',
        'short_name' => 'NEW',
        'description' => 'Updated description',
    ]);

    expect($updated->name)->toBe('New Name');
    expect($updated->short_name)->toBe('NEW');
    expect($updated->description)->toBe('Updated description');
});

test('can update stock location description to null', function () {
    $location = StockLocation::factory()->create([
        'description' => 'Original description',
    ]);

    $action = app(UpdateStockLocation::class);
    $updated = $action->handle($location, [
        'id' => $location->id,
        'name' => 'Location',
        'short_name' => 'LOC',
        'description' => null,
    ]);

    expect($updated->description)->toBeNull();
});

test('id is required', function () {
    $action = app(UpdateStockLocation::class);
    $rules = $action->rules();

    expect($rules['id'])->toContain('required');
    expect($rules['id'])->toContain('exists:stock_locations,id');
});

test('name is required and has max length', function () {
    $action = app(UpdateStockLocation::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
    expect($rules['name'])->toContain('max:255');
});

test('short name is required and has max length', function () {
    $action = app(UpdateStockLocation::class);
    $rules = $action->rules();

    expect($rules['short_name'])->toContain('required');
    expect($rules['short_name'])->toContain('max:50');
});

test('description is optional', function () {
    $action = app(UpdateStockLocation::class);
    $rules = $action->rules();

    expect($rules['description'])->toContain('nullable');
});
