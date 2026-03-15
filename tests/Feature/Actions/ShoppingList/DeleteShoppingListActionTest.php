<?php

use App\Actions\ShoppingList\DeleteShoppingListAction;
use App\Models\ShoppingList;
use App\Models\User;

test('can delete shopping list', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $action = app(DeleteShoppingListAction::class);
    $result = $action->handle($user, $list->id);

    expect($result)->toBeTrue();
    
    // Verify the list is actually deleted
    expect(ShoppingList::find($list->id))->toBeNull();
});

test('handles not found gracefully', function () {
    $user = User::factory()->create();

    $action = app(DeleteShoppingListAction::class);
    
    // This should throw ModelNotFoundException, which is proper handling
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    $action->handle($user, 999);
});

test('deletes associated items due to cascade', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    
    // Create some items in the list
    $item1 = $list->items()->create([
        'name' => 'Test Item 1',
        'quantity' => 1,
    ]);
    
    $item2 = $list->items()->create([
        'name' => 'Test Item 2',
        'quantity' => 2,
    ]);

    $action = app(DeleteShoppingListAction::class);
    $result = $action->handle($user, $list->id);

    expect($result)->toBeTrue();
    
    // Verify the list is deleted
    expect(ShoppingList::find($list->id))->toBeNull();
    
    // Verify associated items are also deleted (due to cascade)
    expect($list->items()->find($item1->id))->toBeNull();
    expect($list->items()->find($item2->id))->toBeNull();
});