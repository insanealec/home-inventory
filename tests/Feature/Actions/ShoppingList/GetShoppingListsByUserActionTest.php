<?php

use App\Actions\ShoppingList\GetShoppingListsByUserAction;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

test('returns paginated shopping lists for a user', function () {
    $user = User::factory()->create();
    ShoppingList::factory()->create(['user_id' => $user->id, 'name' => 'List A']);
    ShoppingList::factory()->create(['user_id' => $user->id, 'name' => 'List B']);

    $result = app(GetShoppingListsByUserAction::class)->handle($user);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($result->total())->toBe(2);
});

test('returns empty paginator when user has no lists', function () {
    $user = User::factory()->create();

    $result = app(GetShoppingListsByUserAction::class)->handle($user);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($result->total())->toBe(0)
        ->and($result->items())->toBeEmpty();
});

test('does not return other users lists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    ShoppingList::factory()->create(['user_id' => $other->id]);

    $result = app(GetShoppingListsByUserAction::class)->handle($user);

    expect($result->total())->toBe(0);
});

test('orders lists by most recent first', function () {
    $user = User::factory()->create();
    $older = ShoppingList::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDays(2)]);
    $newer = ShoppingList::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

    $result = app(GetShoppingListsByUserAction::class)->handle($user);

    expect($result->items()[0]->id)->toBe($newer->id)
        ->and($result->items()[1]->id)->toBe($older->id);
});

test('includes items count on each list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $list->items()->create(['name' => 'Milk', 'quantity' => 1]);
    $list->items()->create(['name' => 'Eggs', 'quantity' => 12]);

    $result = app(GetShoppingListsByUserAction::class)->handle($user);

    expect($result->items()[0]->items_count)->toBe(2);
});
