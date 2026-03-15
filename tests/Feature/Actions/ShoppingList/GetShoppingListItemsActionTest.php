<?php

use App\Actions\ShoppingList\GetShoppingListItemsAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

test('returns all items in a shopping list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $list->items()->create(['name' => 'Milk', 'quantity' => 1]);
    $list->items()->create(['name' => 'Bread', 'quantity' => 2]);

    $result = app(GetShoppingListItemsAction::class)->handle($user, $list->id);

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(2);
});

test('returns empty collection when list has no items', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(GetShoppingListItemsAction::class)->handle($user, $list->id);

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toBeEmpty();
});

test('throws if list not found', function () {
    $user = User::factory()->create();

    app(GetShoppingListItemsAction::class)->handle($user, 9999);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('cannot access another users list items', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    app(GetShoppingListItemsAction::class)->handle($other, $list->id);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('eager loads inventory item relationship', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $inventoryItem = $user->inventoryItems()->create(['name' => 'Milk', 'quantity' => 10]);
    $list->items()->create(['name' => 'Milk', 'quantity' => 1, 'inventory_item_id' => $inventoryItem->id]);

    $result = app(GetShoppingListItemsAction::class)->handle($user, $list->id);

    expect($result->first()->relationLoaded('inventoryItem'))->toBeTrue()
        ->and($result->first()->inventoryItem->name)->toBe('Milk');
});

test('orders items by sort_order then created_at', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $list->items()->create(['name' => 'C', 'quantity' => 1, 'sort_order' => 3]);
    $list->items()->create(['name' => 'A', 'quantity' => 1, 'sort_order' => 1]);
    $list->items()->create(['name' => 'B', 'quantity' => 1, 'sort_order' => 2]);

    $result = app(GetShoppingListItemsAction::class)->handle($user, $list->id);

    expect($result->pluck('name')->all())->toBe(['A', 'B', 'C']);
});
