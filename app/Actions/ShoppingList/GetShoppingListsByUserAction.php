<?php

namespace App\Actions\ShoppingList;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShoppingListsByUserAction
{
    use AsAction;

    public function handle(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->shoppingLists()
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function asController(Request $request): LengthAwarePaginator
    {
        return $this->handle($request->user());
    }
}
