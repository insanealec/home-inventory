<?php

namespace App\Actions\ShoppingCategory;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteShoppingCategoryAction
{
    use AsAction;

    public function handle(User $user, int $id): bool
    {
        $category = $user->shoppingCategories()->findOrFail($id);
        $category->delete();
        return true;
    }

    public function asController(Request $request, int $id): bool
    {
        return $this->handle($request->user(), $id);
    }
}
