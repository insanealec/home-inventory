<?php

use App\Actions\StockLocation\CreateStockLocation;
use App\Models\StockLocation;
use App\Models\User;

test('can create stock location', function () {
    $user = User::factory()->create();

    $action = app(CreateStockLocation::class);
    $location = $action->handle($user, [
        'name' => 'Warehouse A',
        'short_name' => 'WH-A',
        'description' => 'Main warehouse',
    ]);

    expect($location)->toBeInstanceOf(StockLocation::class);
    expect($location->name)->toBe('Warehouse A');
    expect($location->short_name)->toBe('WH-A');
    expect($location->description)->toBe('Main warehouse');
    expect($location->user_id)->toBe($user->id);
});

test('can create stock location with optional description', function () {
    $user = User::factory()->create();

    $action = app(CreateStockLocation::class);
    $location = $action->handle($user, [
        'name' => 'Shelf 1',
        'short_name' => 'SH-1',
    ]);

    expect($location->name)->toBe('Shelf 1');
    expect($location->description)->toBeNull();
});

test('name is required', function () {
    $user = User::factory()->create();

    $action = app(CreateStockLocation::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
    expect($rules['name'])->toContain('max:255');
});

test('short name is nullable and has max length', function () {
    $user = User::factory()->create();

    $action = app(CreateStockLocation::class);
    $rules = $action->rules();

    expect($rules['short_name'])->toContain('nullable');
    expect($rules['short_name'])->toContain('max:50');
});

test('description is optional', function () {
    $user = User::factory()->create();

    $action = app(CreateStockLocation::class);
    $rules = $action->rules();

    expect($rules['description'])->toContain('nullable');
});
