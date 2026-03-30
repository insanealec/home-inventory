<?php

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\ShoppingCategory;
use App\Models\InventoryItem;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});

// GET /api/shopping-lists
test('can get all shopping lists for authenticated user', function () {
    ShoppingList::factory()->count(3)->create(['user_id' => $this->user->id]);
    ShoppingList::factory()->create(['user_id' => User::factory()->create()->id]); // Other user's list

    $response = $this->getJson('/api/shopping-lists');

    $response->assertSuccessful();
    $response->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot get shopping lists', function () {
    $this->app['auth']->forgetGuards();
    $response = $this->getJson('/api/shopping-lists');

    $response->assertUnauthorized();
});

// POST /api/shopping-lists
test('can create a shopping list', function () {
    $response = $this->postJson('/api/shopping-lists', [
        'name' => 'Weekly Shop',
        'notes' => 'Groceries for the week',
        'shopping_date' => '2026-03-15',
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'Weekly Shop',
        'notes' => 'Groceries for the week',
    ]);
    $this->assertDatabaseHas('shopping_lists', [
        'name' => 'Weekly Shop',
        'user_id' => $this->user->id,
    ]);
});

test('create shopping list requires a name', function () {
    $response = $this->postJson('/api/shopping-lists', [
        'notes' => 'No name provided',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('name');
});

test('create shopping list with optional fields', function () {
    $response = $this->postJson('/api/shopping-lists', [
        'name' => 'Shopping List',
        'is_completed' => false,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('shopping_lists', [
        'name' => 'Shopping List',
        'is_completed' => false,
    ]);
});

// GET /api/shopping-lists/{id}
test('can get a single shopping list with its items', function () {
    $list = ShoppingList::factory()
        ->has(ShoppingListItem::factory()->count(2), 'items')
        ->create(['user_id' => $this->user->id]);

    $response = $this->getJson("/api/shopping-lists/{$list->id}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['id' => $list->id, 'name' => $list->name]);
    $response->assertJsonCount(2, 'items');
});

test('user cannot get another user\'s shopping list', function () {
    $otherUser = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/shopping-lists/{$list->id}");

    $response->assertForbidden();
});

// PUT /api/shopping-lists/{id}
test('can update a shopping list', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->putJson("/api/shopping-lists/{$list->id}", [
        'name' => 'Updated Name',
        'is_completed' => true,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('shopping_lists', [
        'id' => $list->id,
        'name' => 'Updated Name',
        'is_completed' => true,
    ]);
});

test('user cannot update another user\'s shopping list', function () {
    $otherUser = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->putJson("/api/shopping-lists/{$list->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertForbidden();
});

// DELETE /api/shopping-lists/{id}
test('can delete a shopping list', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/shopping-lists/{$list->id}");

    $response->assertSuccessful();
    $this->assertDatabaseMissing('shopping_lists', ['id' => $list->id]);
});

test('user cannot delete another user\'s shopping list', function () {
    $otherUser = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/shopping-lists/{$list->id}");

    $response->assertForbidden();
});

// GET /api/shopping-lists/{id}/items
test('can get all items in a shopping list', function () {
    $list = ShoppingList::factory()
        ->has(ShoppingListItem::factory()->count(3), 'items')
        ->create(['user_id' => $this->user->id]);

    $response = $this->getJson("/api/shopping-lists/{$list->id}/items");

    $response->assertSuccessful();
    $response->assertJsonCount(3);
});

test('user cannot get items from another user\'s shopping list', function () {
    $otherUser = User::factory()->create();
    $list = ShoppingList::factory()
        ->has(ShoppingListItem::factory()->count(2), 'items')
        ->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/shopping-lists/{$list->id}/items");

    $response->assertForbidden();
});

// POST /api/shopping-lists/{id}/items
test('can create an item in a shopping list', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items", [
        'name' => 'Milk',
        'quantity' => 2,
        'unit' => 'liters',
        'estimated_price' => 2.50,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('shopping_list_items', [
        'shopping_list_id' => $list->id,
        'name' => 'Milk',
        'quantity' => 2,
    ]);
});

test('create item requires name and quantity', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items", [
        'unit' => 'liters',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name', 'quantity']);
});

// POST /api/shopping-lists/{id}/items/bulk
test('can add multiple items to a shopping list', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/bulk", [
        'items' => [
            ['name' => 'Milk', 'quantity' => 1],
            ['name' => 'Bread', 'quantity' => 2],
            ['name' => 'Eggs', 'quantity' => 12],
        ],
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseCount('shopping_list_items', 3);
});

test('bulk add returns created items and errors', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/bulk", [
        'items' => [
            ['name' => 'Valid Item', 'quantity' => 1],
            ['quantity' => 2], // Missing name - will fail
            ['name' => 'Another Valid', 'quantity' => 3],
        ],
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure(['created', 'errors']);
    $response->assertJsonCount(2, 'created');
});

// PUT /api/shopping-lists/{id}/items/bulk
test('can bulk update shopping list items', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $item1 = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id, 'name' => 'Item 1']);
    $item2 = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id, 'name' => 'Item 2']);

    $response = $this->putJson("/api/shopping-lists/{$list->id}/items/bulk", [
        'updates' => [
            $item1->id => ['is_completed' => true],
            $item2->id => ['quantity' => 5],
        ],
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('shopping_list_items', [
        'id' => $item1->id,
        'is_completed' => true,
    ]);
    $this->assertDatabaseHas('shopping_list_items', [
        'id' => $item2->id,
        'quantity' => 5,
    ]);
});

