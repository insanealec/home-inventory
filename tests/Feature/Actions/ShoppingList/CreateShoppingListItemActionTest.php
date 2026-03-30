<?php

use App\Actions\ShoppingList\CreateShoppingListItemAction;
use App\Models\ShoppingCategory;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;

test('can create a shopping list item', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $item = app(CreateShoppingListItemAction::class)->handle($user, $list->id, [
        'name' => 'Milk',
        'quantity' => 2,
        'unit' => 'litres',
    ]);

    expect($item)->toBeInstanceOf(ShoppingListItem::class)
        ->and($item->name)->toBe('Milk')
        ->and($item->quantity)->toBe(2)
        ->and($item->unit)->toBe('litres')
        ->and($item->shopping_list_id)->toBe($list->id)
        ->and($item->inventory_item_id)->toBeNull();
});

test('can create item with all optional fields', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $category = ShoppingCategory::create(['name' => 'Dairy', 'user_id' => $user->id]);

    $item = app(CreateShoppingListItemAction::class)->handle($user, $list->id, [
        'name' => 'Cheese',
        'quantity' => 1,
        'unit' => 'kg',
        'notes' => 'Mature cheddar',
        'estimated_price' => 4.99,
        'category_id' => $category->id,
        'priority' => 3,
        'sort_order' => 5,
    ]);

    expect($item->notes)->toBe('Mature cheddar')
        ->and($item->estimated_price)->toBe(4.99)
        ->and($item->category_id)->toBe($category->id)
        ->and($item->priority)->toBe(3)
        ->and($item->sort_order)->toBe(5);
});

test('item is stored in the database', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    app(CreateShoppingListItemAction::class)->handle($user, $list->id, [
        'name' => 'Eggs',
        'quantity' => 12,
    ]);

    expect(ShoppingListItem::where('shopping_list_id', $list->id)->where('name', 'Eggs')->exists())->toBeTrue();
});

test('throws if shopping list not found', function () {
    $user = User::factory()->create();

    app(CreateShoppingListItemAction::class)->handle($user, 9999, ['name' => 'X', 'quantity' => 1]);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('cannot create item on another users list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    app(CreateShoppingListItemAction::class)->handle($other, $list->id, ['name' => 'X', 'quantity' => 1]);
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('name is required', function () {
    $rules = app(CreateShoppingListItemAction::class)->rules();

    expect($rules['name'])->toContain('required');
});

test('quantity must be a positive integer', function () {
    $rules = app(CreateShoppingListItemAction::class)->rules();

    expect($rules['quantity'])->toContain('required')
        ->and($rules['quantity'])->toContain('integer')
        ->and($rules['quantity'])->toContain('min:1');
});

test('category_id rule is nullable', function () {
    $rules = app(CreateShoppingListItemAction::class)->rules();

    expect($rules['category_id'])->toContain('nullable');
});
