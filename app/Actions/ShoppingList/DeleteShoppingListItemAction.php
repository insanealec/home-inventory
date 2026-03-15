<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteShoppingListItemAction
{
    use AsAction;

    public function handle(User $user, int $id): bool
    {
        $item = ShoppingListItem::whereHas('shoppingList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $item->delete();

        return true;
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->id);
    }
}