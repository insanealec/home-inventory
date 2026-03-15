<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class AddMultipleItemsToShoppingListAction
{
    use AsAction;

    /**
     * Add multiple items to a shopping list at once.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{created: ShoppingListItem[], errors: array<int, string[]>}
     */
    public function handle(User $user, int $shoppingListId, array $items): array
    {
        $shoppingList = $user->shoppingLists()->findOrFail($shoppingListId);

        $created = [];
        $errors = [];

        foreach ($items as $index => $data) {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'unit' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'estimated_price' => 'nullable|numeric|min:0',
                'category_id' => 'nullable|exists:shopping_categories,id',
                'priority' => 'nullable|integer|min:0|max:10',
                'sort_order' => 'nullable|integer',
                'inventory_item_id' => 'nullable|exists:inventory_items,id',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
                continue;
            }

            $created[] = $shoppingList->items()->create($data);
        }

        return compact('created', 'errors');
    }

    public function asController(Request $request, ShoppingList $shoppingList): array
    {
        return $this->handle($request->user(), $shoppingList->id, $request->input('items', []));
    }
}
