<?php

use App\Actions\ShoppingList\AddMultipleItemsToShoppingListAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;

test('can add multiple items to a shopping list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(AddMultipleItemsToShoppingListAction::class)->handle($user, $list->id, [
        ['name' => 'Milk', 'quantity' => 2],
        ['name' => 'Eggs', 'quantity' => 12],
        ['name' => 'Bread', 'quantity' => 1],
    ]);

    expect($result['created'])->toHaveCount(3)
        ->and($result['errors'])->toBeEmpty();

    expect(ShoppingListItem::where('shopping_list_id', $list->id)->count())->toBe(3);
});

test('returns errors for invalid items without stopping valid ones', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(AddMultipleItemsToShoppingListAction::class)->handle($user, $list->id, [
        ['name' => 'Milk', 'quantity' => 2],
        ['quantity' => 1],           // missing name — invalid
        ['name' => 'Eggs', 'quantity' => -5], // negative quantity — invalid
    ]);

    expect($result['created'])->toHaveCount(1)
        ->and($result['errors'])->toHaveKey(1)
        ->and($result['errors'])->toHaveKey(2);

    expect(ShoppingListItem::where('shopping_list_id', $list->id)->count())->toBe(1);
});

test('throws if shopping list not found', function () {
    $user = User::factory()->create();

    app(AddMultipleItemsToShoppingListAction::class)->handle($user, 9999, [
        ['name' => 'Milk', 'quantity' => 1],
    ]);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('cannot add items to another users list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    app(AddMultipleItemsToShoppingListAction::class)->handle($other, $list->id, [
        ['name' => 'Milk', 'quantity' => 1],
    ]);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('returns empty created and errors for empty items array', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(AddMultipleItemsToShoppingListAction::class)->handle($user, $list->id, []);

    expect($result['created'])->toBeEmpty()
        ->and($result['errors'])->toBeEmpty();
});

test('created items belong to the correct list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(AddMultipleItemsToShoppingListAction::class)->handle($user, $list->id, [
        ['name' => 'Butter', 'quantity' => 1],
    ]);

    expect($result['created'][0]->shopping_list_id)->toBe($list->id);
});
