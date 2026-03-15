<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateShoppingListItemAction
{
    use AsAction;

    public function handle(User $user, int $shoppingListId, array $data): ShoppingListItem
    {
        $shoppingList = $user->shoppingLists()->findOrFail($shoppingListId);

        return $shoppingList->items()->create($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:shopping_categories,id',
            'priority' => 'nullable|integer|min:0|max:10',
            'sort_order' => 'nullable|integer',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
        ];
    }

    public function asController(Request $request, int $shoppingListId): ShoppingListItem
    {
        return $this->handle($request->user(), $shoppingListId, $request->all());
    }
}
