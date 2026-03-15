<?php

use App\Actions\ShoppingCategory\DeleteShoppingCategoryAction;
use App\Models\ShoppingCategory;
use App\Models\User;

test('can delete a shopping category', function () {
    $user = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $user->id]);

    $action = app(DeleteShoppingCategoryAction::class);
    $result = $action->handle($user, $category->id);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('shopping_categories', ['id' => $category->id]);
});

test('cannot delete another user\'s category', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $otherUser->id]);

    $action = app(DeleteShoppingCategoryAction::class);

    expect(fn () => $action->handle($user, $category->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

test('deleting a category returns true', function () {
    $user = User::factory()->create();
    $category = ShoppingCategory::factory()->create(['user_id' => $user->id]);

    $action = app(DeleteShoppingCategoryAction::class);
    $result = $action->handle($user, $category->id);

    expect($result)->toBeTrue();
});
