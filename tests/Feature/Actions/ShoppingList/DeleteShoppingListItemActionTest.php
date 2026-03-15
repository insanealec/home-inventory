<?php

use App\Actions\ShoppingList\DeleteShoppingListItemAction;
use App\Models\ShoppingListItem;
use App\Models\ShoppingList;
use App\Models\User;

test('can delete shopping list item', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $item = $list->items()->create([
        'name' => 'Test Item',
        'quantity' => 1,
    ]);

    $action = app(DeleteShoppingListItemAction::class);
    $result = $action->handle($user, $item->id);

    expect($result)->toBeTrue();
    
    // Verify the item is actually deleted
    expect(ShoppingListItem::find($item->id))->toBeNull();
});

test('handles not found gracefully', function () {
    $user = User::factory()->create();

    $action = app(DeleteShoppingListItemAction::class);
    
    // This should throw ModelNotFoundException, which is proper handling
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    $action->handle($user, 999);
});

test('deletes shopping list item successfully', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $item = $list->items()->create([
        'name' => 'Test Item',
        'quantity' => 1,
        'notes' => 'Some notes to verify deletion',
    ]);

    $action = app(DeleteShoppingListItemAction::class);
    $result = $action->handle($user, $item->id);

    expect($result)->toBeTrue();
    
    // Verify the item is deleted
    expect(ShoppingListItem::find($item->id))->toBeNull();
});