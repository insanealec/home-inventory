<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShoppingListItemAction
{
    use AsAction;

    public function handle(User $user, int $id, array $data): ShoppingListItem
    {
        $item = ShoppingListItem::whereHas('shoppingList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $item->update($data);

        return $item;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'quantity' => 'integer|min:0',
            'unit' => 'nullable|string|max:50',
            'is_completed' => 'boolean',
            'category_id' => 'nullable|exists:shopping_categories,id',
            'notes' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'priority' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->id, $request->all());
    }
}