<?php

use App\Actions\ShoppingList\UpdateShoppingListItemsBulkAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;

test('can update multiple items at once', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $item1 = $list->items()->create(['name' => 'Milk', 'quantity' => 1]);
    $item2 = $list->items()->create(['name' => 'Bread', 'quantity' => 1]);

    $result = app(UpdateShoppingListItemsBulkAction::class)->handle($user, $list->id, [
        $item1->id => ['quantity' => 3],
        $item2->id => ['name' => 'Sourdough', 'quantity' => 2],
    ]);

    expect($result['updated'])->toContain($item1->id)
        ->and($result['updated'])->toContain($item2->id)
        ->and($result['errors'])->toBeEmpty();

    expect($item1->fresh()->quantity)->toBe(3);
    expect($item2->fresh()->name)->toBe('Sourdough');
    expect($item2->fresh()->quantity)->toBe(2);
});

test('returns errors for item ids not in the list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(UpdateShoppingListItemsBulkAction::class)->handle($user, $list->id, [
        9999 => ['quantity' => 5],
    ]);

    expect($result['updated'])->toBeEmpty()
        ->and($result['errors'])->toHaveKey(9999);
});

test('processes valid items even when some are invalid', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $item = $list->items()->create(['name' => 'Eggs', 'quantity' => 6]);

    $result = app(UpdateShoppingListItemsBulkAction::class)->handle($user, $list->id, [
        $item->id => ['quantity' => 12],
        9999 => ['quantity' => 1],
    ]);

    expect($result['updated'])->toContain($item->id)
        ->and($result['errors'])->toHaveKey(9999);

    expect($item->fresh()->quantity)->toBe(12);
});

test('throws if shopping list not found', function () {
    $user = User::factory()->create();

    app(UpdateShoppingListItemsBulkAction::class)->handle($user, 9999, []);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('cannot update items in another users list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);
    $item = $list->items()->create(['name' => 'Milk', 'quantity' => 1]);

    app(UpdateShoppingListItemsBulkAction::class)->handle($other, $list->id, [
        $item->id => ['quantity' => 5],
    ]);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('returns empty updated and errors for empty updates array', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = app(UpdateShoppingListItemsBulkAction::class)->handle($user, $list->id, []);

    expect($result['updated'])->toBeEmpty()
        ->and($result['errors'])->toBeEmpty();
});
