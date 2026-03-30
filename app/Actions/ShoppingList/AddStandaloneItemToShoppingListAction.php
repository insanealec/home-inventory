<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\Concerns\AsAction;

class AddStandaloneItemToShoppingListAction
{
    use AsAction;

    public function handle(User $user, int $shoppingListId, array $data): ShoppingListItem
    {
        $shoppingList = $user->shoppingLists()->findOrFail($shoppingListId);
        
        $data['inventory_item_id'] = null;
        
        $item = $shoppingList->items()->create($data);
        
        return $item;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'category_id' => ['nullable', Rule::exists('shopping_categories', 'id')->where('user_id', auth()->id())],
            'notes' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'priority' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ];
    }

    public function asController(Request $request, ShoppingList $shoppingList): ShoppingListItem
    {
        return $this->handle($request->user(), $shoppingList->id, $request->all());
    }
}