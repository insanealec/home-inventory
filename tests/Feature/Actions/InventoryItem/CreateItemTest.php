<?php

use App\Actions\InventoryItem\CreateItem;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use App\Models\User;

test('can create inventory item', function () {
    $user = User::factory()->create();

    $action = app(CreateItem::class);
    $item = $action->handle($user, [
        'name' => 'Test Item',
        'sku' => 'SKU123',
        'description' => 'Test Description',
        'quantity' => 10,
    ]);

    expect($item)->toBeInstanceOf(InventoryItem::class);
    expect($item->name)->toBe('Test Item');
    expect($item->sku)->toBe('SKU123');
    expect($item->description)->toBe('Test Description');
    expect($item->quantity)->toBe(10);
    expect($item->user_id)->toBe($user->id);
});

test('can create inventory item with stock location', function () {
    $user = User::factory()->create();
    $stockLocation = StockLocation::factory()->create(['user_id' => $user->id]);

    $action = app(CreateItem::class);
    $item = $action->handle($user, [
        'name' => 'Test Item',
        'quantity' => 5,
        'stock_location_id' => $stockLocation->id,
    ]);

    expect($item->stock_location_id)->toBe($stockLocation->id);
});

test('can create inventory item with position', function () {
    $user = User::factory()->create();

    $action = app(CreateItem::class);
    $item = $action->handle($user, [
        'name' => 'Test Item',
        'quantity' => 5,
        'position' => 'Shelf A-1',
    ]);

    expect($item->position)->toBe('Shelf A-1');
});

test('name is required', function () {
    $user = User::factory()->create();

    $action = app(CreateItem::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
});

test('quantity is required and must be non-negative', function () {
    $user = User::factory()->create();

    $action = app(CreateItem::class);
    $rules = $action->rules();

    expect($rules['quantity'])->toContain('required');
    expect($rules['quantity'])->toContain('min:0');
});

test('sku has max length constraint', function () {
    $user = User::factory()->create();

    $action = app(CreateItem::class);
    $rules = $action->rules();

    expect($rules['sku'])->toContain('max:100');
});

test('stock location must exist', function () {
    $user = User::factory()->create();

    $action = app(CreateItem::class);
    $rules = $action->rules();

    expect($rules['stock_location_id'])->toContain('exists:stock_locations,id');
});
