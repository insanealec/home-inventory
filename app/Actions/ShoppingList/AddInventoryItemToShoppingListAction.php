<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class AddInventoryItemToShoppingListAction
{
    use AsAction;

    public function handle(User $user, int $shoppingListId, int $inventoryItemId, int $quantity): ShoppingListItem
    {
        $shoppingList = $user->shoppingLists()->findOrFail($shoppingListId);
        
        $inventoryItem = $user->inventoryItems()->findOrFail($inventoryItemId);
        
        $item = $shoppingList->items()->create([
            'name' => $inventoryItem->name,
            'quantity' => $quantity,
            'unit' => $inventoryItem->unit,
            'notes' => $inventoryItem->description,
            'inventory_item_id' => $inventoryItemId,
        ]);
        
        return $item;
    }

    public function rules(): array
    {
        return [
            'shopping_list_id' => 'required|exists:shopping_lists,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle(
            $request->user(), 
            $request->shopping_list_id, 
            $request->inventory_item_id, 
            $request->quantity
        );
    }
}