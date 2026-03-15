<?php

use App\Actions\ShoppingCategory\UpdateShoppingCategoryAction;
use App\Models\ShoppingCategory;
use App\Models\User;

test('can update a shopping category', function () {
    $user = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $user->id, 'name' => 'Original']);

    $action = app(UpdateShoppingCategoryAction::class);
    $updated = $action->handle($user, $category->id, [
        'name' => 'Updated Name',
        'store_section' => 'Aisle 5',
    ]);

    expect($updated->name)->toBe('Updated Name');
    expect($updated->store_section)->toBe('Aisle 5');
    $this->assertDatabaseHas('shopping_categories', [
        'id' => $category->id,
        'name' => 'Updated Name',
        'store_section' => 'Aisle 5',
    ]);
});

test('can update partial fields', function () {
    $user = User::factory()->create();
    $category = ShoppingCategory::factory()->create([
        'user_id' => $user->id,
        'name' => 'Category',
        'color' => '#FF0000',
    ]);

    $action = app(UpdateShoppingCategoryAction::class);
    $updated = $action->handle($user, $category->id, [
        'name' => 'New Name',
    ]);

    expect($updated->name)->toBe('New Name');
    expect($updated->color)->toBe('#FF0000'); // Unchanged
});

test('cannot update another user\'s category', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);

    $action = app(UpdateShoppingCategoryAction::class);

    expect(fn () => $action->handle($user, $category->id, ['name' => 'Hacked']))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

test('update validation rules allow partial updates', function () {
    $action = app(UpdateShoppingCategoryAction::class);
    $rules = $action->rules();

    // All fields should be optional for updates
    expect($rules['name'])->not->toContain('required');
    expect($rules['store_section'])->toContain('nullable');
    expect($rules['color'])->toContain('nullable');
    expect($rules['sort_order'])->toContain('nullable');
});
