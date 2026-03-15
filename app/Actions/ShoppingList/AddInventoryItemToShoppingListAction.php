<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'inventory_item_id' => [
                'required',
                Rule::exists('inventory_items', 'id')->where('user_id', auth()->id()),
            ],
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function asController(Request $request, ShoppingList $shoppingList): ShoppingListItem
    {
        return $this->handle(
            $request->user(),
            $shoppingList->id,
            $request->integer('inventory_item_id'),
            $request->integer('quantity'),
        );
    }
}