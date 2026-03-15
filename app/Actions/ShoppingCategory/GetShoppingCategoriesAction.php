<?php

namespace App\Actions\ShoppingCategory;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShoppingCategoriesAction
{
    use AsAction;

    public function handle(User $user): Collection
    {
        return $user->shoppingCategories()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function asController(Request $request): Collection
    {
        return $this->handle($request->user());
    }
}
