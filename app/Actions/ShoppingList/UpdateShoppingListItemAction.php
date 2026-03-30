<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'category_id' => ['nullable', Rule::exists('shopping_categories', 'id')->where('user_id', auth()->id())],
            'notes' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'priority' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ];
    }

    public function asController(Request $request, ShoppingList $shoppingList, ShoppingListItem $shoppingListItem): ShoppingListItem
    {
        abort_unless($shoppingListItem->shopping_list_id === $shoppingList->id, 404);

        return $this->handle($request->user(), $shoppingListItem->id, $request->all());
    }
}