<?php

use App\Models\ShoppingCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});

// GET /api/shopping-categories
test('can get all shopping categories', function () {
    ShoppingCategory::factory()->count(3)->create(['user_id' => $this->user->id]);
    ShoppingCategory::factory()->create(['user_id' => User::factory()->create()->id]);

    $response = $this->getJson('/api/shopping-categories');

    $response->assertSuccessful();
    $response->assertJsonCount(3);
});

test('unauthenticated user cannot get shopping categories', function () {
    $this->app['auth']->forgetGuards();
    $response = $this->getJson('/api/shopping-categories');

    $response->assertUnauthorized();
});

// POST /api/shopping-categories
test('can create a shopping category', function () {
    $response = $this->postJson('/api/shopping-categories', [
        'name' => 'Produce',
        'store_section' => 'Aisle 3',
        'color' => '#FF5733',
        'sort_order' => 1,
    ]);

    $response->assertCreated();
    $response->assertJsonFragment(['name' => 'Produce']);
    $this->assertDatabaseHas('shopping_categories', [
        'name' => 'Produce',
        'user_id' => $this->user->id,
    ]);
});

test('create shopping category requires name', function () {
    $response = $this->postJson('/api/shopping-categories', [
        'store_section' => 'Aisle 1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('name');
});

test('create shopping category with only required field', function () {
    $response = $this->postJson('/api/shopping-categories', [
        'name' => 'Dairy',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('shopping_categories', [
        'name' => 'Dairy',
        'user_id' => $this->user->id,
    ]);
});

// PUT /api/shopping-categories/{id}
test('can update a shopping category', function () {
    $category = ShoppingCategory::factory()->create(['user_id' => $this->user->id]);

    $response = $this->putJson("/api/shopping-categories/{$category->id}", [
        'name' => 'Updated Name',
        'color' => '#00FF00',
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('shopping_categories', [
        'id' => $category->id,
        'name' => 'Updated Name',
        'color' => '#00FF00',
    ]);
});

test('user cannot update another user\'s shopping category', function () {
    $otherUser = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->putJson("/api/shopping-categories/{$category->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertForbidden();
});

test('can update partial fields of shopping category', function () {
    $category = ShoppingCategory::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Original',
        'color' => '#FF0000',
    ]);

    $response = $this->putJson("/api/shopping-categories/{$category->id}", [
        'name' => 'New Name',
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('shopping_categories', [
        'id' => $category->id,
        'name' => 'New Name',
        'color' => '#FF0000',
    ]);
});

// DELETE /api/shopping-categories/{id}
test('can delete a shopping category', function () {
    $category = ShoppingCategory::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/shopping-categories/{$category->id}");

    $response->assertSuccessful();
    $this->assertDatabaseMissing('shopping_categories', ['id' => $category->id]);
});

test('user cannot delete another user\'s shopping category', function () {
    $otherUser = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/shopping-categories/{$category->id}");

    $response->assertForbidden();
});
