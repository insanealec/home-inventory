<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShoppingListItemAction
{
    use AsAction;

    public function handle(User $user, int $id): ShoppingListItem
    {
        return ShoppingListItem::whereHas('shoppingList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('inventoryItem')->findOrFail($id);
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->id);
    }
}