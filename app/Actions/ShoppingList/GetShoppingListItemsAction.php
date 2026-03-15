<?php

namespace App\Actions\ShoppingList;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShoppingListItemsAction
{
    use AsAction;

    public function handle(User $user, int $shoppingListId): Collection
    {
        $shoppingList = $user->shoppingLists()->findOrFail($shoppingListId);

        return $shoppingList->items()
            ->with(['inventoryItem', 'category'])
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();
    }

    public function asController(Request $request, int $shoppingListId): Collection
    {
        return $this->handle($request->user(), $shoppingListId);
    }
}
