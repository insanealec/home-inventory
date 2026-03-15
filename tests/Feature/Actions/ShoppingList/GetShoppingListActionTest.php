<?php

use App\Actions\ShoppingList\GetShoppingListAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;

test('can retrieve a shopping list by id', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id, 'name' => 'Weekend Shop']);

    $result = app(GetShoppingListAction::class)->handle($user, $list->id);

    expect($result)->toBeInstanceOf(ShoppingList::class)
        ->and($result->id)->toBe($list->id)
        ->and($result->name)->toBe('Weekend Shop');
});

test('eager loads items', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $list->items()->create(['name' => 'Bread', 'quantity' => 1]);

    $result = app(GetShoppingListAction::class)->handle($user, $list->id);

    expect($result->relationLoaded('items'))->toBeTrue()
        ->and($result->items)->toHaveCount(1)
        ->and($result->items->first()->name)->toBe('Bread');
});

test('throws if list not found', function () {
    $user = User::factory()->create();

    app(GetShoppingListAction::class)->handle($user, 9999);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('cannot retrieve another users list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    app(GetShoppingListAction::class)->handle($other, $list->id);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
