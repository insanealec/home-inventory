<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteShoppingListAction
{
    use AsAction;

    public function handle(User $user, int $id): bool
    {
        $list = $user->shoppingLists()->findOrFail($id);
        $list->delete();
        return true;
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->id);
    }
}