<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShoppingListItemsBulkAction
{
    use AsAction;

    /**
     * Update multiple items in a shopping list at once.
     *
     * @param  array<int, array<string, mixed>>  $updates  Keyed by item id
     * @return array{updated: int[], errors: array<int, string>}
     */
    public function handle(User $user, int $shoppingListId, array $updates): array
    {
        $shoppingList = $user->shoppingLists()->findOrFail($shoppingListId);

        $updated = [];
        $errors = [];

        foreach ($updates as $itemId => $fields) {
            $item = $shoppingList->items()->find($itemId);

            if ($item === null) {
                $errors[$itemId] = "Item {$itemId} not found in this list.";
                continue;
            }

            $item->update($fields);
            $updated[] = $itemId;
        }

        return compact('updated', 'errors');
    }

    public function asController(Request $request, ShoppingList $shoppingList): array
    {
        return $this->handle($request->user(), $shoppingList->id, $request->input('updates', []));
    }
}
