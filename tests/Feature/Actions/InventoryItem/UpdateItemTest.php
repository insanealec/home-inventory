<?php

use App\Actions\InventoryItem\UpdateItem;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use App\Models\User;

test('can update inventory item', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old Name',
        'quantity' => 10,
    ]);

    $action = app(UpdateItem::class);
    $updated = $action->handle($item, [
        'id' => $item->id,
        'name' => 'New Name',
        'quantity' => 20,
        'sku' => 'SKU123',
    ]);

    expect($updated->name)->toBe('New Name');
    expect($updated->quantity)->toBe(20);
    expect($updated->sku)->toBe('SKU123');
});

test('can update inventory item with stock location', function () {
    $user = User::factory()->create();
    $stockLocation = StockLocation::factory()->create(['user_id' => $user->id]);
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateItem::class);
    $updated = $action->handle($item, [
        'id' => $item->id,
        'name' => 'Updated Item',
        'quantity' => 5,
        'stock_location_id' => $stockLocation->id,
    ]);

    expect($updated->stock_location_id)->toBe($stockLocation->id);
});

test('can update inventory item position', function () {
    $item = InventoryItem::factory()->create(['position' => 'Shelf A']);

    $action = app(UpdateItem::class);
    $updated = $action->handle($item, [
        'id' => $item->id,
        'name' => 'Item',
        'quantity' => 5,
        'position' => 'Shelf B',
    ]);

    expect($updated->position)->toBe('Shelf B');
});

test('id is required', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id]);

    $action = app(UpdateItem::class);
    $rules = $action->rules();

    expect($rules['id'])->toContain('required');
    expect($rules['id'])->toContain('exists:inventory_items,id');
});

test('name is required', function () {
    $action = app(UpdateItem::class);
    $rules = $action->rules();

    expect($rules['name'])->toContain('required');
    expect($rules['name'])->toContain('max:255');
});

test('quantity is required and must be non-negative', function () {
    $action = app(UpdateItem::class);
    $rules = $action->rules();

    expect($rules['quantity'])->toContain('required');
    expect($rules['quantity'])->toContain('min:0');
});
