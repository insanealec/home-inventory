<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShoppingListAction
{
    use AsAction;

    public function handle(User $user, int $id): ShoppingList
    {
        return $user->shoppingLists()
            ->with(['items.inventoryItem', 'items.category'])
            ->findOrFail($id);
    }

    public function asController(Request $request, ShoppingList $shoppingList): ShoppingList
    {
        return $this->handle($request->user(), $shoppingList->id);
    }
}