// GET /api/shopping-lists/{id}/items/{itemId}
test('can get a single shopping list item', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $item = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id]);

    $response = $this->getJson("/api/shopping-lists/{$list->id}/items/{$item->id}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['id' => $item->id, 'name' => $item->name]);
});

test('user cannot get an item from another user\'s list', function () {
    $otherUser = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $otherUser->id]);
    $item = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id]);

    $response = $this->getJson("/api/shopping-lists/{$list->id}/items/{$item->id}");

    $response->assertForbidden();
});

// PUT /api/shopping-lists/{id}/items/{itemId}
test('can update a shopping list item', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $item = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id]);

    $response = $this->putJson("/api/shopping-lists/{$list->id}/items/{$item->id}", [
        'quantity' => 5,
        'is_completed' => true,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('shopping_list_items', [
        'id' => $item->id,
        'quantity' => 5,
        'is_completed' => true,
    ]);
});

// DELETE /api/shopping-lists/{id}/items/{itemId}
test('can delete a shopping list item', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $item = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id]);

    $response = $this->deleteJson("/api/shopping-lists/{$list->id}/items/{$item->id}");

    $response->assertSuccessful();
    $this->assertDatabaseMissing('shopping_list_items', ['id' => $item->id]);
});

// Category ownership
test('cannot create item with another user\'s category', function () {
    $otherUser = User::factory()->create();
    $otherCategory = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items", [
        'name' => 'Milk',
        'quantity' => 1,
        'category_id' => $otherCategory->id,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('category_id');
});

test('cannot update item with another user\'s category', function () {
    $otherUser = User::factory()->create();
    $otherCategory = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $item = ShoppingListItem::factory()->create(['shopping_list_id' => $list->id]);

    $response = $this->putJson("/api/shopping-lists/{$list->id}/items/{$item->id}", [
        'category_id' => $otherCategory->id,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('category_id');
});

test('cannot add standalone item with another user\'s category', function () {
    $otherUser = User::factory()->create();
    $otherCategory = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/standalone", [
        'name' => 'Special Item',
        'quantity' => 1,
        'category_id' => $otherCategory->id,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('category_id');
});

test('bulk add rejects items with another user\'s category', function () {
    $otherUser = User::factory()->create();
    $otherCategory = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/bulk", [
        'items' => [
            ['name' => 'Valid Item', 'quantity' => 1],
            ['name' => 'Bad Category Item', 'quantity' => 1, 'category_id' => $otherCategory->id],
        ],
    ]);

    $response->assertSuccessful();
    $response->assertJsonCount(1, 'created');
    $response->assertJsonPath('errors.1.0', fn ($msg) => str_contains($msg, 'category'));
});

// POST /api/shopping-lists/{id}/items/from-inventory
test('can add an inventory item to a shopping list', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $inventoryItem = InventoryItem::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/from-inventory", [
        'inventory_item_id' => $inventoryItem->id,
        'quantity' => 2,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('shopping_list_items', [
        'shopping_list_id' => $list->id,
        'inventory_item_id' => $inventoryItem->id,
        'quantity' => 2,
    ]);
});

test('cannot add another user\'s inventory item to shopping list', function () {
    $otherUser = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);
    $inventoryItem = InventoryItem::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/from-inventory", [
        'inventory_item_id' => $inventoryItem->id,
        'quantity' => 2,
    ]);

    $response->assertUnprocessable();
});

// POST /api/shopping-lists/{id}/items/standalone
test('can add a standalone item to a shopping list', function () {
    $list = ShoppingList::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson("/api/shopping-lists/{$list->id}/items/standalone", [
        'name' => 'Something Special',
        'quantity' => 1,
        'notes' => 'From special store',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('shopping_list_items', [
        'shopping_list_id' => $list->id,
        'name' => 'Something Special',
        'inventory_item_id' => null,
    ]);
});
