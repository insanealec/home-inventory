<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShoppingListAction
{
    use AsAction;

    public function handle(User $user, int $id, array $data): ShoppingList
    {
        $list = $user->shoppingLists()->findOrFail($id);
        $list->update($data);
        return $list;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean',
            'shopping_date' => 'nullable|date',
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->id, $request->all());
    }
}