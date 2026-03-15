<?php

namespace App\Actions\ShoppingCategory;

use App\Models\ShoppingCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateShoppingCategoryAction
{
    use AsAction;

    public function handle(User $user, array $data): ShoppingCategory
    {
        return $user->shoppingCategories()->create($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'store_section' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function asController(Request $request): ShoppingCategory
    {
        return $this->handle($request->user(), $request->all());
    }
}
