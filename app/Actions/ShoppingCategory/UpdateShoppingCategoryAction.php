<?php

namespace App\Actions\ShoppingCategory;

use App\Models\ShoppingCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShoppingCategoryAction
{
    use AsAction;

    public function handle(User $user, int $id, array $data): ShoppingCategory
    {
        $category = $user->shoppingCategories()->findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'store_section' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function asController(Request $request, ShoppingCategory $shoppingCategory): ShoppingCategory
    {
        return $this->handle($request->user(), $shoppingCategory->id, $request->all());
    }
}
